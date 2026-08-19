<?php
// filepath: c:\xampp\htdocs\INNOVA-FORM-REGISTER\APIS\register_student\save_certification.php
require_once '../../controller/conexion.php';

// Capturar los datos
$number_id = $_POST['number_id'] ?? '';
$has_certification = $_POST['has_certification'] ?? 'NO';
$program_certified = $_POST['program_certified'] ?? '';
$anio_certificacion = $_POST['anio_certificacion'] ?? null;

// Validación básica
if (empty($number_id)) {
    echo json_encode(['success' => false, 'message' => 'Número de identificación es obligatorio']);
    exit;
}

// Escapar valores para evitar inyección SQL
$number_id = $conn->real_escape_string($number_id);
$has_certification = $conn->real_escape_string($has_certification);
$program_certified = $conn->real_escape_string($program_certified);
$anio_certificacion = $conn->real_escape_string($anio_certificacion);

// Log para debugging
error_log("save_certification.php - Valores recibidos: number_id=$number_id, has_certification=$has_certification, program_certified=$program_certified, anio_certificacion=$anio_certificacion");

// Construir la consulta SQL
$query = "INSERT INTO certification_previous (number_id, has_certification, program_certified, anio_certificacion) 
          VALUES ('$number_id', '$has_certification', '$program_certified', " . 
          (is_null($anio_certificacion) ? "NULL" : "'$anio_certificacion'") . ")";

// Ejecutar la consulta
$result = $conn->query($query);

// Verificar resultado
if ($result) {
    echo json_encode(['success' => true, 'message' => 'Certificación guardada correctamente']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al guardar la certificación: ' . $conn->error]);
    error_log("Error SQL: " . $conn->error);
}