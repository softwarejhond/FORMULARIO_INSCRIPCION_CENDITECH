<?php
// Obtener el id de la sede desde la URL
$sede_id = isset($_GET['sede']) ? intval($_GET['sede']) : null;
$sede = null;
$programas = [];

if ($sede_id) {
    // Buscar la sede por id
    $querySede = "SELECT * FROM headquarters_registrations WHERE id = $sede_id LIMIT 1";
    $resultSede = $conn->query($querySede);
    if ($resultSede && $resultSede->num_rows > 0) {
        $sede = $resultSede->fetch_assoc();

        // Buscar programas únicos para la sede en schedules_registrations
        $sedeName = $conn->real_escape_string($sede['name']);
        $queryProgramas = "SELECT DISTINCT program 
                            FROM schedules_registrations 
                            WHERE headquarters = '$sedeName'
                            ORDER BY program";
        $resultProgramas = $conn->query($queryProgramas);
        if ($resultProgramas) {
            while ($row = $resultProgramas->fetch_assoc()) {
                $programas[] = $row['program'];
            }
        }
    }
}
?>

<style>
    .form-admin-container {
        max-width: auto;
        margin: 0 auto;
        background: #f8f9fa;
        border-radius: 12px;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.16);
        padding: 32px 24px;
    }

    .form-admin-container label {
        font-weight: 500;
    }

    .text-danger {
        color: #dc3545;
    }

    .form-check-input {
        border: 2px solid #0d6efd !important;
        background-color: #e7f1ff !important;
        width: 1.3em;
        height: 1.3em;
        margin-right: 8px;
    }

    .form-check-input:checked {
        background-color: #0d6efd !important;
        border-color: #0d6efd !important;
    }

    .btn-success {
        background-color: #28a745;
        border-color: #28a745;
    }

    .btn-success:hover {
        background-color: #218838;
        border-color: #1e7e34;
    }
</style>

