<script src="node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Obtenemos los elementos de los campos de correo y los mensajes de validación
    const emailInput = document.getElementById('email');
    const verifyInput = document.getElementById('email_very');
    const emailMessage = document.getElementById('emailMessage');
    const verifyMessage = document.getElementById('verifyMessage');

    // Función para validar que los correos sean correctos
    emailInput.addEventListener('input', function() {
        const email = emailInput.value;
        const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;

        // Validación de formato de correo
        if (!emailPattern.test(email)) {
            emailMessage.textContent = "Por favor, ingresa un correo electrónico válido.";
            emailMessage.style.color = "red";
        } else {
            emailMessage.textContent = "";
        }
    });

    // Función para verificar que los correos coincidan
    verifyInput.addEventListener('input', function() {
        const email = emailInput.value;
        const verifyEmail = verifyInput.value;

        if (email !== verifyEmail) {
            verifyMessage.textContent = "Los correos no coinciden.";
            verifyMessage.style.color = "red";
        } else if (verifyEmail === "") {
            verifyMessage.textContent = "";
        } else {
            verifyMessage.textContent = "Los correos coinciden.";
            verifyMessage.style.color = "green";
        }
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const vulnerablePopulation = document.getElementById('vulnerable_population');
        const vulnerableType = document.getElementById('vulnerable_type');

        // Función para actualizar el campo 'vulnerable_type' basado en 'vulnerable_population'
        function updateVulnerableType() {
            if (vulnerablePopulation.value === 'Sí') {
                // Si selecciona "Sí", habilitamos 'vulnerable_type' con opciones
                vulnerableType.removeAttribute('enable');
                vulnerableType.value = ''; // Limpiar el valor cuando se habilite
            } else if (vulnerablePopulation.value === 'No') {
                // Si selecciona "No", ponemos 'vulnerable_type' en solo lectura y con valor 'No aplica'
                vulnerableType.setAttribute('enable', 'enable');
                vulnerableType.value = 'No aplica'; // Establecemos el valor por defecto
            } else {
                // Si no se ha seleccionado, lo dejamos con el valor inicial
                vulnerableType.setAttribute('enable', 'enable');
                vulnerableType.value = ''; // Limpiar el valor si no se ha seleccionado nada
            }
        }

        // Ejecutamos la función al cargar la página y cuando el valor de 'vulnerable_population' cambie
        updateVulnerableType();
        vulnerablePopulation.addEventListener('change', updateVulnerableType);
    });
</script>

<script>
    function updateMotivaciones() {
        var selectedMotivaciones = [];
        // Seleccionar checkboxes con el atributo name='selectedMotivaciones'
        var checkboxes = document.querySelectorAll('input[name="selectedMotivaciones"]:checked');
        checkboxes.forEach(function(checkbox) {
            selectedMotivaciones.push(checkbox.value);
        });
        // Actualizar el campo de texto con los valores seleccionados
        document.getElementById('motivations_belong_program').value = selectedMotivaciones.join(', ');
    }

    function updateSituacionesActuales() {
        var selectedSituations = [];
        // Seleccionar checkboxes con el atributo name='selectedSituations'
        var checkboxes = document.querySelectorAll('input[name="selectedSituations"]:checked');
        checkboxes.forEach(function(checkbox) {
            selectedSituations.push(checkbox.value);
        });
        // Actualizar el campo de texto con los valores seleccionados
        document.getElementById('current_situation').value = selectedSituations.join(', ');
    }
</script>
<script>
    // Función para actualizar el campo 'languages_level'
    function updateLanguageLevel() {
        var selectedLanguage = document.getElementById('languages').value; // Obtiene el valor del idioma seleccionado
        var languageLevelField = document.getElementById('languages_level'); // Obtiene el campo de nivel de idioma

        // Si no se selecciona ningún idioma o se selecciona "Ningúno", deshabilitar el campo y poner "No aplica"
        if (selectedLanguage === '' || selectedLanguage === 'Ningúno') {
            languageLevelField.value = 'No aplica'; // Cambiar el valor a "No aplica"
        } else {
            languageLevelField.disabled = false; // Habilitar el campo
            languageLevelField.value = ''; // Restablecer el valor a vacío para permitir que el usuario seleccione el nivel
        }
    }

    // Llamar a la función cuando cambie la selección en 'languages'
    document.getElementById('languages').addEventListener('change', updateLanguageLevel);

    // Llamar a la función al cargar la página por si ya tiene un valor predefinido
    window.onload = updateLanguageLevel;
