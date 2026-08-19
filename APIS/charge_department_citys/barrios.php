<?php 
require_once 'conexion.php';

function getBarrio(){
	$mysqli = getConn();
	$id = $_POST['idB'];
	$query = "SELECT * FROM `barrios` WHERE codigo_municipio = $id ORDER BY barrio ASC";
	$result = $mysqli->query($query);
	$barrio = '<option value="0">Seleccionar barrio</option>';
	while($row = $result->fetch_array(MYSQLI_ASSOC)){
		$barrio.= "<option value='$row[barrio]'>$row[barrio]</option>";
	}
	
	return $barrio;
}

echo getBarrio();
?>
