<?php
header('Content-Type: application/json; charset=utf-8');
include("../../controller/conexion.php");

$departamento = isset($_GET['departamento']) ? intval($_GET['departamento']) : 0;

$municipios = [];
if ($departamento > 0) {
    $query = "SELECT cod_municipio, nom_municipio FROM municipios WHERE cod_departamento = $departamento ORDER BY nom_municipio";
    $result = $conn->query($query);
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $municipios[] = $row;
        }
    }
}

echo json_encode($municipios);
