<?php
/**
 * Matrícula automática de un estudiante al confirmar su correo.
 *
 * Al verificar el correo, este módulo:
 *  1. Mapea el programa elegido a su código técnico.
 *  2. Busca el set más reciente en `sets_cursos` para ese código.
 *  3. Crea (o reutiliza) el usuario en Moodle con username/idnumber = cédula.
 *  4. Matricula al estudiante en la tripleta (técnico + inglés + habilidades).
 *  5. Registra la matrícula en la tabla `enrollments`.
 *  6. Envía un correo con las credenciales al correo personal.
 */

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';
require_once dirname(__DIR__, 2) . '/vendor/phpmailer/phpmailer/src/PHPMailer.php';
require_once dirname(__DIR__, 2) . '/vendor/phpmailer/phpmailer/src/SMTP.php';
require_once dirname(__DIR__, 2) . '/vendor/phpmailer/phpmailer/src/Exception.php';
require_once dirname(__DIR__, 2) . '/controller/moodle_api.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// URL genérica de ingreso a la plataforma (placeholder).
const MOODLE_LOGIN_URL = 'https://campus.cenditech.com.co';

// Contraseña inicial de los usuarios Moodle.
const PASSWORD_INICIAL = 'Cendi@2026';

// ID del registro smtpConfig a usar para el envío del correo.
const SMTP_CONFIG_ID = 4;

/**
 * Mapea el valor de `program` (user_register) a su código técnico.
 */
function codigoTecnicoDePrograma($program) {
    $mapa = [
        'ANALISIS DE DATOS'           => 'DT',
        'CIBERSEGURIDAD'              => 'CIBER',
        'INTELIGENCIA ARTIFICIAL'     => 'IA',
        'PROGRAMACION'                => 'PDS',
        'BLOCKCHAIN'                  => 'BLO',
        'COMPUTRACION EN LA NUBE'     => 'CN',
        'ROBOTICA Y AUTOMATIZACION'   => 'RA',
        'INTERNET DE LAS COSAS - IOT' => 'IOT',
    ];
    $key = normalizarMoodle($program);
    return $mapa[$key] ?? null;
}

/**
 * Nombre legible del área técnica a partir de su código.
 */
function nombreTecnicoDeCodigo($codigo) {
    $nombres = [
        'IA'    => 'Inteligencia Artificial',
        'PDS'   => 'Programación y Desarrollo',
        'DT'    => 'Análisis de Datos',
        'CIBER' => 'Ciberseguridad',
        'CN'    => 'Computación en la Nube',
        'BLO'   => 'Blockchain',
        'RA'    => 'Robótica y Automatización',
        'IOT'   => 'Internet de las Cosas',
    ];
    return $nombres[$codigo] ?? $codigo;
}

/**
 * Genera el correo institucional:
 * {iniciales de nombres}{últimos 4 de cédula}{iniciales de apellidos}@cenditech.com.co
 */
function generarCorreoInstitucional($first_name, $second_name, $number_id, $first_last, $second_last) {
    $ini1  = strtolower(substr(trim($first_name), 0, 1));
    $ini2  = strtolower(substr(trim($second_name), 0, 1));
    $last4 = substr((string)$number_id, -4);
    $iniA1 = strtolower(substr(trim($first_last), 0, 1));
    $iniA2 = strtolower(substr(trim($second_last), 0, 1));
    return $ini1 . $ini2 . $last4 . $iniA1 . $iniA2 . '@cenditech.com.co';
}

/**
 * Envía el correo de credenciales al correo personal del estudiante.
 */
