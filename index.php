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
  <link rel="stylesheet" href="css/style.css?v=2.0">
  <link rel="stylesheet" href="css/accessibility.css?v=1.2">
  <link rel="stylesheet" href="css/sweetalert.css?v=1.2">
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
  <a href="#multi-step-form" class="acc-skip-link">Saltar al contenido principal</a>

  <div id="acc-widget">
    <button type="button" id="acc-trigger" class="acc-trigger" aria-label="Abrir panel de accesibilidad" aria-expanded="false" aria-controls="acc-panel">
      <i class="bi bi-universal-access" aria-hidden="true"></i>
    </button>

    <div id="acc-panel" class="acc-panel" role="dialog" aria-modal="false" aria-labelledby="acc-panel-title" hidden>
      <div class="acc-panel__header">
        <h2 id="acc-panel-title" class="acc-panel__title">Accesibilidad</h2>
        <button type="button" id="acc-close" class="acc-close" aria-label="Cerrar panel de accesibilidad">
          <i class="bi bi-x-lg" aria-hidden="true"></i>
        </button>
      </div>

      <div class="acc-panel__body">
        <div class="acc-font-group" role="group" aria-label="Ajustar tamaño del texto">
          <span class="acc-font-label"><i class="bi bi-type" aria-hidden="true"></i> Tamaño de texto</span>
          <div class="acc-font-actions">
            <button type="button" data-acc-font="-1" aria-label="Disminuir tamaño de texto">A&minus;</button>
            <button type="button" data-acc-font="reset" aria-label="Restablecer tamaño de texto">A</button>
            <button type="button" data-acc-font="1" aria-label="Aumentar tamaño de texto">A+</button>
          </div>
        </div>

        <div class="acc-controls">
          <button type="button" class="acc-btn" data-acc-toggle="highContrast" aria-pressed="false">
            <i class="bi bi-circle-half" aria-hidden="true"></i> Alto contraste
          </button>
          <button type="button" class="acc-btn" data-acc-toggle="grayscale" aria-pressed="false">
            <i class="bi bi-circle" aria-hidden="true"></i> Escala de grises
          </button>
          <button type="button" class="acc-btn" data-acc-toggle="highlightLinks" aria-pressed="false">
            <i class="bi bi-highlighter" aria-hidden="true"></i> Resaltar enlaces
          </button>
          <button type="button" class="acc-btn" data-acc-toggle="readableFont" aria-pressed="false">
            <i class="bi bi-fonts" aria-hidden="true"></i> Fuente legible
          </button>
          <button type="button" class="acc-btn" data-acc-toggle="textSpacing" aria-pressed="false">
            <i class="bi bi-text-left" aria-hidden="true"></i> Espaciado de texto
          </button>
          <button type="button" class="acc-btn" data-acc-toggle="bigCursor" aria-pressed="false">
            <i class="bi bi-cursor" aria-hidden="true"></i> Cursor grande
          </button>
          <button type="button" class="acc-btn acc-btn--full" data-acc-reset>
            <i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i> Restablecer todo
          </button>
        </div>
      </div>
    </div>
  </div>

  <div id="vanta-bg"></div>
  <div class="container">
    <img src="img/banner_light.webp" alt="banner top" class="w-100 rounded mt-3">
  </div>

  <div class="container">

    <?php include("APIS/register_student/newStudent.php"); ?>

  </div>

  <div class="container">
    <img src="img/footer_light.webp" alt="banner bottom" class="w-100 rounded mb-3">
  </div>

  <script src="js/accessibility.js"></script>
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
      color: 0x193a70,
      backgroundColor: 0xf4f6fb
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