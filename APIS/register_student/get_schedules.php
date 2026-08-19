<?php
header('Content-Type: application/json');
require_once 'conexion.php';

// Obtener parámetros de sede y programa
$headquarters = $_GET['headquarters'] ?? '';
$program = $_GET['program'] ?? '';

// Validar
if (empty($headquarters) || empty($program)) {
    echo json_encode([
        'success' => false,
        'error' => 'Faltan parámetros de sede o programa',
        'headquarters' => $headquarters,
        'program' => $program
    ]);
    exit;
}

try {
    // Consulta filtrando por sede y programa
    $query = "SELECT DISTINCT schedule FROM schedules WHERE headquarters = ? AND program = ?";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        echo json_encode([
            'success' => false,
            'error' => 'Error al preparar la consulta SQL',
            'message' => $conn->error
        ]);
        exit;
    }

    $stmt->bind_param('ss', $headquarters, $program);
    $stmt->execute();
    $result = $stmt->get_result();

    $schedules = [];
    while ($row = $result->fetch_assoc()) {
        $schedules[] = $row;
    }

    echo json_encode([
        'success' => true,
        'schedules' => $schedules
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Error al ejecutar la consulta',
        'message' => $e->getMessage()
    ]);
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    $conn->close();
}
?>
