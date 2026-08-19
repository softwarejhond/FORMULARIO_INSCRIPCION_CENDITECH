<?php
// --------------------------------------------------
// Sección de Procesamiento PHP
// --------------------------------------------------

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluir la conexión a la base de datos
include 'conexion.php';

// Incluir PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// Establecer el encabezado Content-Type antes de cualquier otra salida
header('Content-Type: application/json');

try {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Obtener los datos del formulario
        $tipo = $_POST["tipo"];
        $asunto = $_POST["asunto"];
        $descripcion = $_POST["descripcion"];
        $fecha_registro = $_POST["fecha_registro"];
        $nombre = isset($_POST["nombre"]) ? $_POST["nombre"] : '';
        $cedula = isset($_POST["cedula"]) ? $_POST["cedula"] : '';
        $email = isset($_POST["email"]) ? $_POST["email"] : '';
        $telefono1 = $_POST["telefono1"];
        $telefono2 = isset($_POST["telefono2"]) ? $_POST["telefono2"] : '';

        // Validaciones (ejemplo básico)
        if (empty($tipo) || empty($asunto) || empty($descripcion) || empty($fecha_registro) || empty($nombre) || empty($cedula) || empty($email) || empty($telefono1)) {
            $response = ['success' => false, 'message' => 'Por favor, complete todos los campos obligatorios.'];
            echo json_encode($response);
            exit();
        }

        // Verificar duplicados basado en múltiples criterios
        $sqlDuplicado = "SELECT id FROM pqr WHERE 
                        (cedula = ? AND email = ? AND asunto = ? AND descripcion = ?) OR
                        (cedula = ? AND telefono1 = ? AND asunto = ? AND DATE(fecha_creacion) = CURDATE()) OR
                        (email = ? AND asunto = ? AND descripcion = ? AND DATE(fecha_creacion) = CURDATE())
                        LIMIT 1";
        
        $stmtDuplicado = mysqli_prepare($conn, $sqlDuplicado);
        
        if ($stmtDuplicado) {
            mysqli_stmt_bind_param($stmtDuplicado, "ssssssssss", 
                $cedula, $email, $asunto, $descripcion,  // Primera condición
                $cedula, $telefono1, $asunto,            // Segunda condición
                $email, $asunto, $descripcion            // Tercera condición
            );
            
            mysqli_stmt_execute($stmtDuplicado);
            $resultDuplicado = mysqli_stmt_get_result($stmtDuplicado);
            
            if (mysqli_num_rows($resultDuplicado) > 0) {
                $response = [
                    'success' => false, 
                    'message' => 'Ya existe una PQRS similar registrada. Por favor verifique si ya ha enviado esta solicitud anteriormente.'
                ];
                echo json_encode($response);
                mysqli_stmt_close($stmtDuplicado);
                exit();
            }
            
            mysqli_stmt_close($stmtDuplicado);
        }

        // Verificar si ya existe un PQRS con el mismo número de cédula en los últimos 5 minutos
        $sqlTiempo = "SELECT id FROM pqr WHERE cedula = ? AND fecha_creacion >= DATE_SUB(NOW(), INTERVAL 5 MINUTE) LIMIT 1";
        $stmtTiempo = mysqli_prepare($conn, $sqlTiempo);
        
        if ($stmtTiempo) {
            mysqli_stmt_bind_param($stmtTiempo, "s", $cedula);
            mysqli_stmt_execute($stmtTiempo);
            $resultTiempo = mysqli_stmt_get_result($stmtTiempo);
            
            if (mysqli_num_rows($resultTiempo) > 0) {
                $response = [
                    'success' => false, 
                    'message' => 'Debe esperar al menos 5 minutos antes de enviar otra PQRS con la misma cédula.'
                ];
                echo json_encode($response);
                mysqli_stmt_close($stmtTiempo);
                exit();
            }
            
            mysqli_stmt_close($stmtTiempo);
        }

        // Generar número de radicado único
        do {
            $fecha_actual = date("Ymd");
            $numero_aleatorio = rand(1000, 9999);
            $numero_radicado = "PQR-" . $fecha_actual . "-" . $numero_aleatorio;
            
            // Verificar que el número de radicado no exista
            $sqlRadicado = "SELECT id FROM pqr WHERE numero_radicado = ? LIMIT 1";
            $stmtRadicado = mysqli_prepare($conn, $sqlRadicado);
            mysqli_stmt_bind_param($stmtRadicado, "s", $numero_radicado);
            mysqli_stmt_execute($stmtRadicado);
            $resultRadicado = mysqli_stmt_get_result($stmtRadicado);
            $existe = mysqli_num_rows($resultRadicado) > 0;
            mysqli_stmt_close($stmtRadicado);
        } while ($existe);

        // Insertar en la base de datos con transacción
        mysqli_autocommit($conn, false);

        $sql = "INSERT INTO pqr (tipo, asunto, descripcion, fecha_registro, nombre, cedula, email, telefono1, telefono2, numero_radicado, fecha_creacion, estado, respuesta)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), 1, 'Esperando respuesta')";
        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ssssssssss", $tipo, $asunto, $descripcion, $fecha_registro, $nombre, $cedula, $email, $telefono1, $telefono2, $numero_radicado);

            if (mysqli_stmt_execute($stmt)) {
                // Confirmar la transacción
                mysqli_commit($conn);
                
                // Obtener configuración SMTP desde la base de datos
                $query = "SELECT * FROM smtpConfig WHERE id=2";
                $querySMTP = mysqli_query($conn, $query);
                $smtpConfig = mysqli_fetch_array($querySMTP);

                if (!$smtpConfig) {
                    $response = ['success' => false, 'message' => 'Error al obtener la configuración SMTP.'];
                    echo json_encode($response);
                    exit();
                }

                $host = $smtpConfig['host'];
                $emailSmtp = $smtpConfig['email'];
                $password = $smtpConfig['password'];
                $port = $smtpConfig['port'];
                $subject = "🔔 Notificación: Su PQRS ha sido ingresado exitosamente";

                // Enviar correo electrónico de confirmación
                $mail = new PHPMailer(true);

                try {
                    // Configuración del servidor SMTP
                    $mail->isSMTP();
                    $mail->Host = $host;
                    $mail->SMTPAuth = true;
                    $mail->Username = $emailSmtp;
                    $mail->Password = $password;
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                    $mail->Port = $port;
                    $mail->setFrom($emailSmtp, 'Servicio al cliente');
                    $mail->CharSet = 'UTF-8';
                    $mail->addAddress($email);

                    // Incrustar imágenes
                    $mail->addEmbeddedImage('assets/img/encabezado.jpg', 'encabezado');
                    //$mail->addEmbeddedImage('assets/img/contenido.png', 'contenido');
                    $mail->addEmbeddedImage('assets/img/pausa_pqr.png', 'pausa_navidad');

                    $mail->isHTML(true);
                    $mail->Subject = $subject;

                    // Aquí va tu mensaje HTML (mantener el mismo)
                    $mensaje = "
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <style>
                            body {
                                font-family: Arial, sans-serif;
                                margin: 0;
                                padding: 0;
                                background-color: #f4f4f9;
                                color: #333;
                            }
                            .container {
                                max-width: 600px;
                                margin: 20px auto;
                                background: #ffffff;
                                border-radius: 10px;
                                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
                                padding: 20px;
                            }
                            .header {
                                text-align: center;
                                color: #fff;
                                border-top-left-radius: 10px;
                                border-top-right-radius: 10px;
                            }
                
                            .header h1 {
                                margin: 0;
                                font-size: 24px;
                            }
                
                            .content {
                                line-height: 1.6;
                            }
                
                            .content p {
                                margin: 10px 0;
                            }
                
                            .footer {
                                text-align: center;
                                margin-top: 20px;
                                color: #777;
                                font-size: 12px;
                            }
                        </style>
                    </head>
                
                    <body>
                        <div class='container'>
                            <div class='header'>
                                <img src='cid:encabezado' alt='Encabezado' style='max-width: 100%; height: auto;'>
                            </div>
                            <div class='content' style='text-align: center;'>
                                <h2>Hola <b>" . strtoupper($nombre) . "</b>,</h2>
                                <p>Tu PQRS ha sido recibida con éxito. El número de radicado es:</p>
                                <h3 style='color: #066aab;'>$numero_radicado</h3>
                            </div>
                            <div style='text-align: center; margin: 20px 0;'>
                                <img src='cid:pausa_navidad' alt='Tarjeta de Navidad' style='max-width: 100%; height: auto; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);'>
                            </div>
                            <div class='footer'>
                                <p style='font-size: 14px; color: #555; font-style: italic; text-align: center; margin-top: 10px;'>
                                    <strong>Nota:</strong> Este es un correo automático, por favor no responda.
                                </p>
                            </div>
                        </div>
                    </body>
                
                    </html>";

                    $mail->Body = $mensaje;

                    if ($mail->send()) {
                        $response = [
                            'success' => true,
                            'message' => "PQRS enviado correctamente. Número de radicado: " . htmlspecialchars($numero_radicado),
                            'data' => ['numero_radicado' => $numero_radicado]
                        ];
                    } else {
                        $response = ['success' => false, 'message' => "El mensaje no pudo ser enviado. Error de Mailer: {$mail->ErrorInfo}"];
                    }
                    echo json_encode($response);
                    exit();
                } catch (Exception $e) {
                    $response = ['success' => false, 'message' => "El mensaje no pudo ser enviado. Error de Mailer: {$mail->ErrorInfo}"];
                    echo json_encode($response);
                    exit();
                }
            } else {
                // Revertir la transacción en caso de error
                mysqli_rollback($conn);
                $response = ['success' => false, 'message' => "Error al registrar el PQRS: " . mysqli_stmt_error($stmt)];
                echo json_encode($response);
                exit();
            }

            mysqli_stmt_close($stmt);
        } else {
            mysqli_rollback($conn);
            $response = ['success' => false, 'message' => "Error al preparar la consulta: " . mysqli_error($conn)];
            echo json_encode($response);
            exit();
        }
    } else {
        $response = ['success' => false, 'message' => 'Método de solicitud no permitido.'];
        echo json_encode($response);
        exit();
    }
} catch (Exception $e) {
    // Revertir la transacción en caso de excepción
    if (isset($conn)) {
        mysqli_rollback($conn);
    }
    $response = ['success' => false, 'message' => "Ocurrió un error inesperado: " . $e->getMessage()];
    echo json_encode($response);
    exit();
} finally {
    if (isset($conn)) {
        mysqli_autocommit($conn, true);
        mysqli_close($conn);
    }
}
?>