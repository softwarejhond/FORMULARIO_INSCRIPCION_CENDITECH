<?php
header('Content-Type: application/json');
require_once 'conexion.php';

$data = json_decode(file_get_contents('php://input'), true);
$sede_id = intval($data['sede_id'] ?? 0);
$password = $data['password'] ?? '';

if (!$sede_id || !$password) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit;
}

$query = "SELECT password FROM headquarters WHERE id = $sede_id";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $hash = $row['password'];
    if ($hash && password_verify($password, $hash)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Contraseña incorrecta']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Sede no encontrada']);
}