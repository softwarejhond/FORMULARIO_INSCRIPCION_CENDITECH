<?php
// Habilitar la visualización de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Verificación de correo</title>
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

    .verification-card {
      background: rgba(255, 255, 255, 0.4);
      backdrop-filter: blur(8px);
      -webkit-backdrop-filter: blur(8px);
      border: 2px solid rgba(25, 58, 112, 0.15);
      border-radius: 14px;
      color: #193A70;
      padding: 2rem;
      max-width: 680px;
      width: 100%;
      margin: 0 auto;
    }

    .glass-alert {
      background: rgba(255, 255, 255, 0.55);
      backdrop-filter: blur(6px);
      -webkit-backdrop-filter: blur(6px);
      border: 2px solid rgba(25, 58, 112, 0.2);
      border-left: 6px solid #193A70;
      border-radius: 12px;
      color: #193A70;
      padding: 1.5rem 1.75rem;
      margin: 1rem 0;
    }

    .glass-alert h4,
    .glass-alert h5 {
      color: #193A70;
      font-weight: 700;
      margin-top: 0;
    }

    .glass-alert p {
      color: #193A70;
      margin-bottom: 0.4rem;
    }

    .glass-alert p:last-child {
      margin-bottom: 0;
    }

    .glass-alert .bi {
      margin-right: 0.35rem;
    }

    .glass-alert-success { border-left-color: #198754; }
    .glass-alert-info    { border-left-color: #0d6efd; }
    .glass-alert-warning { border-left-color: #ffc107; }
    .glass-alert-danger  { border-left-color: #dc3545; }
  </style>
</head>

<body>
  <div id="vanta-bg"></div>

  <div class="container">
    <img src="img/banner_verificacion.webp" alt="banner top" class="w-100 rounded mt-3">
  </div>

  <div class="container">
    <div class="verification-card mt-4 mb-4">
      <?php include("APIS/register_student/verify_student.php"); ?>
    </div>
  </div>

  <div class="container">
    <img src="img/footer_light.webp" alt="banner bottom" class="w-100 rounded mb-3">
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
      color: 0x193a70,
      backgroundColor: 0xf4f6fb
    })
  </script>

</body>

</html>
