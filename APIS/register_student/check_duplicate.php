<?php
header('Content-Type: application/json');
require dirname(__DIR__, 2) . '/controller/conexion.php';

$number_id = $_GET['number_id'] ?? '';
$email = $_GET['email'] ?? '';

$response = ['exists' => false, 'field' => ''];

if ($number_id !== '') {
    $stmt = $conn->prepare("SELECT id FROM user_register WHERE number_id = ? LIMIT 1");
    $stmt->bind_param('s', $number_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $response = ['exists' => true, 'field' => 'number_id'];
    }
    $stmt->close();
} elseif ($email !== '') {
    $stmt = $conn->prepare("SELECT id FROM user_register WHERE email = ? LIMIT 1");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $response = ['exists' => true, 'field' => 'email'];
    }
    $stmt->close();
}

echo json_encode($response);
