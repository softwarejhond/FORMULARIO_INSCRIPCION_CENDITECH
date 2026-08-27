<?php
require dirname(__DIR__, 2) . '/controller/conexion.php';

$token = $_GET['token'] ?? '';
$email = $_GET['email'] ?? '';

if ($token === '' || $email === '') {
    echo "<div class='alert alert-warning mt-4'>Enlace de verificación inválido.</div>";
    exit;
}

$stmt = $conn->prepare("SELECT id, email_verified FROM user_register WHERE email = ? AND token = ? LIMIT 1");
$stmt->bind_param('ss', $email, $token);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

if (!$row) {
    echo "<div class='alert alert-danger mt-4 text-center'>
            <h4>Enlace no válido</h4>
            <p>El enlace de verificación es inválido o ha expirado.</p>
          </div>";
    exit;
}

if ((int)$row['email_verified'] === 1) {
    echo "<div class='alert alert-info mt-4 text-center'>
            <h4>Tu correo ya fue verificado</h4>
            <p>Este correo ya había sido verificado anteriormente.</p>
          </div>";
    exit;
}

$update = $conn->prepare("UPDATE user_register SET email_verified = 1 WHERE email = ? AND token = ?");
$update->bind_param('ss', $email, $token);
if ($update->execute()) {
    echo "<div class='alert alert-success mt-4 text-center'>
            <h4>¡Correo verificado exitosamente!</h4>
            <p>Tu cuenta ha sido activada correctamente.</p>
          </div>";
} else {
    echo "<div class='alert alert-danger mt-4'>Error al verificar: " . $conn->error . "</div>";
}
$update->close();
$stmt->close();