<div class="form-admin-container mt-4 mb-4">
    <form id="formCierre" method="POST" autocomplete="off" novalidate>

        <div class="form-section" id="section-1">

            <div class="col-12 mb-3">
                <h4 class="mb-3">Datos de identificación básica</h4>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="typeID" class="form-label">Tipo de identificación <span class="text-danger">*</span></label>
                    <select name="typeID" id="typeID" class="form-control" required>
                        <option value="">Seleccione...</option>
                        <option value="CC">Cédula de ciudadanía</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="number_id" class="form-label">Número de identificación <span class="text-danger">*</span></label>
                    <input type="text" name="number_id" id="number_id" class="form-control" pattern="\d{5,15}" maxlength="15" required oninput="this.value=this.value.replace(/\D/g,'')">
                </div>
                <div class="col-md-4">
                    <label for="number_id_very" class="form-label">Verifique número de identificación <span class="text-danger">*</span></label>
                    <input type="text" name="number_id_very" id="number_id_very" class="form-control" pattern="\d{5,15}" maxlength="15" required oninput="this.value=this.value.replace(/\D/g,'')">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="email" class="form-label">Correo electrónico <span class="text-danger">*</span></label>
                    <input type="email" name="email" id="email" class="form-control" maxlength="100" required>
                    <small id="emailError" class="text-danger" style="display:none;">Formato de correo no válido.</small>
                </div>
                <div class="col-md-6">
                    <label for="email_very" class="form-label">Verifique correo electrónico <span class="text-danger">*</span></label>
                    <input type="email" name="email_very" id="email_very" class="form-control" maxlength="100" required>
                    <small id="emailVeryError" class="text-danger" style="display:none;">Formato de correo no válido.</small>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="email2" class="form-label">Segundo correo electrónico <span class="text-danger">*</span></label>
                    <input type="email" name="email2" id="email2" class="form-control" maxlength="100" required>
                    <small id="email2Error" class="text-danger" style="display:none;">Formato de correo no válido.</small>
                </div>
                <div class="col-md-6">
                    <label for="email2_very" class="form-label">Verifique segundo correo electrónico <span class="text-danger">*</span></label>
                    <input type="email" name="email2_very" id="email2_very" class="form-control" maxlength="100" required>
                    <small id="email2VeryError" class="text-danger" style="display:none;">Formato de correo no válido.</small>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="phone1" class="form-label">Número de teléfono 1 <span class="text-danger">*</span></label>
                    <input type="text" name="phone1" id="phone1" class="form-control" maxlength="15" pattern="\d{7,15}" required oninput="this.value=this.value.replace(/\D/g,'')">
                    <small id="phone1Error" class="text-danger" style="display:none;">Formato de teléfono no válido.</small>
                </div>
                <div class="col-md-6">
                    <label for="phone2" class="form-label">Número de teléfono 2 <span class="text-danger">*</span></label>
                    <input type="text" name="phone2" id="phone2" class="form-control" maxlength="15" pattern="\d{7,15}" required oninput="this.value=this.value.replace(/\D/g,'')">
                    <small id="phone2Error" class="text-danger" style="display:none;">Formato de teléfono no válido.</small>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="first_name" class="form-label">Primer nombre <span class="text-danger">*</span></label>
                    <input type="text" name="first_name" id="first_name" class="form-control" maxlength="50" required>
                </div>
                <div class="col-md-6">
                    <label for="second_name" class="form-label">Segundo nombre</label>
                    <input type="text" name="second_name" id="second_name" class="form-control" maxlength="50">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="first_last" class="form-label">Primer apellido <span class="text-danger">*</span></label>
                    <input type="text" name="first_last" id="first_last" class="form-control" maxlength="50" required>
                </div>
                <div class="col-md-6">
                    <label for="second_last" class="form-label">Segundo apellido <span class="text-danger">*</span></label>
                    <input type="text" name="second_last" id="second_last" class="form-control" maxlength="50" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="sede" class="form-label">Sede <span class="text-danger">*</span></label>
                    <!-- Sede (solo una opción, deshabilitada) -->
                    <select name="sede" id="sede" class="form-control" required disabled>
                        <?php if ($sede): ?>
                            <option value="<?php echo htmlspecialchars($sede['id']); ?>" selected>
                                <?php echo htmlspecialchars($sede['name']); ?>
                            </option>
                        <?php else: ?>
                            <option value="">Seleccione...</option>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="programa" class="form-label">Programa <span class="text-danger">*</span></label>
                    <!-- Programas (solo los de la sede seleccionada) -->
                    <select name="programa" id="programa" class="form-control" required>
                        <option value="">Seleccione...</option>
                        <?php foreach ($programas as $programa): ?>
                            <option value="<?php echo htmlspecialchars($programa); ?>">
                                <?php echo htmlspecialchars($programa); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12">
                    <label for="horario" class="form-label">Horario</label>
                    <select name="horario" id="horario" class="form-control">
                        <option value="">Seleccione...</option>
                        <!-- Opciones de horario se agregarán posteriormente -->
                    </select>
                </div>
            </div>
        </div>

        <div class="row mb-3 mt-4">
            <div class="col-md-12">
                <div class="form-check d-flex align-items-center">
                    <input class="form-check-input me-2" type="checkbox" id="accept_requirements" name="accept_requirements" required>
                    <label class="form-check-label" for="accept_requirements">
                        Acepta los requisitos establecidos por la presente convocatoria <br>
                        <a href="https://www.mintic.gov.co/portal/inicio/Secciones-auxiliares/Politicas/2627:Politicas-de-Privacidad-y-Condiciones-de-Uso" target="_blank" rel="noopener" class="ms-2">Puedes consultar los requisitos de la convocatoria haciendo click aquí</a>
                        <span class="text-danger">*</span>
                    </label>
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="form-check d-flex align-items-center">
                    <input class="form-check-input me-2" type="checkbox" id="accept_data_policies" name="accept_data_policies" required>
                    <label class="form-check-label" for="accept_data_policies">
                        Confirmo que he leído y acepto las políticas de tratamiento de datos personales <br>
                        <a href="https://drive.google.com/file/d/1r6acAm9TflaQBxfBm8WvX8QiC1REO0Qv/view" target="_blank" rel="noopener" class="ms-2">Puedes consultar las políticas haciendo click aquí</a>
                        <span class="text-danger">*</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="text-center mt-4">
            <button type="submit" id="saveBtn" class="btn btn-success btn-lg px-5" style="display:none;">
                <i class="bi bi-save me-2"></i>
                Guardar
            </button>
        </div>
    </form>