</script>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dayField = document.getElementById('dia_nacimiento');
        const monthField = document.getElementById('mes_nacimiento');
        const yearField = document.getElementById('anio_nacimiento');
        const hiddenField = document.getElementById('birthdate_hidden');

        function updateBirthdate() {
            const day = dayField.value.padStart(2, '0'); // Asegurar dos dígitos
            const month = monthField.value.padStart(2, '0'); // Asegurar dos dígitos
            const year = yearField.value;

            if (day && month && year) {
                hiddenField.value = `${year}-${month}-${day}`;
            } else {
                hiddenField.value = ''; // Vaciar si falta algún valor
            }
        }

        // Agregar eventos a los selectores
        dayField.addEventListener('change', updateBirthdate);
        monthField.addEventListener('change', updateBirthdate);
        yearField.addEventListener('change', updateBirthdate);
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dayField = document.getElementById('expedition_day');
        const monthField = document.getElementById('expedition_month');
        const yearField = document.getElementById('expedition_year');
        const hiddenField = document.getElementById('expedition_hidden');

        function updateExpeditionDate() {
            const day = dayField.value.padStart(2, '0'); // Asegurar dos dígitos
            const month = monthField.value.padStart(2, '0'); // Asegurar dos dígitos
            const year = yearField.value;

            if (day && month && year) {
                hiddenField.value = `${year}-${month}-${day}`;
            } else {
                hiddenField.value = ''; // Vaciar si falta algún valor
            }
        }

        // Agregar eventos a los selectores
        dayField.addEventListener('change', updateExpeditionDate);
        monthField.addEventListener('change', updateExpeditionDate);
        yearField.addEventListener('change', updateExpeditionDate);
    });
</script>

<script>
    function showToast(type, message) {
        // Crear el elemento del toast
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        toast.style.padding = '10px 20px';
        toast.style.marginBottom = '10px';
        toast.style.color = '#fff';
        toast.style.borderRadius = '5px';
        toast.style.fontSize = '16px';
        toast.style.display = 'inline-block';
        toast.style.animation = 'fade-in-out 4s ease forwards';
        toast.style.position = 'relative';

        // Definir el color de fondo según el tipo
        if (type === 'success') {
            toast.style.backgroundColor = '#66cc00';
        } else if (type === 'error') {
            toast.style.backgroundColor = '#f5a6c2';
        } else if (type === 'warning') {
            toast.style.backgroundColor = '#f39c12'; // Color para advertencia
        }

        // Definir el texto del toast
        toast.innerText = message;

        // Añadir el toast al contenedor
        document.getElementById('toast-container').appendChild(toast);

        // Eliminar el toast después de 4 segundos
        setTimeout(() => {
            toast.remove();
        }, 4000);
    }
</script>
<script>
    // Example starter JavaScript for disabling form submissions if there are invalid fields
    (() => {
        'use strict'

        // Fetch all the forms we want to apply custom Bootstrap validation styles to
        const forms = document.querySelectorAll('.needs-validation')

        // Loop over them and prevent submission
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }

                form.classList.add('was-validated')
            }, false)
        })
    })()
</script>

<!-- Cargar jQuery antes de tu archivo JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Reemplaza los iconos por defecto de SweetAlert2 por Bootstrap Icons (naranja)
    (function () {
        if (typeof Swal === 'undefined') {
            return;
        }

        var ICONS = {
            success: 'bi-check-circle-fill',
            error: 'bi-x-circle-fill',
            warning: 'bi-exclamation-triangle-fill',
            info: 'bi-info-circle-fill',
            question: 'bi-question-circle-fill'
        };

        function iconHtmlFor(type) {
            var cls = ICONS[type];
            return cls ? '<i class="bi ' + cls + '" aria-hidden="true"></i>' : '';
        }

        var originalFire = Swal.fire.bind(Swal);

        Swal.fire = function () {
            var args = Array.prototype.slice.call(arguments);

            // Swal.fire(opciones)
            if (args.length === 1 && typeof args[0] === 'object' && args[0] !== null) {
                if (args[0].icon && ICONS[args[0].icon] && !args[0].iconHtml) {
                    args[0].iconHtml = iconHtmlFor(args[0].icon);
                }
            } else if (args.length >= 3) {
                // Swal.fire(title, text, icon)
                var iconArg = args[2];
                if (typeof iconArg === 'string' && ICONS[iconArg]) {
                    args[2] = { icon: iconArg, iconHtml: iconHtmlFor(iconArg) };
                }
            }

            return originalFire.apply(Swal, args);
        };
    })();
