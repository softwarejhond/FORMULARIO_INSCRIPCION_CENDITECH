<?php
// Habilitar la visualización de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>

<?php
include("controller/conexion.php");

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
  <img src="img/header_closed.png" alt="banner top" class="w-100">
  <div class="container">

    <?php include("APIS/register_student/newStudentClosed.php"); ?>

    <!-- Modal Informativo con preguntas de certificación -->
    <div class="modal fade" id="infoModal" tabindex="-1" aria-labelledby="infoModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="infoModalLabel">Información Importante</h5>
            <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
          </div>
          <div class="modal-body">
            <p>
              Estamos emocionados de que formes parte de este sueño. Por favor, asegúrate de llenar todos los campos correctamente, <b>tenga en cuenta que son obligatorios todos los campos</b>.
              Una vez que envíes el formulario, recibirás un correo electrónico de confirmación. Si no lo encuentras en tu bandeja principal, recuerda revisar en la carpeta de spam.
            </p>

            <hr class="my-3">

            <!-- Preguntas sobre certificación previa -->
            <h6 class="mb-3">Para agilizar tu proceso de matrícula, por favor responde:</h6>

            <div class="form-group mb-3">
              <label class="form-label fw-bold">¿Ya cuentas con una certificación previa del programa Talento Tech?</label>
              <select id="certificadoPrevio" class="form-select">
                <option value="" selected disabled>Selecciona una opción</option>
                <option value="SI">Sí</option>
                <option value="NO">No</option>
              </select>
            </div>

            <div id="programaContainer" class="form-group mb-3" style="display:none;">
              <label class="form-label fw-bold">¿En cuál programa?</label>
              <select id="programaCertificado" class="form-select">
                <option value="" selected disabled>Selecciona un programa</option>
                <option value="Análisis de datos">Análisis de datos</option>
                <option value="Ciberseguridad">Ciberseguridad</option>
                <option value="Inteligencia Artificial">Inteligencia Artificial</option>
                <option value="Programación">Programación</option>
                <option value="BlockChain">BlockChain</option>
                <option value="Computración en la nube">Computración en la nube</option>
                <option value="Otro">Otro</option>
              </select>
            </div>

            <div id="otroProgramaContainer" class="form-group mb-3" style="display:none;">
              <label class="form-label fw-bold">Especifica el programa:</label>
              <input type="text" id="otroPrograma" class="form-control" placeholder="Escribe el nombre del programa">
            </div>

            <div id="anioCertificacionContainer" class="form-group mb-3" style="display:none;">
              <label class="form-label fw-bold">¿En qué año obtuviste la certificación?</label>
              <select id="anioCertificacion" class="form-select">
                <option value="" selected disabled>Selecciona el año</option>
              </select>
            </div>

            <div id="errorMensaje" class="alert alert-danger" style="display:none;">
              Por favor completa todos los campos requeridos.
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" id="btnEntendido" class="btn" style="background-color:#066aab; color:white" disabled>Entendido</button>
          </div>
        </div>
      </div>
    </div>

  </div>
  <img src="img/footer.webp" alt="banner top" class="w-100">

</body>
<?php include("controller/scripts.php"); ?>

</html>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
      imageUrl: 'img/cerradas.jpeg',
      imageAlt: 'Inscripciones cerradas',
      showConfirmButton: false,
      showCancelButton: false,
      allowOutsideClick: false,
      allowEscapeKey: false
    });
  });
