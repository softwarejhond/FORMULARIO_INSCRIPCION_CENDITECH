<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

$host = 'smtp-relay.brevo.com';
$username = 'b87983001@smtp-brevo.com';
$password = 'bskp3dGrCOeVj6L';
$port = 587;

$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = $host;
    $mail->SMTPAuth = true;
    $mail->Username = $username;
    $mail->Password = $password;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = $port;
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;
    $mail->setFrom('noreply@cenditech.com.co', 'Servicio al cliente');
    $mail->addAddress('juandidoc11@gmail.com');
    $mail->CharSet = 'UTF-8';
    $mail->isHTML(true);
    $mail->Subject = 'Test SMTP Brevo';
    $mail->Body = 'Test';
    $mail->send();
    echo "OK ENVIADO\n";
} catch (Exception $e) {
    echo "ERROR: " . $mail->ErrorInfo . "\n";
}
