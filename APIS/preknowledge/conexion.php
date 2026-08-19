<?php
//datos del servidor
$server = "localhost";
$username = "u609911669_dashboard";
$password = "Oikid@a#ip6g";
$bd = "u609911669_dashboard";

//$server = "localhost";
//$username = "root";
//$password = "";
//$bd = "db_formularios";
//creamos una conexión
$conn = mysqli_connect($server, $username, $password, $bd);
//Chequeamos la conexión
if (!$conn) {
    die("Conexión fallida:" . mysqli_connect_error());
}