</script>

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

    // MOSTRAR EL MODAL AL CARGAR LA PÁGINA
    const infoModal = document.getElementById('infoModal');
    if (infoModal) {
      const myModal = new bootstrap.Modal(infoModal);
      myModal.show();
    }

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
        isValid = false;
      } else if (certificadoPrevio.value === 'NO') {
        isValid = true;
      } else if (certificadoPrevio.value === 'SI') {
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
    }

    // Event listeners para todos los campos
    certificadoPrevio.addEventListener('change', function() {
      if (this.value === 'SI') {
        programaContainer.style.display = 'block';
        anioCertificacionContainer.style.display = 'block';
        generateYearOptions();
      } else {
        programaContainer.style.display = 'none';
        otroProgramaContainer.style.display = 'none';
        anioCertificacionContainer.style.display = 'none';
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
        otroPrograma.value = '';
      }
      validarCampos();
    });

    otroPrograma.addEventListener('input', validarCampos);
    anioCertificacion.addEventListener('change', validarCampos);

    // Manejar clic en el botón "Entendido"
    btnEntendido.addEventListener('click', function() {
      validarCampos();

      if (btnEntendido.disabled) {
        errorMensaje.style.display = 'block';
        return;
      }

      // Obtener valores para guardar
      const hasCertField = document.getElementById('has_certification');
      const programCertField = document.getElementById('program_certified');

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

      // Cerrar el modal
      const modal = bootstrap.Modal.getInstance(document.getElementById('infoModal'));
      modal.hide();

      // ESPERAR A QUE EL MODAL SE CIERRE COMPLETAMENTE ANTES DE MOSTRAR EL SWAL
      setTimeout(() => {
        // Mostrar swal para digitar la cédula
        Swal.fire({
          title: 'Consulta tu registro',
          text: 'Por favor, digita tu número de cédula para buscar tus datos y continuar con la inscripción.',
          input: 'text',
          inputLabel: 'Número de cédula',
          inputPlaceholder: 'Ejemplo: 1234567890',
          inputAttributes: {
            autocapitalize: 'off',
            autocorrect: 'off'
          },
          showCancelButton: false,
          confirmButtonText: 'Buscar',
          allowOutsideClick: false,
          allowEscapeKey: false,
          preConfirm: (cedula) => {
            if (!cedula) {
              Swal.showValidationMessage('Debes ingresar tu número de cédula');
              return false;
            }
            // Buscar usuario en la base de datos
            return fetch('APIS/register_student/search_user.php', {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                  number_id: cedula
                })
              })
              .then(response => response.json())
              .then(data => {
                if (!data.success) {
                  // Notificar y redirigir si no está en el pre-registro
                  Swal.fire({
                    title: 'No encontrado',
                    text: 'No se encontró tu pre-inscripción. Por favor realiza tu inscripción desde cero.',
                    icon: 'warning',
                    confirmButtonText: 'Ir a inscripción'
                  }).then(() => {
                    window.location.href = 'index.php';
                  });
                  throw new Error(data.message || 'Usuario no encontrado');
                }
                return data.data;
              })
              .catch(error => {
                Swal.showValidationMessage(error.message);
              });
          }
        }).then((result) => {
          if (result.isConfirmed && result.value) {
            // Mostrar mensaje de éxito al encontrar los datos
            Swal.fire({
              title: '¡Datos encontrados!',
              text: 'Se han cargado tus datos de pre-inscripción correctamente. Puedes revisar y completar la información faltante.',
              icon: 'success',
              confirmButtonText: 'Continuar',
              timer: 3000,
              timerProgressBar: true
            }).then(() => {
              // Llenar los campos del formulario con los datos encontrados
              const userData = result.value;
              Object.keys(userData).forEach(function(key) {
                const input = document.querySelector(`[name="${key}"]`);
                if (input) {
                  if (input.type === 'radio') {
                    if (input.value === userData[key]) {
                      input.checked = true;
                    }
                  } else if (input.type === 'checkbox') {
                    if (userData[key] === 'Sí' || userData[key] === '1') {
                      input.checked = true;
                    }
                  } else {
                    input.value = userData[key];
                  }
                  input.removeAttribute('readonly');
                  input.removeAttribute('disabled');
                }
              });

              // Para radio buttons de stratum
              if (userData.stratum) {
                const radio = document.querySelector(`[name="stratum"][value="${userData.stratum}"]`);
                if (radio) radio.checked = true;
              }
            });
          }
        });
      }, 500); // Esperar 500ms antes de mostrar el SweetAlert
    });

    // Inicializar validación
    validarCampos();
  });
</script>