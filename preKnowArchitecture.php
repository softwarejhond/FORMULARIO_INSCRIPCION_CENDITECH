<?php
// Habilitar la visualización de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>

<?php 
include("controller/conexion.php"); ?>
<!DOCTYPE html>
<html lang="en">
<?php include("controller/head.php"); ?>
<body>
<img src="img/banner_pruebas_Computración.png" alt="banner top" class="w-100">
<div class="container">

<?php include("APIS/preknowledge/Computración.php"); ?>

</div>
<img src="img/Banner-inferior.png" alt="banner bottom" class="w-100">



</body>
<script src="node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.all.min.js"></script>
</html>
