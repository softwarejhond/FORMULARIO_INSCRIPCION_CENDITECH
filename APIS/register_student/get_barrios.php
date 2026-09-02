<?php
header('Content-Type: application/json; charset=utf-8');
include("../../controller/conexion.php");

$comuna = isset($_GET['comuna']) ? $_GET['comuna'] : '';

$barrios = [];
if ($comuna !== '') {
    $comuna = $conn->real_escape_string($comuna);
    $query = "SELECT nombre FROM barrios WHERE limite_comuna_corregimiento_id = '$comuna' AND nombre IS NOT NULL AND nombre <> '' ORDER BY nombre";
    $result = $conn->query($query);
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $barrios[] = $row['nombre'];
        }
    }
}

echo json_encode($barrios);