</script>
<script>
        document.addEventListener('DOMContentLoaded', function () {
            const apiUrl = 'https://www.datos.gov.co/resource/gdxc-w37w.json';
            let globalData = [];

            async function fetchApiData() {
                try {
                    const response = await fetch(apiUrl);
                    if (!response.ok) throw new Error(`Error: ${response.statusText}`);
                    const data = await response.json();
                    globalData = data;
                    cargarDepartamento(data);
                } catch (error) {
                    console.error('Error al obtener los datos:', error);
                }
            }

            function cargarDepartamento(data) {
                const selectDepto = document.getElementById('lista_departamento');
                if (selectDepto && selectDepto.dataset.populated === 'true') {
                    return;
                }
                selectDepto.innerHTML = '<option value="">Seleccione un departamento</option>';

                const departamento = data.find(item => item.cod_dpto === "11");

                if (departamento) {
                    const option = document.createElement('option');
                    option.value = departamento.cod_dpto;
                    option.textContent = departamento.dpto;
                    selectDepto.appendChild(option);

                    cargarMunicipios(departamento.cod_dpto);
                }

                selectDepto.addEventListener('change', function () {
                    cargarMunicipios(this.value);
                });
            }

            function cargarMunicipios(codDpto) {
                const selectMunicipios = document.getElementById('municipios');
                selectMunicipios.innerHTML = '<option value="">Seleccione un municipio</option>';

                const municipios = globalData.filter(item => item.cod_dpto === codDpto);
                const codigosUnicos = new Set();

                municipios.forEach(item => {
                    const codigo = item.cod_mpio?.trim();
                    const nombre = item.nom_mpio?.trim();

                    if (codigo && nombre && !codigosUnicos.has(codigo)) {
                        codigosUnicos.add(codigo);
                        const option = document.createElement('option');
                        option.value = codigo;
                        option.textContent = nombre;
                        selectMunicipios.appendChild(option);
                    }
                });

                // Escuchar selección de municipio y mostrar alert
                selectMunicipios.addEventListener('change', function () {
                    const selectedCode = this.value;
                    const selectedText = this.options[this.selectedIndex].text;
               
                });
            }

            fetchApiData();
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const yearField = document.getElementById('anio_nacimiento');
            const diaField = document.getElementById('dia_nacimiento');
            const mesField = document.getElementById('mes_nacimiento');
            const typeSelect = document.querySelector('[name="typeID"]');

            function calcularEdad(dia, mes, anio) {
                const hoy = new Date();
                const nacimiento = new Date(anio, mes - 1, dia);
                let edad = hoy.getFullYear() - nacimiento.getFullYear();
                const m = hoy.getMonth() - nacimiento.getMonth();
                if (m < 0 || (m === 0 && hoy.getDate() < nacimiento.getDate())) {
                    edad--;
                }
                return edad;
            }

            function limpiarFechaNacimiento() {
                if (diaField) diaField.value = '';
                if (mesField) mesField.value = '';
                if (yearField) yearField.value = '';
                const hiddenField = document.getElementById('birthdate_hidden');
                if (hiddenField) hiddenField.value = '';
                limpiarCamposAcudiente();
            }

            function limpiarCamposAcudiente() {
                ['guardian_full_name', 'guardian_document', 'guardian_phone', 'guardian_email'].forEach(function(id) {
                    const el = document.getElementById(id);
                    if (el) el.value = '';
                });
            }

            function abrirModalAcudiente() {
                Swal.fire({
                    title: 'Datos del acudiente',
                    text: 'Por ser menor de edad, necesitamos la información de tu acudiente.',
                    customClass: {
                        popup: 'swal-acudiente-popup',
                        confirmButton: 'swal-acudiente-confirm',
                        cancelButton: 'swal-acudiente-cancel'
                    },
                    html: `
                        <style>
                            .swal-acudiente-popup {
                                background: rgba(255, 255, 255, 0.45);
                                backdrop-filter: blur(12px);
                                -webkit-backdrop-filter: blur(12px);
                                border: 2px solid rgba(25, 58, 112, 0.18);
                                border-radius: 16px;
                                color: #193A70;
                            }
                            .swal-acudiente-popup .swal2-title {
                                color: #193A70;
                            }
                            .swal-acudiente-popup .swal2-html-container {
                                color: #193A70;
                                margin: 0.5em 1.6em 0.8em;
                            }
                            .swal-acudiente-form {
                                text-align: left;
                                width: 100%;
                            }
                            .swal-acudiente-form label {
                                display: block;
                                color: #193A70;
                                font-weight: 600;
                                font-size: 14px;
                                margin: 10px 0 2px;
                            }
                            .swal-acudiente-form input {
                                display: block;
                                width: 100%;
                                box-sizing: border-box;
                                background: rgba(255, 255, 255, 0.9);
                                border: 1px solid rgba(25, 58, 112, 0.25);
                                color: #1a1a1a;
                                border-radius: 8px;
                                padding: 10px 12px;
                                font-size: 14px;
                                transition: border-color .15s ease, box-shadow .15s ease;
                            }
                            .swal-acudiente-form input::placeholder {
                                color: rgba(26, 26, 26, 0.45);
                            }
                            .swal-acudiente-form input:focus {
                                outline: none;
                                background: #ffffff;
                                border-color: #193A70;
                                box-shadow: 0 0 0 0.2rem rgba(25, 58, 112, 0.2);
                            }
                            .swal-acudiente-confirm {
                                background: #193A70;
                                color: #F9B233;
                            }
                            .swal-acudiente-cancel {
                                background: transparent;
                                border: 1px solid rgba(25, 58, 112, 0.4);
                                color: #193A70;
                            }
                        </style>
                        <div class="swal-acudiente-form">
                            <label for="swal_guardian_name">Nombre completo del acudiente <span class="text-danger">*</span></label>
                            <input id="swal_guardian_name" placeholder="Nombre completo" autocomplete="off">

                            <label for="swal_guardian_document">Número de documento del acudiente <span class="text-danger">*</span></label>
                            <input id="swal_guardian_document" placeholder="Número de documento" inputmode="numeric" oninput="this.value=this.value.replace(/[^0-9]/g,'')">

                            <label for="swal_guardian_phone">Teléfono del acudiente <span class="text-danger">*</span></label>
                            <input id="swal_guardian_phone" placeholder="321 1234567" inputmode="numeric" maxlength="10" oninput="this.value=this.value.replace(/[^0-9]/g,'')">

                            <label for="swal_guardian_email">Correo electrónico del acudiente (opcional)</label>
                            <input id="swal_guardian_email" placeholder="correo@ejemplo.com">
                        </div>`,
                    focusConfirm: false,
                    showCancelButton: true,
                    confirmButtonText: 'Guardar acudiente',
                    cancelButtonText: 'Cambiar fecha',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    preConfirm: function() {
                        const nombre = document.getElementById('swal_guardian_name').value.trim();
                        const documento = document.getElementById('swal_guardian_document').value.trim();
                        const telefono = document.getElementById('swal_guardian_phone').value.trim();
                        const correo = document.getElementById('swal_guardian_email').value.trim();

                        if (!nombre) {
                            Swal.showValidationMessage('Ingresa el nombre completo del acudiente');
                            return;
                        }
                        if (!documento) {
                            Swal.showValidationMessage('Ingresa el número de documento del acudiente');
                            return;
                        }
                        if (telefono.length !== 10) {
                            Swal.showValidationMessage('El teléfono debe tener 10 dígitos');
                            return;
                        }
                        if (correo && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correo)) {
                            Swal.showValidationMessage('Ingresa un correo válido o déjalo vacío');
                            return;
                        }
                        return { nombre: nombre, documento: documento, telefono: telefono, correo: correo };
                    }
                }).then(function(result) {
                    if (result.isConfirmed) {
                        document.getElementById('guardian_full_name').value = result.value.nombre;
                        document.getElementById('guardian_document').value = result.value.documento;
                        document.getElementById('guardian_phone').value = result.value.telefono;
                        document.getElementById('guardian_email').value = result.value.correo;
                    } else {
                        limpiarFechaNacimiento();
                    }
                });
            }

            function validarEdadTipo() {
                if (!yearField || !yearField.value) return;

                const anio = parseInt(yearField.value, 10);
                if (isNaN(anio)) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Año inválido',
                        text: 'Por favor, ingresa un año válido.',
                        confirmButtonText: 'Entendido'
                    });
                    return;
                }

                let edad;
                if (diaField && diaField.value && mesField && mesField.value) {
                    edad = calcularEdad(parseInt(diaField.value, 10), parseInt(mesField.value, 10), anio);
                } else {
                    edad = new Date().getFullYear() - anio;
                }

                const tipo = typeSelect ? typeSelect.value : '';

                if (edad < 7) {
                    limpiarFechaNacimiento();
                    Swal.fire({
                        icon: 'error',
                        title: 'Edad no válida',
                        text: 'Debes tener al menos 7 años para inscribirte.',
                        confirmButtonText: 'Entendido'
                    });
                    return;
                }

                if ((tipo === 'CC' || tipo === 'CE') && edad < 18) {
                    limpiarFechaNacimiento();
                    Swal.fire({
                        icon: 'error',
                        title: 'Tipo de documento no válido',
                        text: 'Para C.C o C.E debes ser mayor de 18 años.',
                        confirmButtonText: 'Entendido'
                    });
                    return;
                }

                if (tipo === 'TI' && edad >= 18) {
                    limpiarFechaNacimiento();
                    Swal.fire({
                        icon: 'error',
                        title: 'Tipo de documento no válido',
                        text: 'Para T.I debes ser menor de 18 años.',
                        confirmButtonText: 'Entendido'
                    });
                    return;
                }

                if (edad < 18 && (tipo === 'TI' || tipo === 'PPT')) {
                    abrirModalAcudiente();
                } else {
                    limpiarCamposAcudiente();
                }
            }

            if (yearField) yearField.addEventListener('change', validarEdadTipo);
            if (typeSelect) typeSelect.addEventListener('change', validarEdadTipo);
        });
    </script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const typeSelect = document.querySelector('[name="typeID"]');
        const nationalitySelect = document.querySelector('[name="nationality"]');
        if (!typeSelect || !nationalitySelect) return;

        function updateNationality() {
            const tipo = typeSelect.value;
            if (tipo === 'CE' || tipo === 'PPT') {
                nationalitySelect.innerHTML = '<option value="Venezolana" selected>Venezolana</option>';
            } else {
                nationalitySelect.innerHTML = `
                    <option value="">Seleccione</option>
                    <option value="Colombiana">Colombiana</option>
                `;
            }
        }

        typeSelect.addEventListener('change', updateNationality);
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const expeditionYearField = document.getElementById('expedition_year');
        const birthdateField = document.getElementById('birthdate_hidden');

        function validateExpeditionYear() {
            const expeditionYear = parseInt(expeditionYearField.value, 10);
            const birthdateValue = birthdateField.value;
            const birthdate = new Date(birthdateValue);

            if (!isNaN(expeditionYear) && birthdateValue && birthdate instanceof Date && !isNaN(birthdate)) {
                const birthYear = birthdate.getFullYear();
                const ageAtExpedition = expeditionYear - birthYear;

                const hoy = new Date();
                let edad = hoy.getFullYear() - birthdate.getFullYear();
                const m = hoy.getMonth() - birthdate.getMonth();
                if (m < 0 || (m === 0 && hoy.getDate() < birthdate.getDate())) {
                    edad--;
                }
                const esMenor = edad < 18;
                const minimo = esMenor ? 7 : 18;

                if (ageAtExpedition < 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Fecha de expedición no válida',
                        text: 'La fecha de expedición no puede ser anterior a tu fecha de nacimiento.',
                        confirmButtonText: 'Entendido'
                    });
                } else if (ageAtExpedition < minimo) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Fecha de expedición no válida',
                        text: esMenor
                            ? 'Para menores de edad, la fecha de expedición debe ser al menos 7 años después del nacimiento.'
                            : 'Para mayores de edad, la fecha de expedición debe ser al menos 18 años después del nacimiento.',
                        confirmButtonText: 'Entendido'
                    });
                }
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Datos inválidos',
                    text: 'Por favor, verifica la fecha de nacimiento y el año de expedición.',
                    confirmButtonText: 'Entendido'
                });
            }
        }

        expeditionYearField.addEventListener('input', validateExpeditionYear);
    });
</script>