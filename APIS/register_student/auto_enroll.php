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

// ==========================================================
// CONFIGURACIÓN: cursos técnicos para crear sets por cupo
// ==========================================================
const CATEGORIAS_TECNICAS_SET = [
    'IA'    => ['categoryid' => 16, 'nombre' => 'Inteligencia Artificial',      'template_courseid' => 32],
    'PDS'   => ['categoryid' => 17, 'nombre' => 'Programación y Desarrollo',    'template_courseid' => 31],
    'DT'    => ['categoryid' => 18, 'nombre' => 'Análisis de Datos',            'template_courseid' => 37],
    'CIBER' => ['categoryid' => 19, 'nombre' => 'Ciberseguridad',               'template_courseid' => 36],
    'CN'    => ['categoryid' => 20, 'nombre' => 'Computación en la Nube',       'template_courseid' => 38],
    'BLO'   => ['categoryid' => 21, 'nombre' => 'Blockchain',                   'template_courseid' => 34],
    'RA'    => ['categoryid' => 22, 'nombre' => 'Robótica y Automatización',    'template_courseid' => 35],
    'IOT'   => ['categoryid' => 23, 'nombre' => 'Internet de las Cosas',        'template_courseid' => 33],
];

const CATEGORIAS_OBLIGATORIAS_SET = [
    'ING' => ['categoryid' => 14, 'nombre' => 'Inglés',              'template_courseid' => 28],
    'BH'  => ['categoryid' => 15, 'nombre' => 'Habilidades Blandas', 'template_courseid' => 29],
];

// Tope máximo de estudiantes por curso técnico.
const CUPO_MAXIMO = 100;

// Al llegar a este número de matriculados se crea el siguiente set en fila.
const CUPO_CREAR_SIGUIENTE = 75;

/**
 * Obtiene los cursos de una categoría en Moodle.
 */
function getCursosCategoriaM($categoryId) {
    $r = callMoodleAPIB('core_course_get_courses_by_field', [
        'field' => 'category',
        'value' => $categoryId,
    ]);
    return $r['courses'] ?? [];
}

/**
 * Calcula el siguiente número de serie para un código en una categoría.
 */
function siguienteSerieM($categoryId, $codigo) {
    $cursos = getCursosCategoriaM($categoryId);
    $max = 0;
    foreach ($cursos as $c) {
        if (preg_match('/^' . preg_quote($codigo, '/') . '-(\d+)$/', $c['shortname'], $m)) {
            $max = max($max, (int)$m[1]);
        }
    }
    return $max + 1;
}

/**
 * Duplica un curso plantilla en Moodle.
 */
function duplicarCursoM($templateId, $fullname, $shortname, $categoryId) {
    return callMoodleAPIB('core_course_duplicate_course', [
        'courseid'   => $templateId,
        'fullname'   => $fullname,
        'shortname'  => $shortname,
        'categoryid' => $categoryId,
        'visible'    => 1,
        'options'    => [
            ['name' => 'activities',       'value' => 1],
            ['name' => 'blocks',           'value' => 1],
            ['name' => 'filters',          'value' => 1],
            ['name' => 'users',            'value' => 0],
            ['name' => 'role_assignments', 'value' => 0],
            ['name' => 'comments',         'value' => 0],
            ['name' => 'userscompletion',  'value' => 0],
            ['name' => 'logs',             'value' => 0],
            ['name' => 'grade_histories',  'value' => 0],
        ],
    ]);
}

/**
 * Registra un curso individual en la tabla `cursos`.
 */
function registrarCursoM($conn, $courseId, $code, $name) {
    $stmt = $conn->prepare("INSERT INTO cursos (course_id, course_code, course_name) VALUES (?, ?, ?)");
    $stmt->bind_param('iss', $courseId, $code, $name);
    $ok = $stmt->execute();
    $stmt->close();
    return $ok;
}

/**
 * Registra la tripleta en la tabla `sets_cursos`.
 */
