<?php

// Obtener parámetros de la URL
$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : '';
$curso = isset($_GET['curso']) ? $_GET['curso'] : '';
$url_actual = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

// Validar que los parámetros existan
if ($fecha && $curso) {
    // Verificar si la URL está registrada en la tabla qr_mentorias
    $query_url = "SELECT id, clases_equivalentes, authorized FROM qr_mentorias WHERE url = ?";
    $stmt_url = $conn->prepare($query_url);
    $stmt_url->bind_param("s", $url_actual);
    $stmt_url->execute();
    $result_url = $stmt_url->get_result();

    if ($result_url->num_rows > 0) {
        $row_url = $result_url->fetch_assoc();
        $clases_equivalentes = $row_url['clases_equivalentes'];
        $authorized = $row_url['authorized'];

        if ($authorized == 0) {
            // Nivelación no autorizada
            ?>
            <div class="container mt-4">
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <div class="alert alert-warning text-center">
                            <i class="bi bi-exclamation-triangle" style="font-size: 2rem;"></i>
                            <h4 class="mt-3">Mentoria no autorizada</h4>
                            <p>La mentoría aún no ha sido autorizada por el administrador.</p>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        } else {
            // Obtener el nombre del curso desde la tabla courses
            $query_curso = "SELECT name FROM courses WHERE code = ?";
            $stmt_curso = $conn->prepare($query_curso);
            $stmt_curso->bind_param("s", $curso);
            $stmt_curso->execute();
            $result_curso = $stmt_curso->get_result();

            if ($result_curso->num_rows > 0) {
                $curso_info = $result_curso->fetch_assoc();
                $nombre_curso = $curso_info['name'];
?>
                <div class="container mt-4">
                    <div class="row justify-content-center">
                        <div class="col-md-6">
                            <div class="card shadow-lg">
                                <div class="card-header text-center" style="background-color: #30336b; color: white;">
                                    <h3><i class="bi bi-calendar-check"></i> Registro de Asistencia</h3>
                                </div>
                                <div class="card-body d-flex flex-column align-items-center justify-content-center">
                                    <form id="asistenciaForm" class="w-100">
                                        <div class="form-group mb-3 text-center">
                                            <label for="number_id"><i class="bi bi-person-badge"></i> Número de Identificación:</label>
                                            <input type="text" class="form-control text-center" id="number_id" name="number_id" maxlength="15" pattern="\d*" required
                                                placeholder="Ingrese su número de identificación"
                                                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                                style="font-size: 1.8rem; height: 3.8rem;">
                                        </div>
                                        <input type="hidden" name="fecha" value="<?php echo htmlspecialchars($fecha); ?>">
                                        <input type="hidden" name="curso" value="<?php echo htmlspecialchars($curso); ?>">
                                        <input type="hidden" name="clases_equivalentes" value="<?php echo htmlspecialchars($clases_equivalentes); ?>">
                                        <div class="d-flex justify-content-center">
                                            <button type="submit" class="btn" style="background-color: #ec008c; color: white;">
                                                <i class="bi bi-check-circle"></i> Registrar Asistencia
                                            </button>
                                        </div>
                                        <div id="asistenciaMsg" class="mt-3"></div>
                                    </form>

                                    <div class="info-item mb-3 w-100 text-center">
                                        <label class="info-label"><i class="bi bi-book"></i> Mentoría del Bootcamp:</label>
                                        <p class="info-value program-highlight"><?php echo htmlspecialchars($nombre_curso); ?></p>
                                    </div>
                                    <div class="info-item mb-3 w-100 text-center">
                                        <label class="info-label"><i class="bi bi-clock"></i> Fecha:</label>
                                        <p class="info-value" style="font-size: 22px; font-weight: bold;"><?php echo date('d/m/Y', strtotime($fecha)); ?></p>
                                    </div>

                                    <script>
                                        document.getElementById('asistenciaForm').addEventListener('submit', function(e) {
                                            e.preventDefault();

                                            const form = e.target;
                                            const formData = new FormData(form);
                                            const msgDiv = document.getElementById('asistenciaMsg');
                                            const submitBtn = form.querySelector('button[type="submit"]');

                                            // Mostrar estado de carga
                                            submitBtn.disabled = true;
                                            submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Registrando...';
                                            msgDiv.innerHTML = '<div class="alert alert-info text-center"><i class="bi bi-hourglass-split"></i> Procesando registro...</div>';

                                            fetch('APIS/entryInfo/procesarMentoria.php', {
                                                    method: 'POST',
                                                    body: formData
                                                })
                                                .then(response => {
                                                    // Obtener el texto de la respuesta para debug
                                                    return response.text().then(text => {
                                                        console.log('Respuesta del servidor:', text); // Para debug

                                                        // Verificar si la respuesta es JSON válido
                                                        try {
                                                            const data = JSON.parse(text);
                                                            return data;
                                                        } catch (jsonError) {
                                                            console.error('Error parseando JSON:', jsonError);
                                                            console.error('Respuesta recibida:', text);

                                                            // Si contiene HTML, es probable que sea un error de PHP
                                                            if (text.includes('<') || text.includes('Fatal error') || text.includes('Warning')) {
                                                                throw new Error('Error del servidor. Revise los logs del servidor.');
                                                            } else {
                                                                throw new Error('Respuesta del servidor no válida');
                                                            }
                                                        }
                                                    });
                                                })
                                                .then(data => {
                                                    if (data.success) {
                                                        Swal.fire({
                                                            icon: 'success',
                                                            title: '¡Registro exitoso!',
                                                            text: data.message,
                                                            confirmButtonColor: '#30336b'
                                                        });
                                                        msgDiv.innerHTML = '<div class="alert alert-success text-center"><i class="bi bi-check-circle"></i> ' + data.message + '</div>';
                                                        form.reset();
                                                    } else {
                                                        msgDiv.innerHTML = '<div class="alert alert-danger text-center"><i class="bi bi-x-circle"></i> ' + data.message + '</div>';
                                                        Swal.fire({
                                                            icon: 'error',
                                                            title: 'Error en el registro',
                                                            text: data.message,
                                                            confirmButtonColor: '#30336b'
                                                        });
                                                    }
                                                })
                                                .catch(error => {
                                                    console.error('Error completo:', error);
                                                    const errorMsg = 'Error al registrar la asistencia. Por favor, intente nuevamente.';

                                                    msgDiv.innerHTML = '<div class="alert alert-danger text-center"><i class="bi bi-x-circle"></i> ' + errorMsg + '</div>';

                                                    Swal.fire({
                                                        icon: 'error',
                                                        title: '¡Error!',
                                                        text: errorMsg,
                                                        footer: 'Si el problema persiste, contacte al administrador.',
                                                        confirmButtonColor: '#30336b'
                                                    });
                                                })
                                                .finally(() => {
                                                    // Restaurar el botón
                                                    submitBtn.disabled = false;
                                                    submitBtn.innerHTML = '<i class="bi bi-check-circle"></i> Registrar Asistencia';
                                                });
                                    });
                                </script>
                            </div>
                        </div>
                    </div>
                </div>
                <style>
                    .info-item {
                        background-color: #f8f9fa;
                        padding: 15px;
                        border-radius: 8px;
                        border-left: 4px solid #30336b;
                        height: 100%;
                    }

                    .info-label {
                        font-weight: bold;
                        color: #30336b;
                        margin-bottom: 5px;
                        display: block;
                        font-size: 14px;
                    }

                    .info-value {
                        margin: 0;
                        color: #333;
                        font-size: 16px;
                        word-wrap: break-word;
                    }

                    .program-highlight {
                        background: linear-gradient(135deg, #30336b, #30336b);
                        color: white;
                        padding: 10px;
                        border-radius: 5px;
                        text-align: center;
                        font-weight: bold;
                        font-size: 18px;
                    }

                    .card {
                        border: none;
                        border-radius: 15px;
                    }

                    .card-header {
                        border-top-left-radius: 15px;
                        border-top-right-radius: 15px;
                        border-bottom: none;
                    }
                </style>
            <?php
            } else {
                // Curso no encontrado
            ?>
                <div class="container mt-4">
                    <div class="row justify-content-center">
                        <div class="col-md-6">
                            <div class="alert alert-warning text-center">
                                <i class="bi bi-exclamation-triangle" style="font-size: 2rem;"></i>
                                <h4 class="mt-3">Mentoría no encontrada</h4>
                                <p>No se encontró información para el curso <strong><?php echo htmlspecialchars($curso); ?></strong></p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
            }
            $stmt_curso->close();
        }
    } else {
        // URL no registrada
        ?>
        <div class="container mt-4">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="alert alert-danger text-center">
                        <i class="bi bi-x-circle" style="font-size: 2rem;"></i>
                        <h4 class="mt-3">Mentoría inválida</h4>
                        <p>La URL de esta mentoría no está registrada.</p>
                        <hr>
                        <small class="text-muted">Verifique que el enlace sea correcto.</small>
                    </div>
                </div>
            </div>
        </div>
    <?php
    }
    $stmt_url->close();
} else {
    // Parámetros faltantes
    ?>
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="alert alert-info text-center">
                    <i class="bi bi-info-circle" style="font-size: 2rem;"></i>
                    <h4 class="mt-3">Registro de Asistencia</h4>
                    <p>Agregue los parámetros <code>fecha</code> y <code>curso</code> a la URL para registrar asistencia.</p>
                    <hr>
                    <small class="text-muted">
                        Ejemplo: <code>mentoriaAttendance.php?fecha=2025-08-14&curso=492</code>
                    </small>
                </div>
            </div>
        </div>
    </div>
<?php
}
?>