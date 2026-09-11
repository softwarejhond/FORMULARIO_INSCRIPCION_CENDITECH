<?php
/**
 * Plantillas de correo reutilizables.
 *
 * centraliza el envío de los correos transaccionales para poder
 * reutilizarlos tanto en el flujo real como en pruebas.
 */

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';
require_once dirname(__DIR__, 2) . '/vendor/phpmailer/phpmailer/src/PHPMailer.php';
require_once dirname(__DIR__, 2) . '/vendor/phpmailer/phpmailer/src/SMTP.php';
require_once dirname(__DIR__, 2) . '/vendor/phpmailer/phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Envía el correo de bienvenida / verificación al aspirante.
 */
function enviarCorreoBienvenida($conn, $destino, $nombre, $program, $verificationUrl) {
    $querySMTP = mysqli_query($conn, "SELECT * FROM smtpConfig WHERE id = 4");
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
        $mail->addAddress($destino);
        $mail->isHTML(true);
        $mail->Subject = '¡Bienvenido al Bootcamp de ' . $program . ' de CENDI Tech!';

        $mail->Body = "
                            <!DOCTYPE html>
                            <html lang='es'>
                            <head>
                                <meta charset='UTF-8'>
                                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                                <title>Bienvenido a CENDI Tech</title>
                            </head>
                            <body style='margin:0;padding:0;background-color:#0b0e33;font-family:Verdana,Geneva,sans-serif;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;'>
                                <div style='width:100%;background-color:#0b0e33;background-image:linear-gradient(160deg,#0b0e33 0%,#181E93 48%,#193A70 100%);padding:40px 12px;'>
                                    <div style='max-width:620px;margin:0 auto;'>

                                        <div style='background:rgba(255,255,255,0.10);border:1px solid rgba(255,255,255,0.22);border-radius:22px;overflow:hidden;box-shadow:0 18px 50px rgba(4,6,28,0.45), inset 0 1px 0 rgba(255,255,255,0.30);'>

                                            <div style='padding:34px 28px 22px;text-align:center;background:linear-gradient(180deg,rgba(255,255,255,0.14),rgba(255,255,255,0.02));border-bottom:1px solid rgba(255,255,255,0.16);'>
                                                <img src='cid:logo_blanco' alt='CENDITECH' width='150' height='100' style='display:block;margin:0 auto 16px;width:150px;height:100px;'>
                                                <span style='display:inline-block;padding:7px 18px;border-radius:999px;background:rgba(249,178,51,0.15);border:1px solid rgba(249,178,51,0.45);color:#F9B233;font-size:12px;font-weight:bold;letter-spacing:2px;'>BIENVENIDO AL FUTURO</span>
                                            </div>

                                            <div style='padding:30px 28px;color:#ffffff;'>
                                                <h1 style='margin:0 0 10px;font-size:22px;line-height:1.3;color:#ffffff;text-align:center;'>¡Tu Bootcamp de $program comienza hoy!</h1>
                                                <p style='text-align:center;font-size:14px;color:rgba(255,255,255,0.75);margin:0 0 24px;'>Hola <b style='color:#F9B233;'>$nombre</b>,</p>
                                                <p style='font-size:14px;line-height:1.7;color:rgba(255,255,255,0.82);margin:0 0 14px;'>¡Felicitaciones! Nos emociona darte la bienvenida al <b style='color:#ffffff;'>Bootcamp de $program</b> de CENDI Tech. Este es el primer paso hacia un futuro lleno de posibilidades en una de las áreas más demandadas del mercado.</p>
                                                <p style='font-size:14px;line-height:1.7;color:rgba(255,255,255,0.82);margin:0 0 24px;'>Aprenderás habilidades clave, trabajarás en proyectos prácticos y te prepararás para enfrentar los desafíos del mundo digital.</p>

                                                <div style='background:rgba(255,255,255,0.12);border:1px solid rgba(255,255,255,0.24);border-radius:16px;padding:26px 22px;text-align:center;box-shadow:inset 0 1px 0 rgba(255,255,255,0.25);'>
                                                    <h2 style='margin:0 0 8px;font-size:18px;color:#ffffff;'>Verifica tu correo electrónico</h2>
                                                    <p style='margin:0 0 18px;font-size:14px;color:rgba(255,255,255,0.75);'>Para confirmar tu registro y activar tu cuenta, haz clic en el siguiente botón:</p>
                                                    <a href='$verificationUrl' target='_blank' style='display:inline-block;padding:15px 34px;border-radius:999px;background-image:linear-gradient(135deg,#F9B233 0%,#f39c12 100%);color:#193A70;text-decoration:none;font-weight:bold;font-size:15px;box-shadow:0 10px 24px rgba(249,178,51,0.35);'>Verificar mi correo</a>
                                                    <div style='margin-top:18px;padding-top:16px;border-top:1px dashed rgba(255,255,255,0.25);font-size:12px;color:rgba(255,255,255,0.6);'>
                                                        <b>Si el botón no funciona, copia y pega este enlace en tu navegador:</b><br>
                                                        <a href='$verificationUrl' target='_blank' style='color:#F9B233;word-break:break-all;'>$verificationUrl</a>
                                                    </div>
                                                </div>

                                                <h2 style='margin:26px 0 14px;font-size:16px;color:#F9B233;'>Próximos pasos</h2>

                                                <div style='background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.18);border-radius:14px;padding:16px 18px;margin-bottom:12px;'>
                                                    <b style='color:#ffffff;font-size:14px;'>1. Revisa tu correo</b><br>
                                                    <span style='font-size:13px;color:rgba(255,255,255,0.75);'>Te enviaremos toda la información necesaria para comenzar: horarios, plataforma y recursos.</span>
                                                </div>
                                                <div style='background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.18);border-radius:14px;padding:16px 18px;'>
                                                    <b style='color:#ffffff;font-size:14px;'>2. Prepárate para el inicio</b><br>
                                                    <span style='font-size:13px;color:rgba(255,255,255,0.75);'>Asegúrate de contar con un dispositivo adecuado y una conexión estable a internet para sacar el máximo provecho del programa.</span>
                                                </div>

                                                <p style='margin:22px 0 0;font-size:13px;line-height:1.7;color:rgba(255,255,255,0.7);'>Si tienes alguna duda o necesitas apoyo, no dudes en contactarnos. ¡Estamos aquí para ayudarte en cada etapa de tu formación!</p>
                                                <p style='margin:12px 0 0;font-size:13px;color:rgba(255,255,255,0.7);'>Gracias por confiar en nosotros y ser parte de esta gran comunidad. <b style='color:#F9B233;'>¡Nos vemos pronto futuro campista!</b> 🚀</p>
                                            </div>

                                            <div style='text-align:center;padding:22px 24px;background:rgba(0,0,0,0.18);border-top:1px solid rgba(255,255,255,0.16);'>
                                                <img src='cid:logo' alt='CENDITECH' width='180' height='80' style='display:block;margin:0 auto 8px;width:180px;height:80px;'>
                                                <p style='margin:0;font-size:12px;color:rgba(255,255,255,0.55);'>Equipo CENDI Tech</p>
                                            </div>

                                        </div>

                                    </div>
                                </div>
                            </body>
                            </html>";

        $mail->addEmbeddedImage(dirname(__DIR__, 2) . '/img/cendi_tech_blanco.png', 'logo_blanco');
        $mail->addEmbeddedImage(dirname(__DIR__, 2) . '/img/cendi_tech_logo_recortado.png', 'logo');

        $mail->send();
        return ['ok' => true];
    } catch (Exception $e) {
        return ['ok' => false, 'mensaje' => $mail->ErrorInfo];
    }
}