function enviarCorreoMatricula($conn, $destino, $nombre, $programa, $username, $correoInstitucional, $password) {
    $querySMTP = mysqli_query($conn, "SELECT * FROM smtpConfig WHERE id = " . (int)SMTP_CONFIG_ID);
    if (!$querySMTP || !($smtp = mysqli_fetch_assoc($querySMTP))) {
        return ['ok' => false, 'mensaje' => 'No se encontró la configuración SMTP.'];
    }

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = $smtp['host'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $smtp['email'];
        $mail->Password   = $smtp['password'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = (int)$smtp['port'];

        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer'       => false,
                'verify_peer_name'  => false,
                'allow_self_signed' => true,
            ],
        ];

        $mail->setFrom('no-reply@cenditech.com.co', 'CENDI Tech');
        $mail->CharSet = 'UTF-8';
        $mail->addAddress($destino, $nombre);
        $mail->isHTML(true);
        $mail->Subject = '¡Matrícula exitosa en ' . $programa . '!';

        $mail->Body = '
        <!DOCTYPE html>
        <html>
        <head><meta charset="UTF-8"></head>
        <body style="font-family:Arial,Helvetica,sans-serif;background:#f4f4f9;margin:0;padding:20px;color:#333;">
            <div style="max-width:600px;margin:0 auto;background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.08);">
                <div style="background:#181E93;color:#ffffff;padding:28px 24px;text-align:center;">
                    <img src="cid:logo_blanco" alt="CENDITECH" width="150" height="100" style="display:block;margin:0 auto;width:150px;height:100px;">
                    <h1 style="margin:16px 0 0;font-size:20px;">¡Estás matriculado en ' . htmlspecialchars($programa) . '!</h1>
                </div>
                <div style="padding:28px 24px;line-height:1.6;">
                    <p>Hola <b>' . htmlspecialchars($nombre) . '</b>,</p>
                    <p>Tu cuenta en la plataforma ha sido creada exitosamente. Estos son tus datos de acceso:</p>
                    <div style="background:#f8f9ff;border:2px solid #181E93;border-radius:10px;padding:18px;margin:18px 0;">
                        <p style="margin:8px 0;"><b>Usuario:</b> ' . htmlspecialchars($username) . '</p>
                        <p style="margin:8px 0;"><b>Correo institucional:</b> ' . htmlspecialchars($correoInstitucional) . '</p>
                        <p style="margin:8px 0;"><b>Contraseña inicial:</b> ' . htmlspecialchars($password) . '</p>
                    </div>
                    <p style="margin:6px 0;">Por seguridad, al ingresar por primera vez el sistema te pedirá cambiar la contraseña.</p>
                    <div style="text-align:center;margin:26px 0;">
                        <a href="' . MOODLE_LOGIN_URL . '" style="display:inline-block;background:#181E93;color:#F9B233;text-decoration:none;font-weight:bold;padding:14px 28px;border-radius:8px;">Ingresar a la plataforma</a>
                    </div>
                    <p>Si tienes dudas, contáctanos. ¡Nos vemos pronto futuro campista! 🚀</p>
                </div>
                <div style="text-align:center;padding:20px 24px;color:#777;font-size:12px;background:#f4f4f9;">
                    <img src="cid:logo" alt="CENDITECH" width="180" height="80" style="display:block;margin:0 auto 8px;width:180px;height:80px;">
                    <p style="margin:0;">Equipo CENDI Tech</p>
                </div>
            </div>
        </body>
        </html>';

        $mail->addEmbeddedImage(dirname(__DIR__, 2) . '/img/cendi_tech_blanco.png', 'logo_blanco');
        $mail->addEmbeddedImage(dirname(__DIR__, 2) . '/img/cendi_tech_logo_recortado.png', 'logo');

        $mail->send();
        return ['ok' => true];
    } catch (Exception $e) {
        return ['ok' => false, 'mensaje' => $mail->ErrorInfo];
    }
}

/**
 * Realiza la matrícula automática del estudiante.
 *
 * @param mysqli $conn
 * @param string $numberId Cédula del estudiante
 * @return array ['ok' => bool, 'mensaje' => string, 'detalle' => mixed]
 */
