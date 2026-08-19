<?php
header('Content-Type: application/json');
include("../../controller/conexion.php");

if (isset($_POST['programa']) && isset($_POST['sede'])) {
    $programa = $conn->real_escape_string($_POST['programa']);
    $sede = $conn->real_escape_string($_POST['sede']);
    
    $query = "SELECT DISTINCT schedule 
              FROM schedules_registrations 
              WHERE program = '$programa' AND headquarters = '$sede'
              ORDER BY schedule";
    
    $result = $conn->query($query);
    $horarios = [];
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $horarios[] = $row;
        }
    }
    
    echo json_encode([
        'success' => true,
        'horarios' => $horarios
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Parámetros faltantes'
    ]);
}
?>