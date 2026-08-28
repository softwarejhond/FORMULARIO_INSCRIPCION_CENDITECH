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
  <style>
    #vanta-bg {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: -1;
    }
  </style>
</head>

<body>
  <div id="vanta-bg"></div>
  <div class="container">
    <img src="img/banner_dark.webp" alt="banner top" class="w-100 rounded mt-3">
  </div>

  <div class="container">

    <?php include("APIS/register_student/newStudent.php"); ?>

  </div>

  <div class="container">
    <img src="img/footer_dark.webp" alt="banner bottom" class="w-100 rounded mb-3">
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r134/three.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/vanta@latest/dist/vanta.net.min.js"></script>
  <script>
    VANTA.NET({
      el: "#vanta-bg",
      mouseControls: true,
      touchControls: true,
      gyroControls: false,
      minHeight: 200.00,
      minWidth: 200.00,
      scale: 1.00,
      scaleMobile: 1.00,
      color: 0xfa7f1d,
      backgroundColor: 0x000000
    })
  </script>

</body>
<?php include("controller/scripts.php"); ?>

<script>
  document.addEventListener('DOMContentLoaded', function() {
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