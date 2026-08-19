<?php
// Habilitar la visualización de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>

<?php
include("controller/conexion.php");

// Obtener el nombre de la sede o institución
$nombreSede = '';
if (isset($_GET['sede'])) {
  $idSede = intval($_GET['sede']);
  $query = "SELECT name FROM headquarters WHERE id = $idSede LIMIT 1";
  $result = $conn->query($query);
  if ($result && $row = $result->fetch_assoc()) {
    $nombreSede = $row['name'];
  }
} elseif (isset($_GET['institucion'])) {
  $nombreSede = urldecode($_GET['institucion']);
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
  <img src="img/baner_inscripcion.webp" alt="banner top" class="w-100">
  <div class="container">

    <?php include("APIS/register_student/newStudent.php"); ?>

  </div>
  <img src="img/footer.webp" alt="banner top" class="w-100">

</body>
<?php include("controller/scripts.php"); ?>

</html>



<script>
  document.addEventListener('DOMContentLoaded', function() {
    const certificadoPrevio = document.getElementById('certificadoPrevio');
    const programaContainer = document.getElementById('programaContainer');
    const programaCertificado = document.getElementById('programaCertificado');
    const otroProgramaContainer = document.getElementById('otroProgramaContainer');
    const otroPrograma = document.getElementById('otroPrograma');
    const anioCertificacionContainer = document.getElementById('anioCertificacionContainer');
    const anioCertificacion = document.getElementById('anioCertificacion');
    const btnEntendido = document.getElementById('btnEntendido');
    const errorMensaje = document.getElementById('errorMensaje');

    // Generar opciones de años dinámicamente
    function generateYearOptions() {
      const currentYear = new Date().getFullYear();
      let yearOptions = '<option value="" selected disabled>Selecciona el año</option>';

      for (let year = currentYear; year >= 2015; year--) {
        yearOptions += `<option value="${year}">${year}</option>`;
      }

      anioCertificacion.innerHTML = yearOptions;
    }

    // Función para validar si todos los campos requeridos están completos
    function validarCampos() {
      let isValid = false;
      errorMensaje.style.display = 'none';

      if (!certificadoPrevio.value) {
        // Si no ha seleccionado nada, deshabilitar botón
        isValid = false;
      } else if (certificadoPrevio.value === 'NO') {
        // Si seleccionó "NO", habilitar botón inmediatamente
        isValid = true;
      } else if (certificadoPrevio.value === 'SI') {
        // Si seleccionó "SÍ", validar que complete todos los campos adicionales
        if (!programaCertificado.value) {
          isValid = false;
        } else if (programaCertificado.value === 'Otro' && !otroPrograma.value.trim()) {
          isValid = false;
        } else if (!anioCertificacion.value) {
          isValid = false;
        } else {
          isValid = true;
        }
      }

      btnEntendido.disabled = !isValid;
      console.log('Validación:', {
        certificadoPrevio: certificadoPrevio.value,
        programaCertificado: programaCertificado.value,
        otroPrograma: otroPrograma.value,
        anioCertificacion: anioCertificacion.value,
        isValid: isValid
      });
    }

    // Event listeners para todos los campos
    certificadoPrevio.addEventListener('change', function() {
      if (this.value === 'SI') {
        programaContainer.style.display = 'block';
        anioCertificacionContainer.style.display = 'block';
        generateYearOptions(); // Generar opciones de años cuando se muestre
      } else {
        programaContainer.style.display = 'none';
        otroProgramaContainer.style.display = 'none';
        anioCertificacionContainer.style.display = 'none';
        // Limpiar valores cuando se ocultan
        programaCertificado.value = '';
        otroPrograma.value = '';
        anioCertificacion.value = '';
      }
      validarCampos();
    });

    programaCertificado.addEventListener('change', function() {
      if (this.value === 'Otro') {
        otroProgramaContainer.style.display = 'block';
      } else {
        otroProgramaContainer.style.display = 'none';
        otroPrograma.value = ''; // Limpiar el campo cuando se oculta
      }
      validarCampos();
    });

    // Event listeners para validación en tiempo real
    otroPrograma.addEventListener('input', validarCampos);
    anioCertificacion.addEventListener('change', validarCampos);

    // Manejar clic en el botón "Entendido"
    btnEntendido.addEventListener('click', function() {
      // Validar una vez más antes de continuar
      validarCampos();

      if (btnEntendido.disabled) {
        errorMensaje.style.display = 'block';
        return;
      }

      // Obtener valores para guardar
      const hasCertField = document.getElementById('has_certification');
      const programCertField = document.getElementById('program_certified');
      const numberIdField = document.getElementById('number_id');

      let hasCertValue = certificadoPrevio.value;
      let programCertValue = '';
      let anioCertValue = '';

      if (certificadoPrevio.value === 'SI') {
        if (programaCertificado.value === 'Otro') {
          programCertValue = otroPrograma.value.trim();
        } else {
          programCertValue = programaCertificado.value;
        }
        anioCertValue = anioCertificacion.value;
      }

      // Guardar en campos ocultos
      if (hasCertField) hasCertField.value = hasCertValue;
      if (programCertField) programCertField.value = programCertValue;

      // Crear/actualizar campo oculto para año de certificación
      let anioCertInput = document.getElementById('anio_certificacion');
      if (!anioCertInput) {
        anioCertInput = document.createElement('input');
        anioCertInput.type = 'hidden';
        anioCertInput.id = 'anio_certificacion';
        anioCertInput.name = 'anio_certificacion';
        document.getElementById('multi-step-form').appendChild(anioCertInput);
      }
      anioCertInput.value = anioCertValue;

      console.log('Datos guardados:', {
        has_certification: hasCertValue,
        program_certified: programCertValue,
        anio_certificacion: anioCertValue
      });

      // Configurar el envío de datos cuando se complete el formulario
      document.getElementById('multi-step-form').addEventListener('submit', function(e) {
        const numberIdValue = numberIdField.value;

        if (numberIdValue) {
          fetch('APIS/register_student/save_certification.php', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
              },
              body: `number_id=${encodeURIComponent(numberIdValue)}&has_certification=${encodeURIComponent(hasCertValue)}&program_certified=${encodeURIComponent(programCertValue)}&anio_certificacion=${encodeURIComponent(anioCertValue)}`
            })
            .then(response => response.json())
            .then(data => {
              console.log('Certificación guardada:', data);
            })
            .catch(error => {
              console.error('Error al guardar certificación:', error);
            });
        }
      });

      // Cerrar el modal
      const modal = bootstrap.Modal.getInstance(document.getElementById('infoModal'));
      modal.hide();
    });

    // Inicializar validación
    validarCampos();
  });
</script>