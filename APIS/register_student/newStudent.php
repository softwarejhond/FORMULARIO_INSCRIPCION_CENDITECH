<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require 'vendor/autoload.php';
require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/phpmailer/src/SMTP.php';
require 'vendor/phpmailer/phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// La modalidad siempre es virtual y la sede es "No aplica"
$sede_id = null;
$institucion_param = null;
$instituciones_especiales = [];

$selectedHeadquarter = [
    'id' => null,
    'name' => 'No aplica',
    'mode' => 'Virtual'
];
$headquarterPassword = null;
$selectedMode = 'Virtual';

// Los programas se cargan desde la configuración estática (process_form_register.php).
// Los horarios ya no son necesarios, se guardan como cadena vacía.

// Cargar todos los departamentos
$departamentos = [];
$queryDepartamentos = "SELECT id_departamento, departamento FROM departamentos WHERE id_departamento = 5 ORDER BY departamento";
$resultDepartamentos = $conn->query($queryDepartamentos);
if ($resultDepartamentos && $resultDepartamentos->num_rows > 0) {
    while ($row = $resultDepartamentos->fetch_assoc()) {
        $departamentos[] = $row;
    }
}

// Cargar todas las comunas/corregimientos
$comunas = [];
$queryComunas = "SELECT codigo, nombre FROM comunas_corregimientos WHERE nombre IS NOT NULL AND nombre <> '' ORDER BY codigo";
$resultComunas = $conn->query($queryComunas);
if ($resultComunas && $resultComunas->num_rows > 0) {
    while ($row = $resultComunas->fetch_assoc()) {
        $comunas[] = $row;
    }
}

$formConfig = include 'process_form_register.php'; // Asegúrate de que el archivo de configuración esté correcto.

$tableName = "user_register";
$columnsQuery = "SHOW COLUMNS FROM $tableName";
$columnsResult = $conn->query($columnsQuery);

if (!$columnsResult) {
    die("Error al obtener columnas: " . $conn->error);
}

function generateAttributes($attributes)
{
    $html = '';
    foreach ($attributes as $key => $value) {
        $html .= "$key=\"$value\" ";
    }
    return $html;
}

function normalizarTexto($texto)
{
    $texto = trim($texto);
    $texto = strtr($texto, [
        'á' => 'A', 'é' => 'E', 'í' => 'I', 'ó' => 'O', 'ú' => 'U', 'ü' => 'U',
        'Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U', 'Ü' => 'U'
    ]);
    return mb_strtoupper($texto, 'UTF-8');
}

$includeFields = $formConfig['include_fields'] ?? [];
$fieldsPerStep = 18; // 17 campos en total por paso (solo una columna)
?>

<div class=" p-3">

    <?php
    if (isset($_POST['submit'])) {
        // Variables del formulario
        $typeID = $_POST['typeID'] ?? '';
        $number_id = $_POST['number_id'] ?? '';
        $number_id_very = $_POST['number_id_very'] ?? '';
        $first_name = normalizarTexto($_POST['first_name'] ?? '');
        $second_name = normalizarTexto($_POST['second_name'] ?? '');
        $first_last = normalizarTexto($_POST['first_last'] ?? '');
        $second_last = normalizarTexto($_POST['second_last'] ?? '');
        $birthdate = $_POST['birthdateFormate'] ?? '';
        $expedition_date = $_POST['expedition_date_formate'] ?? '';
        $gender = $_POST['gender'] ?? '';
        $marital_status = $_POST['marital_status'] ?? '';
        $email = $_POST['email'] ?? '';
        $email_very = $_POST['email_very'] ?? '';
        $first_phone = $_POST['first_phone'] ?? '';
        $second_phone = $_POST['second_phone'] ?? '';
        $emergency_contact_name = $_POST['emergency_contact_name'] ?? '';
        $country_code3 = $_POST['country_code3'] ?? '';
        $emergency_contact_number = $_POST['emergency_contact_number'] ?? '';
        $nationality = $_POST['nationality'] ?? '';
        $department = $_POST['department'] ?? '';
        $municipality = $_POST['municipality'] ?? '';
        $address = $_POST['address'] ?? '';
        $latitud = $_POST['latitud'] ?? '';
        $longitud = $_POST['longitud'] ?? '';
        $comuna_corregimiento = $_POST['comuna_corregimiento'] ?? '';
        $barrio = $_POST['barrio'] ?? '';
        $people_charge = $_POST['people_charge'] ?? '';
        $vulnerable_population = $_POST['vulnerable_population'] ?? '';
        $vulnerable_type = $_POST['vulnerable_type'] ?? '';
        $ethnic_group = $_POST['ethnic_group'] ?? '';
        $stratum = $_POST['stratum'] ?? '';
        $residence_area = $_POST['residence_area'] ?? '';
        $country_person = $_POST['country_person'] ?? '';
        $training_level = $_POST['training_level'] ?? '';
        $occupation = $_POST['occupation'] ?? '';
        $time_obligations = $_POST['time_obligations'] ?? '';
        $motivations_belong_program = $_POST['motivations_belong_program'] ?? '';
        $current_situation = $_POST['current_situation'] ?? '';
        $impediment_complete_course = $_POST['impediment_complete_course'] ?? '';
        $availability = '';
        $mode = $_POST['mode'] ?? '';
        $headquarters = $_POST['headquarters'] ?? '';
        $program = $_POST['program'] ?? '';
        $schedules = $_POST['schedules'] ?? '';
        $schedules_alternative = $_POST['schedules_alternative'] ?? '';
        $prior_knowledge = '';
        $level = '';
        $languages = $_POST['languages'] ?? '';
        $languages_level = $_POST['languages_level'] ?? '';
        $medical_condition = $_POST['medical_condition'] ?? '';
        $disability = $_POST['disability'] ?? '';
        $type_disability = $_POST['type_disability'] ?? '';
        $pregnancy = $_POST['pregnancy'] ?? '';
        $technologies = $_POST['technologies'] ?? '';
        $internet = $_POST['internet'] ?? '';
        $knowledge_program = $_POST['knowledge_program'] ?? '';
        $accept_requirements = $_POST['accept_requirements'] ?? '';
        $accepts_tech_talent = $_POST['accepts_tech_talent'] ?? '';
        $accept_data_policies = $_POST['accept_data_policies'] ?? '';
        $file_front_id = $_POST['file_front_id'] ?? '';
        $file_back_id = $_POST['file_back_id'] ?? '';

        date_default_timezone_set('America/Bogota'); // Ajusta según tu zona horaria
        $fechaHoraActual = date("Y-m-d H:i:s");
        $institution = $_POST['institution'] ?? '';
        $lote = 0;

        // Ya no se suben documentos de identidad
        $idFront = '';
        $idBack = '';

        // Verificar si el aspirante ya existe
        $queryCheck = "SELECT * FROM user_register WHERE number_id = '$number_id'";
        $result = $conn->query($queryCheck);

        if ($result->num_rows > 0) {
            // La persona ya está registrada
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    showToast('warning', 'Usted ya se encuentra registrado.');
                });
            </script>
            
<!-- Modal -->
<div class='modal fade' id='successModal' tabindex='-1' aria-labelledby='successModalLabel' aria-hidden='true'>
  <div class='modal-dialog'>
    <div class='modal-content'>
      <div class='modal-header'>
        <h5 class='modal-title' id='successModalLabel'>Registro existente</h5>
        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
      </div>
      <div class='modal-body text-center'>
        <h1 class='display-4'><b>¡Usted ya se encuentra registrado! ✅</b></h1>
        <p class='lead'>
          No es necesario realizar otro registro. Si necesita ayuda, contáctenos.
        </p>
      </div>
      <div class='modal-footer'>
        <button type='button' class='btn btn-danger' data-bs-dismiss='modal'>Cerrar</button>
      </div>
    </div>
  </div>