function matricularEstudiante($conn, $numberId) {
    // 1. Obtener datos del estudiante
    $stmt = $conn->prepare("SELECT typeID, number_id, first_name, second_name, first_last, second_last, email, program
                            FROM user_register WHERE number_id = ? LIMIT 1");
    $stmt->bind_param('s', $numberId);
    $stmt->execute();
    $est = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$est) {
        return ['ok' => false, 'mensaje' => 'No se encontró el estudiante.'];
    }

    // 2. Mapear programa -> código técnico
    $codigo = codigoTecnicoDePrograma($est['program']);
    if (!$codigo) {
        return ['ok' => false, 'mensaje' => 'El programa "' . $est['program'] . '" no está asociado a un área técnica.'];
    }

    // 3. Buscar el set más reciente para ese código
    $stmt = $conn->prepare("SELECT id, serie, curso_tecnico_id, curso_ingles_id, curso_habilidades_id
                            FROM sets_cursos WHERE codigo_tecnico = ? ORDER BY serie DESC LIMIT 1");
    $stmt->bind_param('s', $codigo);
    $stmt->execute();
    $set = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$set) {
        return [
            'ok'        => false,
            'pendiente' => true,
            'mensaje'   => 'Aún no hay un set de cursos disponible para ' . $codigo . '. La matrícula queda pendiente.',
        ];
    }

    // 4. Datos para Moodle
    $username = (string)$est['number_id'];
    $correoInstitucional = generarCorreoInstitucional(
        $est['first_name'],
        $est['second_name'],
        $est['number_id'],
        $est['first_last'],
        $est['second_last']
    );
    $password  = PASSWORD_INICIAL;
    $firstname = normalizarMoodle(trim($est['first_name'] . ' ' . $est['second_name']));
    $lastname  = normalizarMoodle(trim($est['first_last'] . ' ' . $est['second_last']));
    $fullName  = trim(implode(' ', array_filter([
        $est['first_name'], $est['second_name'], $est['first_last'], $est['second_last'],
    ])));
    $programName = nombreTecnicoDeCodigo($codigo);

    // 5. Crear o reutilizar el usuario en Moodle
    $usuario = getUsuarioMoodlePorUsername($username);
    if ($usuario && isset($usuario['id'])) {
        $moodleUserId = $usuario['id'];
    } else {
        $creado = crearUsuarioMoodle([
            'username'  => $username,
            'idnumber'  => $username,
            'password'  => $password,
            'firstname' => $firstname,
            'lastname'  => $lastname,
            'email'     => $correoInstitucional,
        ]);

        if (!isset($creado[0]['id'])) {
            $error = $creado[0]['message'] ?? ($creado['exception']['message'] ?? 'Error desconocido');
            return ['ok' => false, 'mensaje' => 'No se pudo crear el usuario en Moodle: ' . $error, 'detalle' => $creado];
        }
        $moodleUserId = $creado[0]['id'];
    }

    // 6. Matricular en la tripleta
    $cursos = [
        'tecnico'     => $set['curso_tecnico_id'],
        'ingles'      => $set['curso_ingles_id'],
        'habilidades' => $set['curso_habilidades_id'],
    ];
    $errores = [];
    foreach ($cursos as $clave => $courseId) {
        $r = matricularUsuarioCurso($moodleUserId, $courseId);
        if (isset($r['exception']) || isset($r['error'])) {
            $msg = $r['message'] ?? $r['error'] ?? 'error';
            // "ya matriculado" no es un error real
            if (stripos($msg, 'already') === false) {
                $errores[] = $clave . ': ' . $msg;
            }
        }
    }

    // 7. Registrar en enrollments (idempotente)
    $stmt = $conn->prepare("INSERT INTO enrollments
        (type_id, number_id, full_name, email, institutional_email, username, password, program, program_name, moodle_user_id, set_id, course_tecnico_id, course_ingles_id, course_habilidades_id, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'enrolled')
        ON DUPLICATE KEY UPDATE status = 'enrolled'");
    $stmt->bind_param(
        'sssssssssiiiii',
        $est['typeID'],
        $est['number_id'],
        $fullName,
        $est['email'],
        $correoInstitucional,
        $username,
        $password,
        $codigo,
        $programName,
        $moodleUserId,
        $set['id'],
        $set['curso_tecnico_id'],
        $set['curso_ingles_id'],
        $set['curso_habilidades_id']
    );
    $stmt->execute();
    $stmt->close();

    // 8. Enviar correo de credenciales al correo personal
    $correo = enviarCorreoMatricula($conn, $est['email'], $fullName, $programName, $username, $correoInstitucional, $password);

    $resultado = [
        'ok'                   => true,
        'mensaje'              => 'Matrícula realizada en ' . $programName . ' (set serie ' . $set['serie'] . ').',
        'moodle_user_id'       => $moodleUserId,
        'correo_institucional' => $correoInstitucional,
        'correo_enviado'       => $correo['ok'],
    ];

    if (!empty($errores)) {
        $resultado['advertencias'] = $errores;
    }
    if (!$correo['ok']) {
        $resultado['advertencias'][] = 'No se pudo enviar el correo: ' . $correo['mensaje'];
    }

    return $resultado;
}
