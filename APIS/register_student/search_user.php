<?php
header('Content-Type: application/json');
include("../../controller/conexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $number_id = $input['number_id'] ?? '';
    
    if (!$number_id) {
        echo json_encode(['success' => false, 'message' => 'Número de cédula requerido']);
        exit;
    }
    
    $query = "SELECT * FROM user_register WHERE number_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $number_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $userData = $result->fetch_assoc();
        echo json_encode(['success' => true, 'data' => $userData]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
    }
    
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
?>