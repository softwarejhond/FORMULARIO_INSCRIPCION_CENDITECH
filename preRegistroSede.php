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

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="node_modules/bootstrap-icons/font/bootstrap-icons.css">
  <link rel="stylesheet" href="css/style.css?v=1.9">
  <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.css" rel="stylesheet">
  <link rel="icon" href="img/icono_ct.png" type="image/x-icon">
</head>

<body>
  <img src="img/baner_inscripcion.webp" alt="banner top" class="w-100">
  <div class="container">

    <?php include("APIS/register_student/newPreRegis.php"); ?>

    <!-- Modal Informativo -->
    <!-- <div class="modal fade" id="infoModal" tabindex="-1" aria-labelledby="infoModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="infoModalLabel">Información Importante</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            Estamos emocionados de que formes parte de este sueño. Por favor, asegúrate de llenar todos los campos correctamente, <b>tenga en cuenta que son obligatorios todos los campos</b>.
            Una vez que envíes el formulario, recibirás un correo electrónico de confirmación. Si no lo encuentras en tu bandeja principal, recuerda revisar en la carpeta de spam.
            <br>
            ¡Bienvenido!
          </div>
          <div class="modal-footer">
            <button type="button" class="btn" data-bs-dismiss="modal" style="background-color:#066aab ; color:white">Entendido</button>
          </div>
        </div>
      </div>
    </div> -->

  </div>
  <img src="img/footer.webp" alt="banner top" class="w-100">
  <!-- Script para mostrar el modal al cargar la página -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      var myModal = new bootstrap.Modal(document.getElementById('infoModal'));
      myModal.show();
    });
  </script>

</body>
<?php include("controller/scripts.php"); ?>

</html>