</div>
            ";
        } else {
            // Generar token único para verificación de correo
            $token = bin2hex(random_bytes(32));

            // Construir la URL de verificación dinámicamente
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'];
            $scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
            $baseUrl = $protocol . '://' . $host . $scriptDir;
            $verificationUrl = $baseUrl . '/verificacion.php?token=' . urlencode($token) . '&email=' . urlencode($email);

            // Insertar los datos en la base de datos
            $queryInsert = "INSERT INTO user_register (
                typeID, number_id, number_id_very, first_name, second_name, first_last, second_last, birthdate, expedition_date, gender, marital_status, email, 
                email_very, first_phone, second_phone, token, email_verified, emergency_contact_name, emergency_contact_number, nationality, department, 
                municipality, address, latitud, longitud, comuna_corregimiento, barrio, people_charge, vulnerable_population, vulnerable_type, ethnic_group, stratum, 
                residence_area, country_person, lote, directed_base, training_level, occupation, time_obligations, motivations_belong_program, current_situation, 
                impediment_complete_course, availability, mode, headquarters, institution, program, schedules, schedules_alternative, prior_knowledge,level, languages, languages_level, 
                medical_condition, disability, type_disability, pregnancy, technologies, internet, knowledge_program, accept_requirements, accepts_tech_talent, 
                accept_data_policies, file_front_id, file_back_id, status, statusAdmin, idCourse, contactMedium, creationDate, dayUpdate
            ) VALUES (
                '$typeID', '$number_id', '$number_id_very', '$first_name', '$second_name', '$first_last', '$second_last', '$birthdate', '$expedition_date', 
                '$gender', '$marital_status', '$email', '$email_very', '$first_phone', '$second_phone', '$token', 0, '$emergency_contact_name', 
                '$country_code3 $emergency_contact_number', '$nationality', '$department', '$municipality', '$address', '$latitud', '$longitud', '$comuna_corregimiento', '$barrio', '$people_charge', 
                '$vulnerable_population', '$vulnerable_type', '$ethnic_group', '$stratum', '$residence_area', '$country_person', 0, 0, '$training_level', '$occupation', 
                '$time_obligations', '$motivations_belong_program', '$current_situation', '$impediment_complete_course', '$availability', 
                '$mode', '$headquarters', '$institution', '$program', '$schedules', '$schedules_alternative', '$prior_knowledge', '$level','$languages', '$languages_level', '$medical_condition', '$disability','$type_disability',
                '$pregnancy', '$technologies', '$internet', '$knowledge_program', '$accept_requirements', '$accepts_tech_talent', 
                '$accept_data_policies', '$idFront', '$idBack',1,0,0,'','$fechaHoraActual','$fechaHoraActual'
            )";

            if ($conn->query($queryInsert) === TRUE) {
                // Debug: ver qué valores están llegando en el POST
                error_log("POST recibido: " . print_r($_POST, true));

                // Continuar con el flujo normal (envío de correo, etc.)
                echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            title: '¡Exitoso!',
                            text: 'Datos registrados con éxito, recuerda revisar tu correo electrónico',
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 2000,
                        });
                    });
                </script>";

                // Define la consulta que quieres ejecutar
                $query = "SELECT * FROM smtpConfig WHERE id=4"; // Asegúrate de que esta consulta tenga sentido en tu lógica

                if (mysqli_query($conn, $query)) {
                    // Continúa con el envío de correo...
                    $querySMTP = mysqli_query($conn, $query);
                    $smtpConfig = mysqli_fetch_array($querySMTP);
                    $host = $smtpConfig['host'];
                    $emailSmtp = $smtpConfig['email'];
                    $password = $smtpConfig['password'];
                    $port = $smtpConfig['port'];
                    $Subject = $smtpConfig['Subject'];
                    $mail = new PHPMailer(true);

                    try {
                        $mail->isSMTP();
                        $mail->Host = $host; // Servidor SMTP
                        $mail->SMTPAuth = true; // Habilita la autenticación SMTP
                        $mail->Username = $emailSmtp; // Usuario SMTP
                        $mail->Password = $password; // Contraseña SMTP
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // STARTTLS (puerto 587)
                        $mail->Port = $port; // Puerto desde la BD (587 para Brevo)

                        $mail->SMTPOptions = array(
                            'ssl' => array(
                                'verify_peer' => false,
                                'verify_peer_name' => false,
                                'allow_self_signed' => true
                            )
                        );

                        $mail->setFrom('noreply@cenditech.com.co', 'Servicio al cliente'); // Remitente del correo                            
                        $mail->CharSet = 'UTF-8';  // Establece la codificación en UTF-8
                        $mail->addAddress($email);

                        $mail->isHTML(true);
                        $mail->Subject = '¡Bienvenido al Bootcamp de ' . $program . ' de CENDI Tech!';

                        $mensaje = "
                            <!DOCTYPE html>
                            <html>
                            <head>
                                <style>
                                    body {
                                        font-family: Arial, sans-serif;
                                        margin: 0;
                                        padding: 0;
                                        background-color: #f4f4f9;
                                        color: #333;
                                    }
                                    .container {
                                        max-width: 600px;
                                        margin: 20px auto;
                                        background: #ffffff;
                                        border-radius: 10px;
                                        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
                                        padding: 20px;
                                    }
                                    .header {
                                        text-align: center;
                                        background: #181E93;
                                        color: #fff;
                                        padding: 20px;
                                        border-top-left-radius: 10px;
                                        border-top-right-radius: 10px;
                                    }
                                    .header h1 {
                                        margin: 0;
                                        font-size: 24px;
                                    }
                                    .content {
                                        line-height: 1.6;
                                    }
                                    .content p {
                                        margin: 10px 0;
                                    }
                                    a.button {
                                        display: inline-block;
                                        margin: 20px 0;
                                        padding: 10px 20px;
                                        background: #181E93;
                                        color: #F9B233;
                                        text-decoration: none;
                                        font-weight: bold;
                                        border-radius: 5px;
                                        text-align: center;
                                    }
                                    a.button:hover,
                                    a.button:visited {
                                        color: #F9B233;
                                        text-decoration: none;
                                    }
                                    .footer {
                                        text-align: center;
                                        margin-top: 20px;
                                        color: #777;
                                        font-size: 12px;
                                    }
                                    .link-fallback {
                                        margin-top: 10px;
                                        font-size: 14px;
                                        color: #333;
                                    }
                                    .link-fallback a {
                                        color: #181E93;
                                        word-break: break-all;
                                    }
                                    .verification-box {
                                        margin: 20px 0;
                                        padding: 20px;
                                        border: 2px solid #181E93;
                                        border-radius: 8px;
                                        background: #f8f9ff;
                                        text-align: center;
                                    }
                                    .verification-box h3 {
                                        margin-top: 0;
                                        color: #181E93;
                                    }
                                </style>
                            </head>
                            <body>
                                <div class='container'>
                                    <div class='header'>
                                        <h1>¡Bienvenido al Bootcamp de $program!</h1>
                                    </div>
                                    <div class='content'>
                                        <p>Hola <b>$first_name</b>,</p>
                                        <p>¡Felicitaciones! Nos emociona darte la bienvenida al <b>Bootcamp de $program</b> de CENDI Tech.</p>
                                        <p>Este Bootcamp es el primer paso hacia un futuro lleno de posibilidades en una de las áreas más demandadas del mercado. Aprenderás habilidades clave, trabajarás en proyectos prácticos y te prepararás para enfrentar los desafíos del mundo digital.</p>
                                        <div class='verification-box'>
                                            <h3>Verifica tu correo electrónico</h3>
                                            <p>Para confirmar tu registro y activar tu cuenta, haz clic en el siguiente botón:</p>
                                            <a class='button' href='$verificationUrl' target='_blank'>Verificar mi correo</a>
                                            <div class='link-fallback'>
                                                <b>Si el botón no funciona, copia y pega este enlace en tu navegador:</b><br>
                                                <a href='$verificationUrl' target='_blank'>$verificationUrl</a>
                                            </div>
                                        </div>
                                        <h3>Próximos Pasos:</h3>
                                        <ol>
                                            <li><b>Revisa tu correo:</b><br>
                                                Te enviaremos toda la información necesaria para comenzar tu formación: horarios, plataforma y recursos.</li>
                                            <li><b>Prepárate para el inicio:</b><br>
                                                Asegúrate de contar con un dispositivo adecuado y una conexión estable a internet para sacar el máximo provecho del programa.</li>
                                        </ol>
                                        <p>Si tienes alguna duda o necesitas apoyo, no dudes en contactarnos a través de este correo. ¡Estamos aquí para ayudarte en cada etapa de tu formación!</p>
                                        <p>Gracias por confiar en nosotros y ser parte de esta gran comunidad. ¡Nos vemos pronto futuro campista! 🚀</p>
                                    </div>
                                    <div class='footer'>
                                        <p>Equipo CENDI Tech</p>
                                    </div>
                                </div>
                            </body>
                            </html>";

                        $mail->Body = $mensaje;
                        //$mail->addEmbeddedImage($urlpicture, 'cuerpo');

                        $mail->send();
                    } catch (Exception $e) {
                        echo '<div class="jumbotron alert-danger text-center">
                                <h1 class="display-4"><b>Error al enviar el correo</b></h1>
                                <p class="lead">No se pudo enviar el correo a ' . $email . '. Error: ' . $mail->ErrorInfo . '</p>
                                
                            </div>';
                    }
                } else {
                    echo '<div class="alert alert-danger">Error al registrar los datos: ' . mysqli_error($conn) . '</div>';
                }
            } else {
                echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                showToast('error', 'Error: " . $conn->error . "');
            });
            </script>";
            }
        }
    }
    ?>
    <div class="glass-form p-3 mb-5 rounded">
        <form id="multi-step-form" class="needs-validation" novalidate method="POST" enctype="multipart/form-data">
            <div class="progress m-1">
                <div id="progress" class="progress-bar"></div>
            </div>
            <?php
            $totalFields = count($includeFields);
            $steps = ceil($totalFields / $fieldsPerStep);

            for ($step = 1; $step <= $steps; $step++) {
                echo "<div class='step p-1' id='step-$step'>";
                echo "<span class='tittle'>";
                if ($step == 1) {
                    echo "INFORMACIÓN PERSONAL - Paso 1 de $steps";
                } elseif ($step == 2) {
                    echo "INFORMACIÓN ACADÉMICA - Paso 2 de $steps";
                } elseif ($step == 3) {
                    echo "PREFERENCIAS ADICIONALES - Paso 3 de $steps";
                }
                echo "</span>";

                echo "<div class='row'>";

                $startIndex = ($step - 1) * $fieldsPerStep;
                $fieldsInStep = array_slice($includeFields, $startIndex, $fieldsPerStep);

                foreach ($fieldsInStep as $fieldName) {
                    generateField($fieldName);
                }

                echo "</div></div>";
            }

            function generateField($fieldName)
            {
                global $formConfig, $selectedHeadquarter, $selectedMode, $institucion_param, $departamentos, $comunas;
                $field = $formConfig[$fieldName] ?? [];
                $type = $field['type'] ?? 'text';
                $label = $field['label'] ?? ucfirst($fieldName);
                $attributes = generateAttributes($field['attributes'] ?? []);

                echo "<div class='form-group'>";
                if (!in_array($fieldName, ['mode', 'headquarters'])) {
                    echo "<label class='bold-label label-$fieldName'>$label</label>";
                }

                if ($fieldName === 'birthdate') {
                    echo "<div class='row'>";

                    // Día
                    echo "<div class='col-4'>";
                    echo "<select class='form-control' id='dia_nacimiento' name='dia_nacimiento' required>";
                    echo "<option value='' disabled selected>Día</option>";
                    for ($i = 1; $i <= 31; $i++) {
                        echo "<option value='$i'>$i</option>";
                    }
                    echo "</select>";
                    echo "</div>";

                    // Mes
                    echo "<div class='col-4'>";
                    echo "<select class='form-control' id='mes_nacimiento' name='mes_nacimiento' required>";
                    echo "<option value='' disabled selected>Mes</option>";
                    for ($i = 1; $i <= 12; $i++) {
                        echo "<option value='$i'>$i</option>";
                    }
                    echo "</select>";
                    echo "</div>";

                    // Año
                    echo "<div class='col-4'>";
                    echo "<select class='form-control' id='anio_nacimiento' name='anio_nacimiento' required>";
                    echo "<option value='' disabled selected>Año</option>";
                    for ($i = date('Y'); $i >= 1900; $i--) {
                        echo "<option value='$i'>$i</option>";
                    }
                    echo "</select>";
                    echo "</div>";

                    // Campo oculto para la fecha unificada
                    echo "<input type='hidden' name='birthdateFormate' id='birthdate_hidden'>";

                    echo "</div>";
                } elseif ($fieldName === 'mode') {
                    // Modalidad siempre virtual
                    echo "<input type='hidden' name='mode' id='mode' value='$selectedMode'>";
                } elseif ($fieldName === 'headquarters') {
                    // Sede siempre "No aplica"
                    echo "<input type='hidden' name='headquarters' id='headquarters' value='" . $selectedHeadquarter['name'] . "'>";
                    echo "<input type='hidden' name='institution' value='" . $selectedHeadquarter['name'] . "'>";
                } elseif ($fieldName === 'program') {
                    // Campo de programa con listado estático
                    $options = $field['options'] ?? [];
                    echo "<select class='form-control mb-3' name='program' id='program' required>";
                    echo "<option value=''>Seleccione un programa</option>";

                    foreach ($options as $value => $optionLabel) {
                        if ($value === '') {
                            continue;
                        }
                        echo "<option value='$value'>$optionLabel</option>";
                    }
                    echo "</select>";
                } else if ($fieldName == 'department') {
                    echo "<select class='form-control' name='department' id='lista_departamento' data-populated='true' required>";
                    echo "<option value=''>Seleccione un departamento</option>";
                    foreach ($departamentos as $dep) {
                        echo "<option value='" . $dep['id_departamento'] . "'>" . htmlspecialchars($dep['departamento']) . "</option>";
                    }
                    echo "</select>";
                } else if ($fieldName == 'municipality') {
                    echo "<select name='municipality' id='municipios' class='form-control' data-populated='true' required>";
                    echo "<option value=''>Seleccione un departamento primero</option>";
                    echo "</select>";
                } else if ($fieldName == 'comuna_corregimiento') {
                    echo "<select class='form-control' name='comuna_corregimiento' id='comuna_corregimiento' required>";
                    echo "<option value=''>Seleccione una comuna/corregimiento</option>";
                    foreach ($comunas as $c) {
                        $label = $c['codigo'] . ' - ' . $c['nombre'];
                        echo "<option value='" . htmlspecialchars($label) . "' data-codigo='" . htmlspecialchars($c['codigo']) . "'>" . htmlspecialchars($label) . "</option>";
                    }
                    echo "</select>";
                } else if ($fieldName == 'barrio') {
                    echo "<select name='barrio' id='barrio' class='form-control' required>";
                    echo "<option value=''>Seleccione primero una comuna</option>";
                    echo "</select>";
                } elseif ($type === 'select') {
                    $options = $field['options'] ?? [];
                    echo "<select $attributes>";
                    foreach ($options as $value => $optionLabel) {
                        echo "<option value='$value'>$optionLabel</option>";
                    }
                    echo "</select>";
                } elseif ($fieldName === 'expedition_date') {
                    echo "<div class='row'>";

                    // Día
                    echo "<div class='col-4'>";
                    echo "<select class='form-control' id='expedition_day' name='expedition_day' required>";
                    echo "<option value='' disabled selected>Día</option>";
                    for ($i = 1; $i <= 31; $i++) {
                        echo "<option value='$i'>$i</option>";
                    }
                    echo "</select>";
                    echo "</div>";

                    // Mes
                    echo "<div class='col-4'>";
                    echo "<select class='form-control' id='expedition_month' name='expedition_month' required>";
                    echo "<option value='' disabled selected>Mes</option>";
                    for ($i = 1; $i <= 12; $i++) {
                        echo "<option value='$i'>$i</option>"; // Ahora solo mostramos el número del mes
                    }
                    echo "</select>";
                    echo "</div>";

                    // Año
                    echo "<div class='col-4'>";
                    echo "<select class='form-control' id='expedition_year' name='expedition_year' required>";
                    echo "<option value='' disabled selected>Año</option>";
                    for ($i = date('Y'); $i >= 1900; $i--) {
                        echo "<option value='$i'>$i</option>";
                    }
                    echo "</select>";
                    echo "</div>";

                    // Campo oculto para la fecha unificada
                    echo "<input type='hidden' name='expedition_date_formate' id='expedition_hidden'>"; // Campo oculto para la fecha unificada

                    echo "</div>";
                }

                // Verificamos si el campo es 'email' o 'email_very'
                elseif ($fieldName === 'email') {
                    echo "<div class='row'>"; // Usamos una fila con espacio entre las columnas

                    // Campo de email
                    if ($fieldName === 'email') {
                        echo "<div class='col col-md-6'>";  // Asegura que cada campo ocupe la mitad del espacio en pantallas medianas
                        echo "<input type='$type' name='$fieldName' id='email' class='form-control' $attributes>";
                        echo "<small id='emailMessage' class='form-text text-muted'>Correo electrónico</small>";  // Mensaje inicial debajo del campo
                        echo "</div>";
                    }

                    // Campo de verificar email (email_very)
                    echo "<div class='col col-md-6'>";  // Columna para el campo de verificación
                    echo "<input type='$type' name='email_very' id='email_very' class='form-control' placeholder='' $attributes>";  // Segundo campo de email
                    echo "<small id='verifyMessage' class='form-text text-muted'>Confirmar el correo electrónico</small>";  // Mensaje inicial debajo del campo
                    echo "</div>";

                    echo "</div>"; // Cierra la fila
                } elseif ($fieldName === 'first_phone') {
                    echo "<div class='row'>";

                    // Campo de selección de país con banderas
                    echo "<div class='col-md-6'>";
                    echo "<select name='country_code' class='form-control' id='country_code' required>";
                    echo "<option value='+57'  selected>Colombia</option>";
                    echo "</select>";
                    echo "</div>";

                    // Campo para el teléfono
                    echo "<div class='col-md-6'>";
                    echo "<input type='tel' name='first_phone' id='first_phone' class='form-control' placeholder='321 1234567' required maxlength='10' minlength='10' pattern='^[0-9]{10}$' inputmode='numeric' oninput='this.value=this.value.replace(/[^0-9]/g, \"\")'>";
                    echo "</div>";


                    echo "</div>";
                } elseif ($fieldName === 'second_phone') {
                    echo "<div class='row'>";

                    // Campo de selección de país con banderas
                    echo "<div class='col-md-6'>";
                    echo "<select name='country_code2' class='form-control' id='country_code2' required>";
                    echo "<option value='+57'  selected>Colombia</option>";
                    echo "</select>";
                    echo "</div>";

                    // Campo para el teléfono
                    echo "<div class='col-md-6'>";
                    echo "<input type='tel' name='second_phone' id='second_phone' class='form-control' placeholder='321 1234567' required maxlength='10' minlength='10' pattern='^[0-9]{10}$' inputmode='numeric' oninput='this.value=this.value.replace(/[^0-9]/g, \"\")'>";
                    echo "</div>";

                    echo "</div>";
                } elseif ($fieldName === 'password') {
                    echo "<div class='row'>"; // Usamos una fila con espacio entre las columnas

                    // Campo de contraseña
                    echo "<div class='col col-md-6'>";  // Columna para la contraseña
                    echo "<input type='password' name='password' id='password' class='form-control' $attributes>";
                    echo "<small id='passwordMessage' class='form-text text-muted'>Contraseña (Alfanumérica, al menos 8 caracteres, con caracteres especiales)</small>";  // Mensaje inicial debajo del campo
                    echo "<div id='passwordLengthMessage' style='color:red;'>Mínimo 8 caracteres</div>"; // Mensaje de longitud mínima
                    echo "</div>";

                    // Campo de verificar contraseña
                    echo "<div class='col col-md-6'>";  // Columna para la verificación de la contraseña
                    echo "<input type='password' name='password_very' id='password_very' class='form-control' placeholder='' $attributes>";  // Segundo campo de verificación
                    echo "<small id='verifyMessagePassword' class='form-text text-muted'>Confirmar la contraseña</small>";  // Mensaje debajo del campo
                    echo "</div>";

                    echo "  <div id='passwordValidationMessage' style='color:red;'></div>";  // Mensaje de validación en tiempo real para la contraseña
                    echo "</div>"; // Cierra la fila
                } elseif ($fieldName === 'emergency_contact_number') {
                    echo "<div class='row'>";

                    // Campo de selección de país con banderas
                    echo "<div class='col-md-6'>";
                    echo "<select name='country_code3' class='form-control' id='country_code3' required>";
                    echo "<option value='+57' selected>Colombia</option>";
                    echo "</select>";
                    echo "</div>";

                    // Campo para el teléfono
                    echo "<div class='col-md-6'>";
                    echo "<input type='tel' name='emergency_contact_number' id='emergency_contact_number' class='form-control' placeholder='321 1234567' required maxlength='10' minlength='10' pattern='^[0-9]{10}$' inputmode='numeric' oninput='this.value=this.value.replace(/[^0-9]/g, \"\")'>";
                    echo "</div>";

                    echo "</div>";
                } elseif ($fieldName === 'stratum') {
                    echo "<div class='form-group'>";

                    // Contenedor para mostrar los botones en línea
                    echo "<div class='d-flex flex-wrap'>";

                    foreach ($field['options'] as $value => $label) {
                        echo "<div class='form-check me-3'>"; // Espaciado entre botones
                        echo "<input type='radio' class='{$field['attributes']['class']}' 
                                name='{$field['attributes']['name']}' 
                                value='$value' 
                                id='stratum_$value' 
                                required>";
                        echo "<label class='form-check-label' for='stratum_$value'>$label</label>";
                        echo "</div>";
                    }

                    echo "</div>"; // Cierra el contenedor de botones
                    echo "</div>"; // Cierra el form-group
                } elseif ($fieldName == 'motivations_belong_program') {
                    echo "<div class='form-group'>";

                    // Lista de opciones para servicios públicos
                    $opcionesMotivaciones = ['Adquirir nuevos conocimientos', 'Conseguir empleo', 'Emprender', 'Fortalecer conocimientos', 'Tener un trabajo remoto', 'Incrementar mis ingresos', 'Familia/amigos'];

                    // Campo de entrada para mostrar los servicios seleccionados (solo lectura)
                    echo "<input type='hidden' id='motivations_belong_program' name='motivations_belong_program' class='form-control mb-3' value='' readonly>";

                    // Lista de checkboxes para seleccionar los servicios
                    foreach ($opcionesMotivaciones as $value) {
                        echo "<div class='form-check form-check'>";
                        echo "<input type='checkbox' class='form-check-input custom-checkbox' name='selectedMotivaciones' id='servicio_$value' value='$value' onchange='updateMotivaciones()'>";
                        echo "<label class='form-check-label' for='servicio_$value'> " . ucfirst($value) . "</label>";
                        echo "</div>";
                    }
                    echo "</div>";
                } elseif ($fieldName == 'current_situation') {
                    echo "<div class='form-group'>";
                    // Lista de opciones para situaciones actuales
                    $opcionesMotivaciones = [
                        'Vivo solo/a',
                        'Tengo Apoyo economico de mi  familia',
                        'Vivo con un familiar cercano (Padres, hermanos, pareja)',
                        'No tengo residencia fija',
                        'No cuento con ingresos',
                        'Estoy buscando trabajo',
                    ];
                    // Campo de entrada para mostrar las opciones seleccionadas (solo lectura)
                    echo "<input type='hidden' id='current_situation' name='current_situation' class='form-control mb-3' value='' readonly>";
                    // Lista de checkboxes para seleccionar las opciones
                    foreach ($opcionesMotivaciones as $value) {
                        echo "<div class='form-check form-check'>";
                        echo "<input type='checkbox' class='form-check-input custom-checkbox' name='selectedSituations' id='situacion_$value' value='$value' onchange='updateSituacionesActuales()'>";
                        echo "<label class='form-check-label' for='situacion_$value'> " . ucfirst($value) . "</label>";
                        echo "</div>";
                    }
                    echo "</div>";
                } elseif ($fieldName == 'accept_requirements') {
                    echo "<div class='form-group'>";
                    // Campo de aceptación de requisitos
                    echo "<div class='form-check form-check'>";
                    echo "<input type='checkbox' class='form-check-input custom-checkbox' name='accept_requirements' id='accept_requirements' value='Sí' required>";
                    echo "<label class='form-check-label' for='accept_requirements'>Acepta los requisitos establecidos por la presente convocatoria</label>";
                    echo "</div>";
                    // Enlace con los requisitos
                    echo "<p><a href='' target='_blank'>Puedes consultar los requisitos de la convocatoria haciendo click aquí</a></p>";
                    echo "</div>";
                } elseif ($fieldName == 'accepts_tech_talent') {
                    echo "<div class='form-group'>";
                    // Campo de aceptación de requisitos
                    echo "<div class='form-check form-check'>";
                    echo "<input type='checkbox' class='form-check-input custom-checkbox' name='accepts_tech_talent' id='accepts_tech_talent' value='Sí' required>";
                    echo "<label class='form-check-label' for='accepts_tech_talent'>Acepta la carta de compromiso de talento Tech</label>";
                    echo "</div>";
                    // Enlace con los requisitos
                    echo "<p><a href='' target='_blank'>Puedes consultar los requisitos de la convocatoria haciendo click aquí</a></p>";
                    echo "</div>";
                } elseif ($fieldName == 'accept_data_policies') {
                    echo "<div class='form-group'>";
                    // Campo de aceptación de requisitos
                    echo "<div class='form-check form-check'>";
                    echo "<input type='checkbox' class='form-check-input custom-checkbox' name='accept_data_policies' id='accept_data_policies' value='Sí' required>";
                    echo "<label class='form-check-label' for='Accept_data_policies'>Confirmo que he leído y acepto las políticas de tratamiento de datos personales</label>";
                    echo "</div>";
                    // Enlace con los requisitos
                    echo "<p><a href='' target='_blank'>Puedes consultar los requisitos de la convocatoria haciendo click aquí</a></p>";
                    echo "</div>";
                } else {
                    echo "<input type='$type' $attributes>";
                }
                echo "</div>";
            }
            ?>
            <!-- CAMPOS OCULTOS PARA CAPTURAR ESTOS CAMPOS -->
            <input type="hidden" id="latitud" name="latitud">
            <input type="hidden" id="longitud" name="longitud">
            <input type="hidden" id="has_certification" name="has_certification">
            <input type="hidden" id="program_certified" name="program_certified">
            <input type="hidden" id="anio_certificacion" name="anio_certificacion">
            <div class="form-navigation mt-3 d-flex justify-content-center">
                <button type="button" id="prevBtn" class="btn prevBtn m-2" style="display:none;"><i class="bi bi-chevron-double-left"></i> Anterior</button>
                <button type="button" id="nextBtn" class="btn nextBtn m-2">Siguiente</button>
                <button type="submit" id="saveBtn" class="btn nextBtn m-2" name="submit" style="display:none;"><i class="bi bi-floppy-fill"></i> Guardar</button>
            </div>

        </form>
    </div>
    <script>
        let currentStep = 1;
        const steps = document.querySelectorAll('.step');
        const progressBar = document.getElementById('progress');
        const nextBtn = document.getElementById('nextBtn');
        const prevBtn = document.getElementById('prevBtn');
        const saveBtn = document.getElementById('saveBtn');

        // Función para actualizar la visualización de los pasos
        function showStep() {
            // Actualizar las clases activas de los pasos
            steps.forEach((step, index) => {
                if (index + 1 === currentStep) {
                    step.classList.add('active');
                } else {
                    step.classList.remove('active');
                }
            });

            // Calcular el progreso
            const progress = ((currentStep - 1) / (steps.length - 1)) * 100;
            progressBar.style.width = progress + '%';

            // Mostrar/ocultar los botones de navegación
            updateNavigationButtons();
        }

        // Función para mostrar/ocultar los botones de navegación
        function updateNavigationButtons() {
            if (currentStep === 1) {
                prevBtn.style.display = 'none';
            } else {
                prevBtn.style.display = 'inline';
            }

            if (currentStep === steps.length) {
                nextBtn.style.display = 'none'; // Oculta el botón "Siguiente"
                saveBtn.style.display = 'inline'; // Muestra el botón "Guardar"
            } else {
                nextBtn.style.display = 'inline';
                saveBtn.style.display = 'none';
            }
        }

        // Función de validación antes de cambiar al siguiente paso
        function validateStep() {
            let error = false;
            let message = '';

            const fieldElements = {
                'number_id': document.querySelector('[name="number_id"]'),
                'number_id_very': document.querySelector('[name="number_id_very"]'),
                'email': document.querySelector('[name="email"]'),
                'email_very': document.querySelector('[name="email_very"]'),
                'first_phone': document.querySelector('[name="first_phone"]'),
                'second_phone': document.querySelector('[name="second_phone"]'),
                'emergency_contact_number': document.querySelector('[name="emergency_contact_number"]'),
            };

            // Validaciones personalizadas
            if (fieldElements['number_id'] && fieldElements['number_id_very'] && fieldElements['number_id'].value !== fieldElements['number_id_very'].value) {
                error = true;
                message = 'Los números de identificación no coinciden';
            } else if ((fieldElements['number_id'] && fieldElements['number_id_very'] && (fieldElements['number_id'].value === "" || fieldElements['number_id_very'].value === ""))) {
                error = true;
                message = 'Uno de los campos de identificación está vacío';
            } else if (fieldElements['email'] && fieldElements['email_very'] && fieldElements['email'].value !== fieldElements['email_very'].value) {
                error = true;
                message = 'Los correos electrónicos no coinciden';
            } else if ((fieldElements['email'] && fieldElements['email_very'] && (fieldElements['email'].value === "" || fieldElements['email_very'].value === ""))) {
                error = true;
                message = 'Uno de los correos electrónicos está vacío';
            } else if (fieldElements['first_phone'] && fieldElements['second_phone'] && fieldElements['emergency_contact_number'] &&
                (fieldElements['first_phone'].value.length !== 10 || fieldElements['second_phone'].value.length !== 10 || fieldElements['emergency_contact_number'].value.length !== 10)) {
                error = true;
                message = 'Revisa los campos de teléfono, deben tener 10 dígitos';
            } else if (fieldElements['first_phone'] && fieldElements['second_phone'] && fieldElements['emergency_contact_number'] &&
                (fieldElements['first_phone'].value === "" || fieldElements['second_phone'].value === "" || fieldElements['emergency_contact_number'].value === "")) {
                error = true;
                message = 'Uno de los campos de teléfono está vacío';
            }

            // Si hay un error, mostrar el mensaje y evitar avanzar
            if (error) {
                Swal.fire({
                    title: '¡Error!',
                    text: message,
                    icon: 'error',
                    showConfirmButton: true,
                });
                return false; // No permitir avanzar al siguiente paso
            }

            return true; // Permitir avanzar al siguiente paso si no hay error
        }

        // Navegar al siguiente paso
        nextBtn.addEventListener('click', () => {
            // Validamos el paso actual antes de avanzar
            if (validateStep()) {
                if (currentStep < steps.length) {
                    currentStep++;
                    showStep();
                }
            }
        });

        // Navegar al paso anterior
        prevBtn.addEventListener('click', () => {
            if (currentStep > 1) {
                currentStep--;
                showStep();
            }
        });

        // Inicializar el primer paso
        showStep();
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sedePasswordHash = <?php echo json_encode($headquarterPassword); ?>;
            if (sedePasswordHash && sedePasswordHash !== '' && sedePasswordHash !== null) {
                Swal.fire({
                    title: 'Acceso restringido',
                    text: 'Esta sede requiere contraseña para registrarse. Por favor, ingrésala:',
                    input: 'password',
                    inputLabel: 'Contraseña de la sede',
                    inputPlaceholder: 'Ingresa la contraseña',
                    inputAttributes: {
                        autocapitalize: 'off',
                        autocorrect: 'off'
                    },
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showCancelButton: false,
                    confirmButtonText: 'Validar',
                    preConfirm: (password) => {
                        if (!password) {
                            Swal.showValidationMessage('Debes ingresar la contraseña');
                            return false;
                        }
                        return fetch('APIS/register_student/validate_headquarter_password.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({
                                    sede_id: <?php echo json_encode($sede_id); ?>,
                                    password: password
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (!data.success) {
                                    throw new Error(data.message || 'Contraseña incorrecta');
                                }
                                return true;
                            })
                            .catch(error => {
                                Swal.showValidationMessage(error.message);
                            });
                    }
                }).then((result) => {
                    if (!result.isConfirmed) {
                        document.getElementById('multi-step-form').style.display = 'none';
                    } else {
                        // Mostrar el modal informativo de Bootstrap solo si la contraseña fue correcta
                        var infoModal = document.getElementById('infoModal');
                        if (infoModal) {
                            var myModal = new bootstrap.Modal(infoModal);
                            myModal.show();
                        }
                    }
                });
            } else {
                // Si no hay contraseña, mostrar el modal informativo normalmente
                var infoModal = document.getElementById('infoModal');
                if (infoModal) {
                    var myModal = new bootstrap.Modal(infoModal);
                    myModal.show();
                }
            }
        });
    </script>

    <script>
        // Referencias a los elementos
        const disabilitySelect = document.getElementById('disability');
        const typeDisabilitySelect = document.getElementById('type_disability');
        typeDisabilitySelect.innerHTML = `
                <option value="">No has contestado a la pregunta anterior</option>
            `;
        // Función para manejar la visibilidad y estado del campo de tipo de discapacidad
        function handleDisabilityChange() {
            const selectedDisability = disabilitySelect.value;

            if (selectedDisability === 'Sí') {
                // Habilitar el campo y mostrar todas las opciones
                typeDisabilitySelect.innerHTML = `
                <option value="">Seleccione</option>
                <option value="Discapacidad visual">Discapacidad visual</option>
                <option value="Discapacidad auditiva">Discapacidad auditiva</option>
                <option value="Sordoceguera">Sordoceguera</option>
                <option value="Discapacidad intelectual">Discapacidad intelectual</option>
                <option value="Discapacidad psicosocial">Discapacidad psicosocial (mental)</option>
                <option value="Discapacidad física">Discapacidad física</option>
                <option value="Discapacidad múltiple">Discapacidad múltiple</option>
            `;
                typeDisabilitySelect.removeAttribute('readonly'); // Quitar readonly si existía
            } else if (selectedDisability === 'No') {
                // Establecer "No aplica" como opción predeterminada y hacerlo de solo lectura
                typeDisabilitySelect.innerHTML = `
                <option value="No aplica" selected>No aplica</option>
            `;
                typeDisabilitySelect.setAttribute('readonly', true); // Añadir readonly
            }
        }
        // Evento al cambiar la selección de discapacidad
        disabilitySelect.addEventListener('change', handleDisabilityChange);
    </script>
    <script>
        // Referencias a los elementos
        const residenceAreaSelect = document.getElementById('residence_area');
        const countryPersonSelect = document.getElementById('country_person');

        // Función para actualizar el campo de campesino según el área de residencia
        function updateCountryPersonOptions() {
            const selectedResidenceArea = residenceAreaSelect.value;

            if (selectedResidenceArea === 'Urbana') {
                // Si selecciona área urbana, automáticamente establecer como "No" y deshabilitar
                countryPersonSelect.innerHTML = '<option value="No" selected>No</option>';
                countryPersonSelect.setAttribute('disabled', 'disabled');
            } else if (selectedResidenceArea === 'Rural') {
                // Si selecciona área rural, mostrar las opciones Sí y No
                countryPersonSelect.innerHTML = `
                    <option value="">Seleccione</option>
                    <option value="Sí">Sí</option>
                    <option value="No">No</option>
                `;
                countryPersonSelect.removeAttribute('disabled');
            } else {
                // Si no selecciona ninguna opción, mostrar solo la opción predeterminada
                countryPersonSelect.innerHTML = '<option value="">Seleccione</option>';
                countryPersonSelect.setAttribute('disabled', 'disabled');
            }
        }

        // Evento para detectar cambios en el select de área de residencia
        residenceAreaSelect.addEventListener('change', updateCountryPersonOptions);

        // Inicializar el estado del select de campesino al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            updateCountryPersonOptions();
        });
    </script>



    <script>
        document.getElementById('multi-step-form').addEventListener('submit', function(event) {
            const fieldElements = {
                'typeID': document.querySelector('[name="typeID"]'),
                'number_id': document.querySelector('[name="number_id"]'),
                'number_id_very': document.querySelector('[name="number_id_very"]'),
                'first_name': document.querySelector('[name="first_name"]'),
                'second_name': document.querySelector('[name="second_name"]'),
                'first_last': document.querySelector('[name="first_last"]'),
                'second_last': document.querySelector('[name="second_last"]'),
                'birthdate': document.querySelector('[name="birthdate"]'),
                'expedition_date': document.querySelector('[name="expedition_date"]'),
                'gender': document.querySelector('[name="gender"]'),
                'marital_status': document.querySelector('[name="marital_status"]'),
                'email': document.querySelector('[name="email"]'),
                'email_very': document.querySelector('[name="email_very"]'),
                'first_phone': document.querySelector('[name="first_phone"]'),
                'second_phone': document.querySelector('[name="second_phone"]'),
                'emergency_contact_name': document.querySelector('[name="emergency_contact_name"]'),
                'emergency_contact_number': document.querySelector('[name="emergency_contact_number"]'),
                'nationality': document.querySelector('[name="nationality"]'),
                'department': document.querySelector('[name="department"]'),
                'municipality': document.querySelector('[name="municipality"]'),
                'comuna_corregimiento': document.querySelector('[name="comuna_corregimiento"]'),
                'barrio': document.querySelector('[name="barrio"]'),
                'address': document.querySelector('[name="address"]'),
                'people_charge': document.querySelector('[name="people_charge"]'),
                'vulnerable_population': document.querySelector('[name="vulnerable_population"]'),
                'vulnerable_type': document.querySelector('[name="vulnerable_type"]'),
                'ethnic_group': document.querySelector('[name="ethnic_group"]'),
                'stratum': document.querySelector('[name="stratum"]'),
                'residence_area': document.querySelector('[name="residence_area"]'),
                'country_person': document.querySelector('[name="country_person"]'),
                'training_level': document.querySelector('[name="training_level"]'),
                'occupation': document.querySelector('[name="occupation"]'),
                'time_obligations': document.querySelector('[name="time_obligations"]'),
                'motivations_belong_program': document.querySelector('[name="motivations_belong_program"]'),
                'current_situation': document.querySelector('[name="current_situation"]'),
                'impediment_complete_course': document.querySelector('[name="impediment_complete_course"]'),
                'mode': document.querySelector('[name="mode"]'),
                'headquarters': document.querySelector('[name="headquarters"]'),
                'program': document.querySelector('[name="program"]'),
                'languages': document.querySelector('[name="languages"]'),
                'languages_level': document.querySelector('[name="languages_level"]'),
                'medical_condition': document.querySelector('[name="medical_condition"]'),
                'disability': document.querySelector('[name="disability"]'),
                'type_disability': document.querySelector('[name="type_disability"]'),
                'pregnancy': document.querySelector('[name="pregnancy"]'),
                'technologies': document.querySelector('[name="technologies"]'),
                'internet': document.querySelector('[name="internet"]'),
                'knowledge_program': document.querySelector('[name="knowledge_program"]'),
                'accept_requirements': document.querySelector('[name="accept_requirements"]'),
                'accepts_tech_talent': document.querySelector('[name="accepts_tech_talent"]'),
                'accept_data_policies': document.querySelector('[name="accept_data_policies"]'),
                'file_front_id': document.querySelector('[name="file_front_id"]'),
                'file_back_id': document.querySelector('[name="file_back_id"]')
            };

            let error = false; // Bandera para detectar errores
            let message = ''; // Mensaje de error

            // Mapeo de campos con sus nombres en español
            const fieldNames = {
                'typeID': 'Tipo de identificación',
                'number_id': 'Número de identificación',
                'number_id_very': 'Número de identificación (verificación)',
                'first_name': 'Primer nombre',
                'second_name': 'Segundo nombre',
                'first_last': 'Primer apellido',
                'second_last': 'Segundo apellido',
                'birthdate': 'Fecha de nacimiento',
                'expedition_date': 'Fecha de expedición del documento',
                'gender': 'Género',
                'marital_status': 'Estado civil',
                'email': 'Correo electrónico',
                'email_very': 'Correo electrónico (verificación)',
                'first_phone': 'Primer teléfono de contacto',
                'second_phone': 'Segundo teléfono de contacto',
                'emergency_contact_name': 'Nombre de contacto de emergencia',
                'emergency_contact_number': 'Número de contacto de emergencia',
                'nationality': 'Nacionalidad',
                'department': 'Departamento',
                'municipality': 'Municipio',
                'comuna_corregimiento': 'Comuna / Corregimiento',
                'barrio': 'Barrio',
                'address': 'Dirección',
                'people_charge': 'Personas a cargo',
                'vulnerable_population': 'que pregunta si usted pertenence a un grupo poblacional reconocido por sus necesidades especiales o de atención prioritaria',
                'vulnerable_type': 'donde puedes indicar a cuál de los siguientes grupos pertenece',
                'ethnic_group': 'Grupo étnico',
                'stratum': 'Estrato',
                'residence_area': 'Área de residencia',
                'country_person': 'Si es campesino',
                'training_level': 'Nivel de formación',
                'occupation': 'Ocupación',
                'time_obligations': 'que indica que tiempo te consume tus obligaciones',
                'motivations_belong_program': 'Motivación para pertenecer al programa',
                'current_situation': 'Situación actual',
                'impediment_complete_course': 'que indica que Impedimento para completar el curso',
                'mode': 'de modalidad de estudio',
                'headquarters': 'Sede',
                'program': 'Programa',
                'languages': 'que inidica si habla otro idioma',
                'languages_level': 'Nivel de idiomas',
                'medical_condition': 'Condición médica',
                'disability': 'Discapacidad',
                'type_disability': 'Tipo de discapacidad',
                'pregnancy': 'Embarazo',
                'technologies': 'Tecnologías',
                'internet': 'Acceso a internet',
                'knowledge_program': 'Conocimiento del programa',
                'accept_requirements': 'Aceptación de requisitos',
                'accepts_tech_talent': 'Aceptación de talento tecnológico',
                'accept_data_policies': 'Aceptación de políticas de datos',
                'file_front_id': 'Archivo de frente de identificación',
                'file_back_id': 'Archivo de reverso de identificación'
            };

            // Validaciones para los campos
            if (fieldElements['number_id'].value !== fieldElements['number_id_very'].value) {
                error = true;
                message = 'Los números de identificación no coinciden';
            } else if (fieldElements['number_id'].value === "" || fieldElements['number_id_very'].value === "") {
                error = true;
                message = 'Uno de los campos de identificación está vacío';
            } else if (fieldElements['email'].value !== fieldElements['email_very'].value) {
                error = true;
                message = 'Los correos electrónicos no coinciden';
            } else if (fieldElements['email'].value === "" || fieldElements['email_very'].value === "") {
                error = true;
                message = 'Uno de los correos electrónicos está vacío';
            } else if (fieldElements['first_phone'].value.length !== 10 || fieldElements['second_phone'].value.length !== 10 || fieldElements['emergency_contact_number'].value.length !== 10) {
                error = true;
                message = 'Revisa los campos de teléfono, deben tener 10 dígitos';
            } else if (fieldElements['first_phone'].value === "" || fieldElements['second_phone'].value === "" || fieldElements['emergency_contact_number'].value === "") {
                error = true;
                message = 'Uno de los campos de teléfono está vacío';
            }

            // Validar que los campos obligatorios no estén vacíos
            Object.keys(fieldElements).forEach(fieldName => {
                if (fieldElements[fieldName] && fieldElements[fieldName].required && fieldElements[fieldName].value === "") {
                    error = true;
                    message = `El campo ${fieldNames[fieldName] || fieldName.replace(/_/g, ' ')} no puede estar vacío`;
                }
            });

            // Si hay un error, mostrar el mensaje y evitar el envío del formulario
            if (error) {
                event.preventDefault();
                Swal.fire({
                    title: '¡Ojo!',
                    text: message,
                    icon: 'warning',
                    showConfirmButton: false,
                    timer: 5000,
                });
            }
        });
    </script>
    <script>
        // Asegurar que los campos deshabilitados se envíen con el formulario
        document.getElementById('multi-step-form').addEventListener('submit', function() {
            // Habilitar temporalmente los campos deshabilitados para que se envíen
            if (countryPersonSelect.disabled) {
                countryPersonSelect.disabled = false;
            }
        });
    </script>
    <div id="toast-container" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.all.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const departamentoSelect = document.getElementById('lista_departamento');
            const municipioSelect = document.getElementById('municipios');
            if (!departamentoSelect || !municipioSelect) return;

            function cargarMunicipios(codDepartamento) {
                municipioSelect.innerHTML = '<option value="">Cargando...</option>';
                if (!codDepartamento) {
                    municipioSelect.innerHTML = '<option value="">Seleccione un departamento primero</option>';
                    return;
                }
                fetch('APIS/register_student/get_municipios.php?departamento=' + encodeURIComponent(codDepartamento))
                    .then(function(response) { return response.json(); })
                    .then(function(data) {
                        municipioSelect.innerHTML = '<option value="">Seleccione un municipio</option>';
                        data.forEach(function(mun) {
                            const option = document.createElement('option');
                            option.value = mun.cod_municipio;
                            option.textContent = mun.nom_municipio;
                            municipioSelect.appendChild(option);
                        });
                    })
                    .catch(function() {
                        municipioSelect.innerHTML = '<option value="">Error al cargar municipios</option>';
                    });
            }

            departamentoSelect.addEventListener('change', function() {
                cargarMunicipios(this.value);
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const comunaSelect = document.getElementById('comuna_corregimiento');
            const barrioSelect = document.getElementById('barrio');
            if (!comunaSelect || !barrioSelect) return;

            const hasSelect2 = typeof jQuery !== 'undefined' && typeof jQuery.fn.select2 === 'function';

            function renderBarrios(data) {
                if (hasSelect2) {
                    jQuery(barrioSelect).select2('destroy');
                }
                barrioSelect.innerHTML = '<option value="">Seleccione un barrio</option>';
                (data || []).forEach(function(nombre) {
                    const option = document.createElement('option');
                    option.value = nombre;
                    option.textContent = nombre;
                    barrioSelect.appendChild(option);
                });
                if (hasSelect2) {
                    jQuery(barrioSelect).select2({
                        placeholder: 'Seleccione un barrio',
                        width: '100%',
                        allowClear: true
                    });
                }
            }

            function cargarBarrios(codComuna) {
                if (!codComuna) {
                    renderBarrios([]);
                    return;
                }
                if (hasSelect2) {
                    jQuery(barrioSelect).select2('destroy');
                }
                barrioSelect.innerHTML = '<option value="">Cargando...</option>';
                if (hasSelect2) {
                    jQuery(barrioSelect).select2({
                        placeholder: 'Cargando...',
                        width: '100%',
                        allowClear: true
                    });
                }
                fetch('APIS/register_student/get_barrios.php?comuna=' + encodeURIComponent(codComuna))
                    .then(function(response) { return response.json(); })
                    .then(function(data) {
                        renderBarrios(data);
                    })
                    .catch(function() {
                        renderBarrios([]);
                    });
            }

            if (hasSelect2) {
                jQuery(comunaSelect).select2({
                    placeholder: 'Seleccione una comuna/corregimiento',
                    width: '100%',
                    allowClear: true
                });
                jQuery(barrioSelect).select2({
                    placeholder: 'Seleccione primero una comuna',
                    width: '100%',
                    allowClear: true
                });
                // Select2 dispara el cambio vía jQuery; escuchar con .on() garantiza el cascado.
                jQuery(comunaSelect).on('change', function() {
                    const val = jQuery(comunaSelect).val() || '';
                    const codComuna = val.indexOf(' - ') !== -1 ? val.split(' - ')[0] : val;
                    cargarBarrios(codComuna);
                });
            } else {
                comunaSelect.addEventListener('change', function() {
                    const val = comunaSelect.value || '';
                    const codComuna = val.indexOf(' - ') !== -1 ? val.split(' - ')[0] : val;
                    cargarBarrios(codComuna);
                });
            }
        });
    </script>


    <style>
        /* Estilos para la preview de archivos */
        .file-drop-area {
            position: relative;
            border: 2px dashed #ccc;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }

        .file-drop-area.highlight {
            border-color: #066aab;
            background-color: #f0f8ff;
        }

        .file-drop-area .file-drop-icon {
            font-size: 40px;
            margin-bottom: 10px;
            color: #066aab;
        }

        .file-drop-area .file-drop-text {
            font-size: 16px;
            color: #333;
        }

        .file-drop-area.has-preview {
            border-color: #28a745;
            background-color: #e8f5e9;
        }

        .file-drop-area.has-preview .file-drop-icon {
            color: #28a745;
        }



        .file-drop-area.has-preview .file-drop-text {
            color: #333;
        }

        .preview-image-container {
            position: relative;
            display: inline-block;
            margin: 10px 0;
        }

        .preview-image {
            max-width: 100%;
            max-height: 150px;
            object-fit: cover;
            border-radius: 5px;
        }

        .swal-content-no-scroll {
            overflow-x: hidden !important;
            width: 100% !important;
        }

        /* Ajusta los select para evitar desbordamiento */
        .swal2-select {
            width: 100% !important;
            max-width: 100% !important;
            box-sizing: border-box !important;
            margin: 0 !important;
            /* Elimina márgenes adicionales */
        }
    </style>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Solo ejecutar si estamos en modo "Instituto Triangulo"
        const isInstitutoTriangulo = <?php echo ($institucion_param === 'Instituto Triangulo' || $institucion_param === 'Lorena Rojas') ? 'true' : 'false'; ?>;

        if (isInstitutoTriangulo) {
            const modeSelect = document.getElementById('mode');
            const headquartersSelect = document.getElementById('headquarters');

            // Función para actualizar la sede según la modalidad
            function updateHeadquartersBasedOnMode() {
                const selectedMode = modeSelect.value;

                if (selectedMode === 'Virtual') {
                    // Si la modalidad es Virtual, establecer sede como "No aplica" y deshabilitar
                    headquartersSelect.value = 'No aplica';
                    headquartersSelect.disabled = true;

                    console.log('Modalidad Virtual seleccionada - Sede establecida a "No aplica" y deshabilitada');
                } else if (selectedMode === 'Presencial') {
                    // Si es Presencial, habilitar el selector para que el usuario pueda elegir
                    headquartersSelect.disabled = false;

                    // Opcional: restablecer a la primera opción (vacía)
                    if (headquartersSelect.value === 'No aplica') {
                        headquartersSelect.value = '';
                    }

                    console.log('Modalidad Presencial seleccionada - Sede habilitada para selección');
                } else {
                    // Si no hay modalidad seleccionada, deshabilitar sede
                    headquartersSelect.disabled = true;
                    headquartersSelect.value = '';
                }
            }

            // Escuchar cambios en el selector de modalidad
            modeSelect.addEventListener('change', updateHeadquartersBasedOnMode);

            // Inicializar el estado al cargar la página
            updateHeadquartersBasedOnMode();
        }
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const numberIdInput = document.getElementById('number_id');
        const emailInput = document.getElementById('email');

        function debounce(fn, delay) {
            let timer;
            return function () {
                clearTimeout(timer);
                const ctx = this;
                const args = arguments;
                timer = setTimeout(function () { fn.apply(ctx, args); }, delay);
            };
        }

        function checkDuplicate(field, value) {
            if (!value) return;
            fetch('APIS/register_student/check_duplicate.php?' + field + '=' + encodeURIComponent(value))
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (data.exists) {
                        const msg = field === 'number_id'
                            ? 'Este número de documento ya se encuentra registrado.'
                            : 'Este correo electrónico ya se encuentra registrado.';
                        Swal.fire({
                            title: '¡Atención!',
                            text: msg,
                            icon: 'warning',
                            confirmButtonText: 'Entendido'
                        });
                        if (field === 'number_id') {
                            const very = document.getElementById('number_id_very');
                            if (numberIdInput) numberIdInput.value = '';
                            if (very) very.value = '';
                        } else {
                            const very = document.getElementById('email_very');
                            if (emailInput) emailInput.value = '';
                            if (very) very.value = '';
                        }
                    }
                })
                .catch(function () {});
        }

        if (numberIdInput) {
            numberIdInput.addEventListener('input', debounce(function () {
                const v = this.value.trim();
                if (v.length >= 7) checkDuplicate('number_id', v);
            }, 600));
        }

        if (emailInput) {
            emailInput.addEventListener('input', debounce(function () {
                const v = this.value.trim();
                if (v.indexOf('@') !== -1) checkDuplicate('email', v);
            }, 600));
        }
    });
</script>