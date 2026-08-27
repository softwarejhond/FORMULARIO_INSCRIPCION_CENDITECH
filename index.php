<?php
// Habilitar la visualización de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>

<?php
include("controller/conexion.php");

// Si no se especifica una sede o institución, usar la sede virtual por defecto (No aplica)
if (!isset($_GET['sede']) && !isset($_GET['institucion'])) {
    $queryVirtual = "SELECT id FROM headquarters WHERE name = 'No aplica' LIMIT 1";
    $resultVirtual = $conn->query($queryVirtual);
    if ($resultVirtual && $row = $resultVirtual->fetch_assoc()) {
        $_GET['sede'] = $row['id'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inscripción</title>
  <link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="node_modules/bootstrap-icons/font/bootstrap-icons.css">
  <link rel="stylesheet" href="css/style.css?v=1.9">
  <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.css" rel="stylesheet">
  <link rel="icon" href="img/icono_ct.png" type="image/x-icon">
</head>

<body>
  <img src="img/header_ph.webp" alt="banner top" class="w-100">
  <div class="container">

    <?php include("APIS/register_student/newStudent.php"); ?>

  </div>
  <img src="img/footer_ph.webp" alt="banner top" class="w-100">

</body>
<?php include("controller/scripts.php"); ?>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    Swal.fire({
      title: 'Aviso importante',
      text: 'La inscripción se debe realizar con un correo vigente, válido y activo. Recomendamos no inscribirse con correos temporales o de prueba, ya que durante los cursos pueden estar recibiendo información.',
      icon: 'info',
      confirmButtonText: 'Entendido',
      allowOutsideClick: false,
      allowEscapeKey: false
    });
  });
</script>

</html>
