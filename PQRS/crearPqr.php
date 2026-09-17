<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario PQRS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/stylePqr.css?v=1"> 
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="icon" href="assets/img/favicon.png" type="image/x-icon">
</head>

<body>

    <div id="vanta-bg"></div>

    <!-- Header con imagen -->
    <header class="header">
        <div class="container">
            <img src="../img/banner_pqrs.webp" alt="banner top pqrs" class="w-100 rounded mt-3">
        </div>
    </header>

    <div class="container">
        <div class="glass-form p-4 rounded">
            <h2 class="subTitle">Crear PQRS</h2>
            <hr>
            <p>Por favor, llena el siguiente formulario para realizar una petición, queja, reclamo o sugerencia. <strong
                    class="subtitle">Todos los campos son obligatorios.</strong></p>

            <form action="procesar_pqr.php" method="POST" enctype="multipart/form-data" id="formularioPQR" novalidate>
                <input type="hidden" name="formType" value="pqrForm">

                <div class="row g-3">
                    <!-- Primera columna -->
                    <div class="col-md-6">
                        <div id="camposAnonimos">
                            <div class="mb-3">
                                <label for="fecha_registro" class="form-label">Fecha de Registro:</label>
                                <input type="date" class="form-control" id="fecha_registro" name="fecha_registro" required>
                            </div>
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre:</label>
                                <input type="text" class="form-control" id="nombre" name="nombre"
                                    placeholder="Escribe tu nombre" required>
                            </div>
                            <div class="mb-3">
                                <label for="cedula" class="form-label">Cédula:</label>
                                <input type="number" class="form-control" id="cedula" name="cedula"
                                    placeholder="Escribe tu cédula" required>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Correo:</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    placeholder="Escribe tu email" required>
                            </div>
                            <div class="mb-3">
                                <label for="telefono1" class="form-label">Teléfono principal:</label>
                                <input type="number" class="form-control" id="telefono1" name="telefono1"
                                    placeholder="Escribe tu teléfono principal" required>
                            </div>


                            <!-- Botón de envío en la primera columna -->
                            <button type="submit" class="btn bg-indigo-dark mt-3">Enviar PQRS</button>
                        </div>
                    </div>

                    <!-- Segunda columna -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="telefono2" class="form-label">Teléfono secundario:</label>
                            <input type="number" class="form-control" id="telefono2" name="telefono2"
                                placeholder="Escribe tu teléfono secundario" required>
                        </div>
                        <div class="mb-3">
                            <label for="tipo" class="form-label">Tipo:</label>
                            <select class="form-select" id="tipo" name="tipo" required>
                                <option value="">Seleccione una opción</option>
                                <option value="Petición">Petición</option>
                                <option value="Queja">Queja</option>
                                <option value="Reclamo">Reclamo</option>
                                <option value="Sugerencia">Sugerencia</option>
                                <option value="Simple">Solicitud simple</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="asunto" class="form-label">Asunto:</label>
                            <input type="text" class="form-control" id="asunto" name="asunto"
                                placeholder="Escribe el asunto" required>
                        </div>
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción:</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="5"
                                placeholder="Escribe la descripción (máx. 250 caracteres)" maxlength="250"
                                required></textarea>
                            <small id="contadorDescripcion" class="form-text text-muted">250 caracteres
                                restantes</small>
                        </div>
                    </div>
                </div>
            </form>
        </div>

    </div>

    <!-- Footer con imagen -->
    <footer class="footer">
        <div class="container">
            <img src="../img/footer_pqrs.webp" alt="banner bottom pqrs" class="w-100 rounded mb-3">
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        //PARA LOS CARACTERES RESTANTES 
        document.addEventListener("DOMContentLoaded", function() {
            let descripcion = document.getElementById("descripcion");
            let contador = document.getElementById("contadorDescripcion");

            descripcion.addEventListener("input", function() {
                let caracteresRestantes = 250 - descripcion.value.length;
                contador.textContent = caracteresRestantes + " caracteres restantes";

                if (caracteresRestantes < 0) {
                    descripcion.value = descripcion.value.substring(0, 250); // Corta el texto si excede el límite
                    contador.textContent = "0 caracteres restantes";
                }
            });
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const formulario = document.getElementById("formularioPQR");
            const urlProcesarPqr = 'procesar_pqr.php'; // URL para procesar el formulario

            // Función para mostrar mensajes de error en los campos
            function mostrarError(campo, mensaje) {
                campo.classList.add("is-invalid");
                let mensajeError = campo.closest('.mb-3')?.querySelector('.invalid-feedback');
                if (mensajeError) mensajeError.textContent = mensaje;
            }

            // Función para eliminar mensajes de error de los campos
            function eliminarError(campo) {
                campo.classList.remove("is-invalid");
                campo.classList.remove("is-valid");
                let mensajeError = campo.closest('.mb-3')?.querySelector('.invalid-feedback');
                if (mensajeError) mensajeError.textContent = "";
            }

            // Función para validar campos vacíos
            function validarCamposVacios(campos) {
                let camposFaltantes = [];
                let formularioValido = true;

                campos.forEach(campo => {
                    eliminarError(campo); // Limpiar errores anteriores
                    if (!campo.value.trim()) {
                        mostrarError(campo, campo.getAttribute("data-error") || "Este campo es obligatorio.");
                        camposFaltantes.push(campo.getAttribute("placeholder") || campo.name); // Guarda el nombre del campo
                        formularioValido = false;
                    } else {
                        campo.classList.add("is-valid");
                    }
                });

                return {
                    formularioValido,
                    camposFaltantes
                };
            }

            // Función para validar formato de email
            function validarEmail(emailCampo) {
                let email = emailCampo.value.trim();
                let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                if (!emailRegex.test(email)) {
                    mostrarError(emailCampo, "Ingrese un email válido.");
                    return false;
                }

                return true;
            }

            // Función para validar formato de teléfono
            function validarTelefono(telefonoCampo) {
                let telefono = telefonoCampo.value.trim();
                let telefonoRegex = /^[0-9]{7,}$/; // Ejemplo: al menos 7 dígitos

                if (!telefonoRegex.test(telefono)) {
                    mostrarError(telefonoCampo, "Ingrese un teléfono válido (solo números, mínimo 7 dígitos).");
                    return false;
                }

                return true;
            }

            // Función para mostrar SweetAlert con los campos faltantes
            function mostrarModalCamposFaltantes(camposFaltantes) {
                Swal.fire({
                    icon: "warning",
                    title: "Campos incompletos",
                    html: `
                        <p>Por favor, complete los siguientes campos antes de enviar:</p>
                        <ul style="text-align: left; padding-left: 20px;">
                            ${camposFaltantes.map(campo => `<li>${campo}</li>`).join("")}
                        </ul>
                    `,
                    confirmButtonText: "Entendido",
                });
            }

            // Evento submit del formulario
            formulario.addEventListener("submit", function(event) {
                event.preventDefault(); // Evita recargar la página

                // Verificar si ya se está procesando una solicitud
                if (formulario.dataset.processing === 'true') {
                    return; // Salir si ya se está procesando
                }

                // Validar campos
                let campos = formulario.querySelectorAll("input, select, textarea");
                let {
                    formularioValido,
                    camposFaltantes
                } = validarCamposVacios(campos);

                // Validar formato de email
                let emailCampo = document.getElementById('email');
                if (formularioValido && !validarEmail(emailCampo)) {
                    formularioValido = false;
                    camposFaltantes.push("Correo electrónico");
                }

                // Validar formato de teléfono
                let telefono1Campo = document.getElementById('telefono1');
                if (formularioValido && !validarTelefono(telefono1Campo)) {
                    formularioValido = false;
                    camposFaltantes.push("Teléfono");
                }

                // Si hay campos faltantes, mostrar modal y detener el envío
                if (!formularioValido) {
                    mostrarModalCamposFaltantes(camposFaltantes);
                    return;
                }

                // Marcar como en procesamiento
                formulario.dataset.processing = 'true';

                // Deshabilitar el botón de envío
                const submitButton = formulario.querySelector('button[type="submit"]');
                const originalButtonText = submitButton.innerHTML;
                submitButton.disabled = true;
                submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Enviando...';

                // Mostrar loader con SweetAlert
                Swal.fire({
                    title: 'Procesando PQRS...',
                    html: 'Por favor espere mientras procesamos su solicitud.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    allowEnterKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                console.log('Enviando formulario...');

                // Realizar la petición fetch
                fetch(urlProcesarPqr, {
                        method: 'POST',
                        body: new FormData(formulario)
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log("Datos procesados: ", data);

                        // Cerrar el loader
                        Swal.close();

                        if (data.success) {
                            const numero_radicado = data.data.numero_radicado;
                            console.log("Número de radicado recibido:", numero_radicado);

                            Swal.fire({
                                title: "¡Éxito!",
                                html: `
                                    <i class="fa fa-check-circle text-success" style="font-size: 40px;"></i>
                                    <strong>Número de radicado:</strong> ${numero_radicado}
                                    <br>
                                    <button class="btn btn-sm btn-outline-secondary mt-2" onclick="copyToClipboard('${numero_radicado}')">Copiar</button>
                                `,
                                icon: "success",
                                confirmButtonText: "Aceptar"
                            }).then(() => {
                                window.location.href = 'crearPqr.php';
                            });
                        } else {
                            console.error("Error en la respuesta del servidor:", data.message);
                            Swal.fire({
                                icon: "error",
                                title: "¡Error!",
                                text: data.message || "Hubo un problema al procesar la solicitud.",
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error en la petición fetch:', error);

                        // Cerrar el loader en caso de error
                        Swal.close();

                        Swal.fire({
                            icon: 'error',
                            title: '¡Error!',
                            text: 'Hubo un problema al enviar el formulario, intenta nuevamente.',
                        });
                    })
                    .finally(() => {
                        // Restaurar el estado del formulario
                        formulario.dataset.processing = 'false';
                        submitButton.disabled = false;
                        submitButton.innerHTML = originalButtonText;
                    });
            });

            // Evento input para remover errores en tiempo real
            formulario.querySelectorAll("input, select, textarea").forEach(campo => {
                campo.addEventListener("input", () => {
                    eliminarError(campo); // Limpiar errores al escribir
                });
            });
        });

        // Función para copiar el número de radicado al portapapeles
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text)
                .then(() => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Copiado',
                        text: 'Número de radicado copiado al portapapeles.',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 4000
                    });
                })
                .catch(err => {
                    console.error('Error al copiar: ', err);
                });
        }
    </script>

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