function registrarSetM($conn, $codigo, $serie, $tecnicoId, $inglesId, $blandasId) {
    $stmt = $conn->prepare("INSERT INTO sets_cursos (codigo_tecnico, serie, curso_tecnico_id, curso_ingles_id, curso_habilidades_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('siiii', $codigo, $serie, $tecnicoId, $inglesId, $blandasId);
    $ok = $stmt->execute();
    $stmt->close();
    return $ok;
}

/**
 * Crea un set completo (técnico + inglés + habilidades) para un código.
 * Devuelve el set con su `id`, `serie` y los ids de los 3 cursos, o null si falla.
 */
function crearSetCursoTecnicoM($conn, $codigo) {
    $tecnicos = CATEGORIAS_TECNICAS_SET;
    $oblig    = CATEGORIAS_OBLIGATORIAS_SET;

    if (!isset($tecnicos[$codigo])) {
        return null;
    }

    $tec = $tecnicos[$codigo];
    $ing = $oblig['ING'];
    $bh  = $oblig['BH'];

    $serie   = siguienteSerieM($tec['categoryid'], $codigo);
    $shortT  = "{$codigo}-{$serie}";
    $shortI  = "ING-{$codigo}-{$serie}";
    $shortB  = "BH-{$codigo}-{$serie}";
    $fullT   = "{$tec['nombre']} {$shortT}";
    $fullI   = "{$ing['nombre']} {$shortI}";
    $fullB   = "{$bh['nombre']} {$shortB}";

    $pasos = [
        [$tec['template_courseid'], $fullT, $shortT, $tec['categoryid']],
        [$ing['template_courseid'], $fullI, $shortI, $ing['categoryid']],
        [$bh['template_courseid'],  $fullB, $shortB,  $bh['categoryid']],
    ];

    $ids = [];
    foreach ($pasos as [$tid, $fn, $sn, $cid]) {
        $r = duplicarCursoM($tid, $fn, $sn, $cid);
        if (isset($r['error']) || isset($r['exception']) || !isset($r['id'])) {
            return null;
        }
        $ids[] = $r['id'];
        registrarCursoM($conn, $r['id'], $sn, $fn);
    }

    if (!registrarSetM($conn, $codigo, $serie, $ids[0], $ids[1], $ids[2])) {
        return null;
    }

    return [
        'id'                    => $conn->insert_id,
        'serie'                 => $serie,
        'curso_tecnico_id'      => $ids[0],
        'curso_ingles_id'       => $ids[1],
        'curso_habilidades_id'  => $ids[2],
    ];
}

/**
 * Cuenta los estudiantes matriculados en un curso técnico.
 */
function contarMatriculadosCursoM($conn, $courseTecnicoId) {
    $stmt = $conn->prepare("SELECT COUNT(*) AS c FROM enrollments WHERE course_tecnico_id = ?");
    $stmt->bind_param('i', $courseTecnicoId);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return (int)($row['c'] ?? 0);
}

/**
 * Obtiene el set con cupo disponible para un código.
 * Recorre los sets en orden de serie (más antiguo primero) y devuelve el
 * primero con menos de CUPO_MAXIMO matriculados. Si todos están llenos (o no
 * hay ninguno), crea un nuevo set y lo devuelve.
 */
function obtenerSetConCupoM($conn, $codigo) {
    $stmt = $conn->prepare("SELECT id, serie, curso_tecnico_id, curso_ingles_id, curso_habilidades_id
                            FROM sets_cursos WHERE codigo_tecnico = ? ORDER BY serie ASC");
    $stmt->bind_param('s', $codigo);
    $stmt->execute();
    $sets = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    foreach ($sets as $set) {
        if (contarMatriculadosCursoM($conn, $set['curso_tecnico_id']) < CUPO_MAXIMO) {
            return [$set, null];
        }
    }

    // Todos llenos o no hay sets: crear uno nuevo
    $nuevo = crearSetCursoTecnicoM($conn, $codigo);
    if (!$nuevo) {
        return [null, 'No se pudo crear un nuevo set de cursos para ' . $codigo . '.'];
    }
    return [$nuevo, null];
}

/**
 * Adquiere un bloqueo con nombre (GET_LOCK) para serializar la decisión de
 * cupo por código técnico y evitar condiciones de carrera.
 */
function adquirirLockMatricula($conn, $codigo, $timeout = 15) {
    $lockName = 'matricula_' . $codigo;
    $stmt = $conn->prepare("SELECT GET_LOCK(?, ?)");
    $stmt->bind_param('si', $lockName, $timeout);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_row();
    $stmt->close();
    return ($row !== null && (int)$row[0] === 1);
}

/**
 * Libera el bloqueo con nombre previamente adquirido.
 */
function liberarLockMatricula($conn, $codigo) {
    $lockName = 'matricula_' . $codigo;
    $stmt = $conn->prepare("SELECT RELEASE_LOCK(?)");
    $stmt->bind_param('s', $lockName);
    $stmt->execute();
    $stmt->close();
}

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
 * Envía el correo de credenciales al correo personal del estudiante.
 */
function enviarCorreoMatricula($conn, $destino, $nombre, $programa, $username, $password) {
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
                    <img src="cid:logo_blanco" alt="CENDITECH" width="180" height="105" style="display:block;margin:0 auto;width:180px;height:105px;">
                    <h1 style="margin:16px 0 0;font-size:20px;">¡Estás matriculado en ' . htmlspecialchars($programa) . '!</h1>
                </div>
                <div style="padding:28px 24px;line-height:1.6;">
                    <p>Hola <b>' . htmlspecialchars($nombre) . '</b>,</p>
                    <p>Tu cuenta en la plataforma ha sido creada exitosamente. Estos son tus datos de acceso:</p>
                    <div style="background:#f8f9ff;border:2px solid #181E93;border-radius:10px;padding:18px;margin:18px 0;">
                        <p style="margin:8px 0;"><b>Usuario:</b> ' . htmlspecialchars($username) . '</p>
                        <p style="margin:8px 0;"><b>Contraseña inicial:</b> ' . htmlspecialchars($password) . '</p>
                    </div>
                    <p style="margin:6px 0;">Por seguridad, al ingresar por primera vez el sistema te pedirá cambiar la contraseña.</p>
                    <div style="text-align:center;margin:26px 0;">
                        <a href="' . MOODLE_LOGIN_URL . '" style="display:inline-block;background:#181E93;color:#F9B233;text-decoration:none;font-weight:bold;padding:14px 28px;border-radius:8px;">Ingresar a la plataforma</a>
                    </div>
                    <p>Si tienes dudas, contáctanos. ¡Nos vemos pronto futuro campista! 🚀</p>
                </div>
                <div style="text-align:center;padding:20px 24px;color:#777;font-size:12px;background:#f4f4f9;">
                    <img src="cid:logo" alt="CENDITECH" width="150" height="88" style="display:block;margin:0 auto 8px;width:150px;height:88px;">
                    <p style="margin:0;">Equipo CENDI Tech</p>
                </div>
            </div>
        </body>
        </html>';

        $mail->addEmbeddedImage(dirname(__DIR__, 2) . '/img/cendi_logo_blanco.png', 'logo_blanco');
        $mail->addEmbeddedImage(dirname(__DIR__, 2) . '/img/cendi_logo_color.png', 'logo');

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

    // 3. Bloquear la decisión de cupo para este código (evita condiciones de carrera)
    if (!adquirirLockMatricula($conn, $codigo)) {
        return ['ok' => false, 'mensaje' => 'No se pudo procesar la matrícula en este momento. Inténtalo de nuevo en unos segundos.'];
    }

    try {
        // 3.1. Obtener el set con cupo disponible (creando uno nuevo si está lleno)
        [$set, $errorSet] = obtenerSetConCupoM($conn, $codigo);
        if (!$set) {
            return [
                'ok'        => false,
                'pendiente' => true,
                'mensaje'   => $errorSet ?: 'No hay un set de cursos disponible para ' . $codigo . '.',
            ];
        }

    // 4. Datos para Moodle
    $username = (string)$est['number_id'];
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
            'email'     => $est['email'],
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
        $est['email'],
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

    // 7.5. Si este set alcanzó el umbral, crear el siguiente set en fila
    if (contarMatriculadosCursoM($conn, $set['curso_tecnico_id']) >= CUPO_CREAR_SIGUIENTE) {
        $stmt = $conn->prepare("SELECT id FROM sets_cursos WHERE codigo_tecnico = ? AND serie > ? LIMIT 1");
        $stmt->bind_param('si', $codigo, $set['serie']);
        $stmt->execute();
        $existeSiguiente = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if (!$existeSiguiente) {
            crearSetCursoTecnicoM($conn, $codigo);
        }
    }
    } finally {
        liberarLockMatricula($conn, $codigo);
    }

    // 8. Enviar correo de credenciales al correo personal
    $correo = enviarCorreoMatricula($conn, $est['email'], $fullName, $programName, $username, $password);

    $resultado = [
        'ok'             => true,
        'mensaje'        => 'Matrícula realizada en ' . $programName . ' (set serie ' . $set['serie'] . ').',
        'moodle_user_id' => $moodleUserId,
        'correo_enviado' => $correo['ok'],
    ];

    if (!empty($errores)) {
        $resultado['advertencias'] = $errores;
    }
    if (!$correo['ok']) {
        $resultado['advertencias'][] = 'No se pudo enviar el correo: ' . $correo['mensaje'];
    }

    return $resultado;
}
