<?php
require dirname(__DIR__, 2) . '/controller/conexion.php';

$token = $_GET['token'] ?? '';
$email = $_GET['email'] ?? '';

if ($token === '' || $email === '') {
    echo "<div class='glass-alert glass-alert-warning'>
            <h4>Enlace de verificación inválido</h4>
            <p>El enlace no contiene los datos necesarios para verificar tu correo.</p>
          </div>";
    return;
}

$stmt = $conn->prepare("SELECT id, number_id, email_verified FROM user_register WHERE email = ? AND token = ? LIMIT 1");
$stmt->bind_param('ss', $email, $token);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

if (!$row) {
    echo "<div class='glass-alert glass-alert-danger text-center'>
            <h4>Enlace no válido</h4>
            <p>El enlace de verificación es inválido o ha expirado.</p>
          </div>";
    $stmt->close();
    return;
}

if ((int)$row['email_verified'] === 1) {
    echo "<div class='glass-alert glass-alert-info text-center'>
            <h4>Tu correo ya fue verificado</h4>
            <p>Este correo ya había sido verificado anteriormente.</p>
          </div>";
    $stmt->close();
    return;
}

$update = $conn->prepare("UPDATE user_register SET email_verified = 1 WHERE email = ? AND token = ?");
$update->bind_param('ss', $email, $token);
if ($update->execute()) {
    echo "<div class='glass-alert glass-alert-success text-center'>
            <h4>¡Correo verificado exitosamente!</h4>
            <p>Tu cuenta ha sido activada correctamente.</p>
          </div>";

    // Matrícula automática en Moodle
    require_once __DIR__ . '/auto_enroll.php';
    try {
        $resultadoMatricula = matricularEstudiante($conn, $row['number_id']);
    } catch (Throwable $e) {
        $resultadoMatricula = ['ok' => false, 'mensaje' => 'Error interno durante la matrícula: ' . $e->getMessage()];
    }

    if (!empty($resultadoMatricula['ok'])) {
        echo "<div class='glass-alert glass-alert-info text-center'>
                <h5><i class='bi bi-mortarboard-fill'></i>¡Ya estás matriculado!</h5>
                <p>" . htmlspecialchars($resultadoMatricula['mensaje']) . "</p>
                <p>Te enviamos un correo con tu usuario y contraseña de acceso a la plataforma.</p>
              </div>";
    } elseif (!empty($resultadoMatricula['pendiente'])) {
        echo "<div class='glass-alert glass-alert-warning text-center'>
                <h5>Tu correo fue verificado</h5>
                <p>" . htmlspecialchars($resultadoMatricula['mensaje']) . "</p>
                <p>Te notificaremos cuando tu matrícula esté disponible.</p>
              </div>";
    } else {
        echo "<div class='glass-alert glass-alert-warning text-center'>
                <h5>Tu correo fue verificado</h5>
                <p>" . htmlspecialchars($resultadoMatricula['mensaje'] ?? 'No se pudo completar la matrícula en este momento.') . "</p>
              </div>";
    }
} else {
    echo "<div class='glass-alert glass-alert-danger'>
            <h4>Error al verificar</h4>
            <p>" . htmlspecialchars($conn->error) . "</p>
          </div>";
}
$update->close();
$stmt->close();