</div>



<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Validación de formato de correo en tiempo real
    document.getElementById('email').addEventListener('input', function() {
        const email = this.value;
        const emailError = document.getElementById('emailError');
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email) && email.length > 0) {
            emailError.style.display = 'block';
        } else {
            emailError.style.display = 'none';
        }
    });

    document.getElementById('email_very').addEventListener('input', function() {
        const email = this.value;
        const emailError = document.getElementById('emailVeryError');
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email) && email.length > 0) {
            emailError.style.display = 'block';
        } else {
            emailError.style.display = 'none';
        }
    });

    document.getElementById('email2').addEventListener('input', function() {
        const email = this.value;
        const emailError = document.getElementById('email2Error');
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email) && email.length > 0) {
            emailError.style.display = 'block';
        } else {
            emailError.style.display = 'none';
        }
    });

    document.getElementById('email2_very').addEventListener('input', function() {
        const email = this.value;
        const emailError = document.getElementById('email2VeryError');
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email) && email.length > 0) {
            emailError.style.display = 'block';
        } else {
            emailError.style.display = 'none';
        }
    });

    // Validación en tiempo real para coincidencia de número de documento
    document.getElementById('number_id_very').addEventListener('input', function() {
        const number_id = document.getElementById('number_id').value;
        const number_id_very = this.value;
        let errorMsg = document.getElementById('numberIdVeryError');
        if (!errorMsg) {
            errorMsg = document.createElement('small');
            errorMsg.id = 'numberIdVeryError';
            errorMsg.className = 'text-danger';
            this.parentNode.appendChild(errorMsg);
        }
        if (number_id !== number_id_very && number_id_very.length > 0) {
            errorMsg.textContent = 'El número de identificación no coincide.';
            errorMsg.style.display = 'block';
        } else {
            errorMsg.textContent = '';
            errorMsg.style.display = 'none';
        }
    });

    // Validación en tiempo real para coincidencia de email
    document.getElementById('email_very').addEventListener('input', function() {
        const email = document.getElementById('email').value;
        const email_very = this.value;
        let errorMsg = document.getElementById('emailVeryMatchError');
        if (!errorMsg) {
            errorMsg = document.createElement('small');
            errorMsg.id = 'emailVeryMatchError';
            errorMsg.className = 'text-danger';
            this.parentNode.appendChild(errorMsg);
        }
        if (email !== email_very && email_very.length > 0) {
            errorMsg.textContent = 'El correo electrónico no coincide.';
            errorMsg.style.display = 'block';
        } else {
            errorMsg.textContent = '';
            errorMsg.style.display = 'none';
        }
    });

    // El botón de guardado siempre debe mostrarse
    document.getElementById('saveBtn').style.display = 'inline-block';

    // MEJORADO: Manejo del envío del formulario con mejor debugging
    document.getElementById('formCierre').addEventListener('submit', function(e) {
        e.preventDefault();

        // Validaciones básicas antes del envío
        const email = document.getElementById('email').value;
        const emailVery = document.getElementById('email_very').value;
        const numberId = document.getElementById('number_id').value;
        const numberIdVery = document.getElementById('number_id_very').value;

        // Verificar que los emails coincidan
        if (email !== emailVery) {
            Swal.fire('Error', 'Los correos electrónicos no coinciden', 'error');
            return;
        }

        // Verificar que los números de ID coincidan
        if (numberId !== numberIdVery) {
            Swal.fire('Error', 'Los números de identificación no coinciden', 'error');
            return;
        }

        const email2 = document.getElementById('email2').value;
        if (email === email2) {
            Swal.fire('Error', 'El segundo correo electrónico no puede ser igual al primero.', 'error');
            return;
        }

        // Habilitar el campo sede para que se envíe
        const sedeSelect = document.getElementById('sede');
        sedeSelect.disabled = false;

        // Crear FormData
        const formData = new FormData(this);

        // Volver a deshabilitar el campo sede (opcional, por experiencia de usuario)
        sedeSelect.disabled = true;

        // Mostrar loading
        Swal.fire({
            title: 'Guardando...',
            text: 'Por favor espere mientras se procesa su información',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Realizar la petición
        fetch('APIS/register_student/savePreRegis.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);

                // Primero verificar si la respuesta es correcta
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }

                // Intentar obtener el texto de la respuesta para debugging
                return response.text();
            })
            .then(text => {
                console.log('Response text:', text);

                // Intentar parsear como JSON
                try {
                    const data = JSON.parse(text);

                    if (data.success) {
                        Swal.fire({
                            title: '¡Éxito!',
                            text: data.message || 'Registro guardado correctamente.',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            this.reset();
                        });
                    } else {
                        Swal.fire('Error', data.message || 'Ocurrió un error al guardar.', 'error');
                    }
                } catch (jsonError) {
                    console.error('JSON Parse Error:', jsonError);
                    console.error('Response was:', text);
                    Swal.fire({
                        title: 'Error de respuesta',
                        html: `El servidor devolvió una respuesta inválida.<br><small>Respuesta: ${text.substring(0, 200)}...</small>`,
                        icon: 'error'
                    });
                }
            })
            .catch(error => {
                console.error('Network/Server Error:', error);
                Swal.fire({
                    title: 'Error de conexión',
                    html: `No se pudo conectar con el servidor.<br><small>Error: ${error.message}</small>`,
                    icon: 'error'
                });
            });
    });

    // Función para cargar horarios según el programa seleccionado
    function cargarHorarios() {
        const programaSelect = document.getElementById('programa');
        const horarioSelect = document.getElementById('horario');
        const programaSeleccionado = programaSelect.value;

        // Limpiar opciones de horario
        horarioSelect.innerHTML = '<option value="">Seleccione...</option>';

        if (!programaSeleccionado) {
            return;
        }

        // Datos de la sede desde PHP
        const sedeId = <?php echo $sede_id ? $sede_id : 'null'; ?>;
        const sedeName = '<?php echo $sede ? addslashes($sede['name']) : ''; ?>';

        if (!sedeId || !sedeName) {
            return;
        }

        // Hacer petición AJAX para obtener horarios
        fetch('APIS/register_student/getHorarios.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `programa=${encodeURIComponent(programaSeleccionado)}&sede=${encodeURIComponent(sedeName)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.horarios.length > 0) {
                    data.horarios.forEach(horario => {
                        const option = document.createElement('option');
                        option.value = horario.schedule;
                        option.textContent = horario.schedule;
                        horarioSelect.appendChild(option);
                    });
                } else {
                    // No hay horarios disponibles
                    const option = document.createElement('option');
                    option.value = '';
                    option.textContent = 'No hay horarios disponibles para este programa';
                    horarioSelect.appendChild(option);
                }
            })
            .catch(error => {
                console.error('Error cargando horarios:', error);
            });
    }

    // Escuchar cambios en el selector de programa
    document.getElementById('programa').addEventListener('change', cargarHorarios);
</script>