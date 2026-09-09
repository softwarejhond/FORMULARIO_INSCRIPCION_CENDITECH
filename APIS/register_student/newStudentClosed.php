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

// Solo obtener el parámetro de institución
$institucion_param = isset($_GET['institucion']) ? $_GET['institucion'] : null;

$instituciones_especiales = ['SenaTICS'];

if (in_array($institucion_param, $instituciones_especiales)) {

    // SEDES HARDCODEADAS
    $allHeadquarters = [
        ['id' => 28, 'name' => 'Universidad ANDAP - Calle 76'],
        ['id' => 36, 'name' => 'INESCO'],
        ['id' => 29, 'name' => 'Universidad Colegio mayor de Cundinamarca']
    ];

    // PROGRAMAS HARDCODEADOS
    $availablePrograms = [
        'Análisis de Datos',
        'Inteligencia Artificial',
        'Programación',
        'Ciberseguridad'
    ];

    // HORARIOS HARDCODEADOS
    $availableSchedules = [
        // Universidad ANDAP - Calle 76
        ['schedule' => 'LUNES A VIERNES DE 06:00 PM A 10:00 PM', 'mode' => 'Presencial', 'headquarters' => 'Universidad ANDAP - Calle 76', 'program' => 'Análisis de Datos'],
        ['schedule' => 'LUNES A VIERNES DE 06:00 PM A 10:00 PM', 'mode' => 'Presencial', 'headquarters' => 'Universidad ANDAP - Calle 76', 'program' => 'Inteligencia Artificial'],
        ['schedule' => 'LUNES A VIERNES DE 06:00 PM A 10:00 PM', 'mode' => 'Presencial', 'headquarters' => 'Universidad ANDAP - Calle 76', 'program' => 'Ciberseguridad'],

        // INESCO
        ['schedule' => 'LUNES A VIERNES DE 06:00 PM A 10:00 PM', 'mode' => 'Presencial', 'headquarters' => 'INESCO', 'program' => 'Análisis de Datos'],
        ['schedule' => 'LUNES A VIERNES DE 06:00 PM A 10:00 PM', 'mode' => 'Presencial', 'headquarters' => 'INESCO', 'program' => 'Inteligencia Artificial'],
        ['schedule' => 'LUNES A VIERNES DE 06:00 PM A 10:00 PM', 'mode' => 'Presencial', 'headquarters' => 'INESCO', 'program' => 'Programación'],

        // Universidad Colegio mayor de Cundinamarca
        ['schedule' => 'LUNES A VIERNES DE 06:00 PM A 10:00 PM', 'mode' => 'Presencial', 'headquarters' => 'Universidad Colegio mayor de Cundinamarca', 'program' => 'Análisis de Datos'],
        ['schedule' => 'LUNES A VIERNES DE 06:00 PM A 10:00 PM', 'mode' => 'Presencial', 'headquarters' => 'Universidad Colegio mayor de Cundinamarca', 'program' => 'Inteligencia Artificial'],
        ['schedule' => 'LUNES A VIERNES DE 06:00 PM A 10:00 PM', 'mode' => 'Presencial', 'headquarters' => 'Universidad Colegio mayor de Cundinamarca', 'program' => 'Programación'],

    ];
} else {
    echo "<div class='alert alert-danger mt-4'>Institución no válida o no especificada.</div>";
    exit;

}

$formConfig = include 'process_form_register_closed.php'; // Asegúrate de que el archivo de configuración esté correcto.

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
        $first_name = $_POST['first_name'] ?? '';
        $second_name = $_POST['second_name'] ?? '';
        $first_last = $_POST['first_last'] ?? '';
        $second_last = $_POST['second_last'] ?? '';
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
        $availability = $_POST['availability'] ?? '';
        $mode = $_POST['mode'] ?? '';
        $headquarters = $_POST['headquarters'] ?? '';
        $program = $_POST['program'] ?? '';
        $schedules = $_POST['schedules'] ?? '';
        $schedules_alternative = $_POST['schedules_alternative'] ?? '';
        $prior_knowledge = $_POST['prior_knowledge'] ?? '';
        $level = $_POST['level'] ?? '';
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

        // Subir documento frontal
        $idFront = '';
        if (isset($_FILES['file_front_id']) && $_FILES['file_front_id']['error'] == 0) {
            $extension = pathinfo($_FILES['file_front_id']['name'], PATHINFO_EXTENSION);
            $idFront = $number_id . '_Front.' . $extension;
            move_uploaded_file($_FILES['file_front_id']['tmp_name'], 'files/idFilesFront/' . $idFront);
        }

        // Subir documento trasero
        $idBack = '';
        if (isset($_FILES['file_back_id']) && $_FILES['file_back_id']['error'] == 0) {
            $extension = pathinfo($_FILES['file_back_id']['name'], PATHINFO_EXTENSION);
            $idBack = $number_id . '_Back.' . $extension;
            move_uploaded_file($_FILES['file_back_id']['tmp_name'], 'files/idFilesBack/' . $idBack);
        }

        // Verificar si el aspirante ya existe
        $queryCheck = "SELECT * FROM user_register WHERE number_id = '$number_id'";
        $result = $conn->query($queryCheck);

        if ($result->num_rows > 0) {
            // Actualizar los datos del usuario existente
            $queryUpdate = "UPDATE user_register SET
                typeID = '$typeID',
                number_id_very = '$number_id_very',
                first_name = '$first_name',
                second_name = '$second_name',
                first_last = '$first_last',
                second_last = '$second_last',
                birthdate = '$birthdate',
                expedition_date = '$expedition_date',
                gender = '$gender',
                marital_status = '$marital_status',
                email = '$email',
                email_very = '$email_very',
                first_phone = '$first_phone',
                second_phone = '$second_phone',
                emergency_contact_name = '$emergency_contact_name',
                emergency_contact_number = '$country_code3 $emergency_contact_number',
                nationality = '$nationality',
                department = '$department',
                municipality = '$municipality',
                address = '$address',
                latitud = '$latitud',
                longitud = '$longitud',
                people_charge = '$people_charge',
                vulnerable_population = '$vulnerable_population',
                vulnerable_type = '$vulnerable_type',
                ethnic_group = '$ethnic_group',
                stratum = '$stratum',
                residence_area = '$residence_area',
                country_person = '$country_person',
                training_level = '$training_level',
                occupation = '$occupation',
                time_obligations = '$time_obligations',
                motivations_belong_program = '$motivations_belong_program',
                current_situation = '$current_situation',
                impediment_complete_course = '$impediment_complete_course',
                availability = '$availability',
                mode = '$mode',
                headquarters = '$headquarters',
                institution = 'SenaTICS',
                program = '$program',
                schedules = '$schedules',
                schedules_alternative = '$schedules_alternative',
                prior_knowledge = '$prior_knowledge',
                level = '$level',
                languages = '$languages',
                languages_level = '$languages_level',
                medical_condition = '$medical_condition',
                disability = '$disability',
                type_disability = '$type_disability',
                pregnancy = '$pregnancy',
                technologies = '$technologies',
                internet = '$internet',
                knowledge_program = '$knowledge_program',
                accept_requirements = '$accept_requirements',
                accepts_tech_talent = '$accepts_tech_talent',
                accept_data_policies = '$accept_data_policies',
                file_front_id = '$idFront',
                file_back_id = '$idBack',
                status = 1,
                dayUpdate = NOW()
            WHERE number_id = '$number_id'";

            if ($conn->query($queryUpdate) === TRUE) {
                // --- ENVÍO DE CORREO TAMBIÉN EN ACTUALIZACIÓN ---
                $query = "SELECT * FROM smtpConfig WHERE id=3";
                if (mysqli_query($conn, $query)) {
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
                        $mail->Host = $host;
                        $mail->SMTPAuth = true;
                        $mail->Username = $emailSmtp;
                        $mail->Password = $password;
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port = $port;
                        $mail->SMTPOptions = array(
                            'ssl' => array(
                                'verify_peer' => false,
                                'verify_peer_name' => false,
                                'allow_self_signed' => true
                            )
                        );
                        $mail->setFrom('noreply@utinnova.co', 'Servicio al cliente');
                        $mail->CharSet = 'UTF-8';
                        $mail->addAddress($email);
                        $mail->isHTML(true);
                        $mail->Subject = '¡Bienvenido al Bootcamp de ' . $program . ' de Talento Tech del MINTIC!';

                        // Definir las URLs para cada programa
                        $programKey = mb_strtolower(trim($program));
                        $programUrls = [
                            'análisis de datos' => 'https://dashboard.utinnova.co/preKnowAnalysis.php',
                            'ciberseguridad' => 'https://dashboard.utinnova.co/preKnowCybersecurity.php',
                            'inteligencia artificial' => 'https://dashboard.utinnova.co/preKnowIntelligence.php',
                            'programación' => 'https://dashboard.utinnova.co/preKnowPrograming.php',
                            'blockchain' => 'https://dashboard.utinnova.co/preKnowBlockchain.php',
                            'Computración en la nube' => 'https://dashboard.utinnova.co/preKnowArchitecture.php'
                        ];
                        $programUrl = isset($programUrls[$programKey]) ? $programUrls[$programKey] : '#';

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
                                </style>
                            </head>
                            <body>
                                <div class='container'>
                                    <div class='header'>
                                        <h1>¡Bienvenido al Bootcamp de $program!</h1>
                                    </div>
                                    <div class='content'>
                                        <p>Hola <b>$first_name</b>,</p>
                                        <p>¡Felicitaciones! 🎉 Nos emociona darte la bienvenida al <b>Bootcamp de $program</b> de Talento Tech del MINTIC.</p>
                                        <p>Este Bootcamp es el primer paso hacia un futuro lleno de posibilidades en una de las áreas más demandadas del mercado. Aprenderás habilidades clave, trabajarás en proyectos prácticos y te prepararás para enfrentar los desafíos del mundo digital.</p>
                                        <h3>Próximos Pasos:</h3>
                                        <ol>
                                            <li><b>Realiza tu Prueba de Saberes:</b><br>
                                                Antes de iniciar el Bootcamp, necesitamos que completes una Prueba de Saberes. Esta evaluación nos ayudará a reconocer tus conocimientos previos y asignarte al nivel que mejor se adapte a tus necesidades de aprendizaje.<br>
                                                <a class='button w-100' href='$programUrl' target='_blank'>¡Diligencia aquí el formulario de presaberes haciendo click aquí!</a>
                                                <div class='link-fallback'>
                                                    <b>Si el botón no funciona, copia y pega este enlace en tu navegador:</b><br>
                                                    <a href='$programUrl' target='_blank'>$programUrl</a>
                                                </div>
                                            </li>
                                            <li><b>Revisa tu correo:</b><br>
                                                Después de la prueba, te enviaremos toda la información necesaria para comenzar tu formación: horarios, plataforma y recursos.</li>
                                            <li><b>Prepárate para el inicio:</b><br>
                                                Asegúrate de contar con un dispositivo adecuado y una conexión estable a internet para sacar el máximo provecho del programa.</li>
                                            <li>Esta prueba no es eliminatoria, y su único objetivo es validar tú nivel de conocimientos para ubicarte correctamente dentro del programa.</li>
                                        </ol>
                                        <p>Si tienes alguna duda o necesitas apoyo, no dudes en contactarnos a través de este correo. ¡Estamos aquí para ayudarte en cada etapa de tu formación!</p>
                                        <p>Gracias por confiar en nosotros y ser parte de esta gran comunidad. ¡Nos vemos pronto futuro campista! 🚀</p>
                                    </div>
                                    <div class='footer'>
                                        <p>Equipo Talento Tech - MINTIC</p>
                                    </div>
                                </div>
                            </body>
                            </html>";
                        $mail->Body = $mensaje;
                        $mail->send();

                        echo "<script>
                    Swal.fire({
                        title: '¡Actualizado!',
                        text: 'Tus datos han sido actualizados correctamente y se ha enviado el correo de bienvenida.',
                        icon: 'success',
                        showConfirmButton: false,
                        timer: 4000,
                    });
                </script>";
                    } catch (Exception $e) {
                        echo '<div class="jumbotron alert-danger text-center">
                        <h1 class="display-4"><b>Error al enviar el correo</b></h1>
                        <p class="lead">No se pudo enviar el correo a ' . $email . '. Error: ' . $mail->ErrorInfo . '</p>
                    </div>';
                    }
                } else {
                    echo '<div class="alert alert-danger">Error al actualizar los datos: ' . mysqli_error($conn) . '</div>';
                }
            } else {
                echo "<script>
            Swal.fire({
                title: 'Error',
                text: 'No se pudo actualizar el registro: " . $conn->error . "',
                icon: 'error',
                showConfirmButton: true,
            });
        </script>";
            }
        } else {
            // Insertar los datos en la base de datos
            $queryInsert = "INSERT INTO user_register (
                typeID, number_id, number_id_very, first_name, second_name, first_last, second_last, birthdate, expedition_date, gender, marital_status, email, 
                email_very, first_phone, second_phone, emergency_contact_name, emergency_contact_number, nationality, department, 
                municipality, address, latitud, longitud, people_charge, vulnerable_population, vulnerable_type, ethnic_group, stratum, 
                residence_area, country_person, lote, training_level, occupation, time_obligations, motivations_belong_program, current_situation, 
                impediment_complete_course, availability, mode, headquarters, institution, program, schedules, schedules_alternative, prior_knowledge,level, languages, languages_level, 
                medical_condition, disability, type_disability, pregnancy, technologies, internet, knowledge_program, accept_requirements, accepts_tech_talent, 
                accept_data_policies, file_front_id, file_back_id, status, creationDate
            ) VALUES (
                '$typeID', '$number_id', '$number_id_very', '$first_name', '$second_name', '$first_last', '$second_last', '$birthdate', '$expedition_date', 
                '$gender', '$marital_status', '$email', '$email_very', '$first_phone', '$second_phone', '$emergency_contact_name', 
                '$country_code3 $emergency_contact_number', '$nationality', '$department', '$municipality', '$address', '$latitud', '$longitud', '$people_charge', 
                '$vulnerable_population', '$vulnerable_type', '$ethnic_group', '$stratum', '$residence_area', '$country_person', $lote, '$training_level', '$occupation', 
                '$time_obligations', '$motivations_belong_program', '$current_situation', '$impediment_complete_course', '$availability', 
                '$mode', '$headquarters', '$institution', '$program', '$schedules', '$schedules_alternative', '$prior_knowledge', '$level','$languages', '$languages_level', '$medical_condition', '$disability','$type_disability',
                '$pregnancy', '$technologies', '$internet', '$knowledge_program', '$accept_requirements', '$accepts_tech_talent', 
                '$accept_data_policies', '$idFront', '$idBack',1,'$fechaHoraActual'
            )";

            if ($conn->query($queryInsert) === TRUE) {
                // Debug: ver qué valores están llegando en el POST
                error_log("POST recibido: " . print_r($_POST, true));

                // Continuar con el flujo normal (envío de correo, etc.)
                echo "<script>
                    Swal.fire({
                        title: '¡Exitoso!',
                        text: 'Datos registrados con éxito, recuerda revisar tu correo electrónico',
                        icon: 'success',
                        showConfirmButton: false,
                        timer: 2000,
                    });
                </script>";

                // Define la consulta que quieres ejecutar
                $query = "SELECT * FROM smtpConfig WHERE id=3"; // Asegúrate de que esta consulta tenga sentido en tu lógica

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
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Habilita SSL (seguridad)
                        $mail->Port = $port; // Puerto SSL

                        $mail->SMTPOptions = array(
                            'ssl' => array(
                                'verify_peer' => false,
                                'verify_peer_name' => false,
                                'allow_self_signed' => true
                            )
                        );

                        $mail->setFrom('noreply@utinnova.co', 'Servicio al cliente'); // Remitente del correo                            
                        $mail->CharSet = 'UTF-8';  // Establece la codificación en UTF-8
                        $mail->addAddress($email);

                        $mail->isHTML(true);
                        $mail->Subject = '¡Bienvenido al Bootcamp de ' . $program . ' de Talento Tech del MINTIC!';

                        // Definir las URLs para cada programa
                        $programKey = mb_strtolower(trim($program));
                        $programUrls = [
                            'análisis de datos' => 'https://dashboard.utinnova.co/preKnowAnalysis.php',
                            'ciberseguridad' => 'https://dashboard.utinnova.co/preKnowCybersecurity.php',
                            'inteligencia artificial' => 'https://dashboard.utinnova.co/preKnowIntelligence.php',
                            'programación' => 'https://dashboard.utinnova.co/preKnowPrograming.php',
                            'blockchain' => 'https://dashboard.utinnova.co/preKnowBlockchain.php',
                            'Computración en la nube' => 'https://dashboard.utinnova.co/preKnowArchitecture.php'
                        ];
                        $programUrl = isset($programUrls[$programKey]) ? $programUrls[$programKey] : '#';
                        // Aquí va tu mensaje HTML
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
                                        /* Letra blanca */
                                        text-decoration: none;
                                        /* Sin subrayado */
                                        font-weight: bold;
                                        border-radius: 5px;
                                        text-align: center;
                                    }
                        
                                    a.button:hover,
                                    a.button:visited {
                                        color: #F9B233;
                                        /* Mantener el texto blanco incluso cuando el enlace se haya visitado o esté en hover */
                                        text-decoration: none;
                                        /* Eliminar subrayado en hover */
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
                                </style>
                            </head>
                        
                            <body>
                                <div class='container'>
                                    <div class='header'>
                                        <h1>¡Bienvenido al Bootcamp de $program!</h1>
                                    </div>
                                    <div class='content'>
                                        <p>Hola <b>$first_name</b>,</p>
                                        <p>¡Felicitaciones! 🎉 Nos emociona darte la bienvenida al <b>Bootcamp de $program</b> de Talento Tech del MINTIC.</p>
                                        <p>Este Bootcamp es el primer paso hacia un futuro lleno de posibilidades en una de las áreas más demandadas del mercado. Aprenderás habilidades clave, trabajarás en proyectos prácticos y te prepararás para enfrentar los desafíos del mundo digital.</p>
                                        <h3>Próximos Pasos:</h3>
                                        <ol>
                                            <li><b>Realiza tu Prueba de Saberes:</b><br>
                                                Antes de iniciar el Bootcamp, necesitamos que completes una Prueba de Saberes. Esta evaluación nos ayudará a reconocer tus conocimientos previos y asignarte al nivel que mejor se adapte a tus necesidades de aprendizaje.<br>
                                                <a class='button w-100' href='$programUrl' target='_blank'>¡Diligencia aquí el formulario de presaberes haciendo click aquí!</a>
                                                <div class='link-fallback'>
                                                    <b>Si el botón no funciona, copia y pega este enlace en tu navegador:</b><br>
                                                    <a href='$programUrl' target='_blank'>$programUrl</a>
                                                </div>
                                            </li>
                                            <li><b>Revisa tu correo:</b><br>
                                                Después de la prueba, te enviaremos toda la información necesaria para comenzar tu formación: horarios, plataforma y recursos.</li>
                                            <li><b>Prepárate para el inicio:</b><br>
                                                Asegúrate de contar con un dispositivo adecuado y una conexión estable a internet para sacar el máximo provecho del programa.</li>
                                            <li>Esta prueba no es eliminatoria, y su único objetivo es validar tú nivel de conocimientos para ubicarte correctamente dentro del programa.</li>
                                        </ol>
                                        <p>Si tienes alguna duda o necesitas apoyo, no dudes en contactarnos a través de este correo. ¡Estamos aquí para ayudarte en cada etapa de tu formación!</p>
                                        <p>Gracias por confiar en nosotros y ser parte de esta gran comunidad. ¡Nos vemos pronto futuro campista! 🚀</p>
                                    </div>
                                    <div class='footer'>
                                        <p>Equipo Talento Tech - MINTIC</p>
                                    </div>
                                </div>
                            </body>
                        
                            </html>";



                        $mail->Body = $mensaje;
                        //$mail->addEmbeddedImage($urlpicture, 'cuerpo');

                        $mail->send();

                        echo "
<script>
    // Esto asegura que el código se ejecute después de que el DOM esté cargado
    document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
            title: '¡Exitoso! 🎉',
            text: 'Datos registrados con éxito, recuerda revisar tu correo electrónico en la carpeta de spam en caso de que no este en la bandeja de entrada',
            icon: 'success',
            showConfirmButton: false,
            timer: 7000,
        });
    });
</script>";
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
    <div class="shadow-lg p-3 mb-5 bg-body-tertiary rounded">
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
                global $formConfig, $selectedHeadquarter, $selectedMode, $availablePrograms, $institucion_param, $allHeadquarters;
                $field = $formConfig[$fieldName] ?? [];
                $type = $field['type'] ?? 'text';
                $label = $field['label'] ?? ucfirst($fieldName);
                $attributes = generateAttributes($field['attributes'] ?? []);

                echo "<div class='form-group'>";
                echo "<label class='bold-label'>$label</label>";

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
                    // Modalidad siempre "Presencial"
                    echo "<select class='form-control mb-3' name='mode' id='mode' required>";
                    echo "<option value='Presencial' selected>Presencial</option>";
                    echo "</select>";
                    // Campo oculto para enviar el valor en el formulario
                    echo "<input type='hidden' name='mode' value='Presencial'>";
                } elseif ($fieldName === 'headquarters') {
                    // Selector con las sedes obtenidas por la consulta
                    echo "<select class='form-control mb-3' name='headquarters' id='headquarters' required>";
                    echo "<option value=''>Seleccione una sede</option>";
                    foreach ($allHeadquarters as $hq) {
                        echo "<option value='" . $hq['name'] . "'>" . $hq['name'] . "</option>";
                    }
                    echo "</select>";
                    // Campo oculto para institution
                    echo "<input type='hidden' name='institution' value='" . $institucion_param . "'>";
                } elseif ($fieldName === 'program') {
                    // Campo de programa filtrado por la sede
                    $options = $field['options'] ?? [];
                    echo "<select class='form-control mb-3' name='program' id='program' required>";
                    echo "<option value=''>Seleccione un programa</option>";

                    // Solo mostrar programas disponibles para esta sede
                    foreach ($availablePrograms as $program) {
                        echo "<option value='$program'>$program</option>";
                    }
                    echo "</select>";

                    // Si no hay programas disponibles, mostrar mensaje
                    if (empty($availablePrograms)) {
                        echo "<small class='text-muted'>No hay programas disponibles para esta sede.</small>";
                    }
                } elseif ($fieldName === 'schedules') {
                    // Campo de horarios principal
                    echo "<select class='form-control mb-3' name='schedules' id='schedules' required>";
                    echo "<option value=''>Primero seleccione programa y sede</option>";
                    echo "</select>";
                } elseif ($fieldName === 'schedules_alternative') {
                    // Campo de horario alternativo
                    echo "<select class='form-control mb-3' name='schedules_alternative' id='schedules_alternative' required>";
                    echo "<option value=''>Primero seleccione horario principal</option>";
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
                } else if ($fieldName == 'department') {
                    echo "<div class='form-group'>";
                    echo "<label  class='form-label text-magenta-dark'> " . ucfirst(str_replace('_', ' ', '')) . "</label>";
                    echo "<select class='form-control' required>";
                    echo "<option value=''>Seleccionar</option>";
                    echo "</select>";
                    echo "</div>";
                } else if ($fieldName == 'municipality') {
                    echo "<div class='form-group' >";
                    echo "<label  class='form-label text-magenta-dark'>" . ucfirst(str_replace('_', ' ', '')) . "</label>";
                    echo "<select name='$fieldName' id='municipios' class='form-control' required>";
                    echo "<option value=''>Seleccionar</option>";
                    echo "</select>";
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
                    echo "<p><a href='https://www.mintic.gov.co/portal/inicio/Secciones-auxiliares/Politicas/2627:Politicas-de-Privacidad-y-Condiciones-de-Uso' target='_blank'>Puedes consultar los requisitos de la convocatoria haciendo click aquí</a></p>";
                    echo "</div>";
                } elseif ($fieldName == 'accepts_tech_talent') {
                    echo "<div class='form-group'>";
                    // Campo de aceptación de requisitos
                    echo "<div class='form-check form-check'>";
                    echo "<input type='checkbox' class='form-check-input custom-checkbox' name='accepts_tech_talent' id='accepts_tech_talent' value='Sí' required>";
                    echo "<label class='form-check-label' for='accepts_tech_talent'>Acepta la carta de compromiso de talento Tech</label>";
                    echo "</div>";
                    // Enlace con los requisitos
                    echo "<p><a href='https://talentotech.utinnova.co/acta-de-compromiso/' target='_blank'>Puedes consultar los requisitos de la convocatoria haciendo click aquí</a></p>";
                    echo "</div>";
                } elseif ($fieldName == 'accept_data_policies') {
                    echo "<div class='form-group'>";
                    // Campo de aceptación de requisitos
                    echo "<div class='form-check form-check'>";
                    echo "<input type='checkbox' class='form-check-input custom-checkbox' name='accept_data_policies' id='accept_data_policies' value='Sí' required>";
                    echo "<label class='form-check-label' for='Accept_data_policies'>Confirmo que he leído y acepto las políticas de tratamiento de datos personales</label>";
                    echo "</div>";
                    // Enlace con los requisitos
                    echo "<p><a href='https://talentotech.utinnova.co/politica-de-tratamiento-de-datos/' target='_blank'>Puedes consultar los requisitos de la convocatoria haciendo click aquí</a></p>";
                    echo "</div>";
                } elseif ($fieldName == 'file_front_id') {
                    echo "<div class='form-group'>";
                    // Área de carga de archivo con arrastrar y soltar
                    echo "
                <div class='file-drop-area' id='file_front_drop_area'>
                    <div class='file-drop-icon'>
                        <i class='bi bi-cloud-upload'></i>
                    </div>
                    <span class='file-drop-text'>Arrastra y suelta tu archivo aquí o haz clic para seleccionarlo</span>
                    <input type='file' name='file_front_id' id='file_front_drag' class='file-input' accept='.jpg, .jpeg, .png' required onchange='validateImageFile(this)' />
                    <div class='file-preview' id='file_front_preview'></div>
                </div>";
                    // Texto de ayuda
                    echo "<small>El archivo debe ser  JPG, JPEG o PNG y no superar los 2MB.</small>";

                    echo "</div>";
                } elseif ($fieldName == 'file_back_id') {
                    echo "<div class='form-group'>";

                    // Área de carga de archivo con arrastrar y soltar
                    echo "
                <div class='file-drop-area' id='file_back_drop_area'>
                    <div class='file-drop-icon'>
                        <i class='bi bi-cloud-upload'></i>
                    </div>
                    <span class='file-drop-text'>Arrastra y suelta tu archivo aquí o haz clic para seleccionarlo</span>
                    <input type='file' name='file_back_id' id='file_back_drag' class='file-input' accept='.jpg, .jpeg, .png' required />
                    <div class='file-preview' id='file_back_preview'></div>
                </div>";

                    // Texto de ayuda
                    echo "<small>El archivo debe ser JPG, JPEG o PNG y no superar los 2MB.</small>";

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
    <div class="modal fade" id="scheduleModal" tabindex="-1" aria-labelledby="infoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="infoModalLabel">Información de Horario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="modalMessage">
                        Estimado usuario, los cursos se habilitarán de manera gradual considerando la cantidad de usuarios matriculados. Le invitamos cordialmente a seleccionar el horario que mejor se adapte a su disponibilidad, tomando en cuenta la oferta actual. Agradecemos su comprensión y colaboración.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" style="background-color:#066aab ; color:white" data-bs-dismiss="modal">Entendido</button>
                </div>
            </div>
        </div>
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
        // Referencias a los elementos
        const modeSelect = document.getElementById('mode');
        const headquartersSelect = document.getElementById('headquarters');
        const programSelect = document.getElementById('program');
        const schedulesSelect = document.getElementById('schedules');
        const schedulesAlternativeSelect = document.getElementById('schedules_alternative');

        // CAMBIO 4: Actualizar el script para usar el nuevo nombre de variable
        const schedules = <?php echo json_encode($availableSchedules ?? []); ?>;

        console.log('Horarios cargados:', schedules); // Debug

        function updateScheduleOptions() {
            const selectedProgram = programSelect ? programSelect.value : '';
            const currentMode = modeSelect ? modeSelect.value : '';
            const currentHeadquarters = headquartersSelect ? headquartersSelect.value : '';

            console.log('Valores seleccionados:', {
                programa: selectedProgram,
                modalidad: currentMode,
                sede: currentHeadquarters
            });

            schedulesSelect.innerHTML = '<option value="">Seleccione horario principal</option>';
            schedulesAlternativeSelect.innerHTML = '<option value="">Seleccione horario alternativo</option>';

            if (!selectedProgram || !currentMode || !currentHeadquarters) {
                const message = 'Seleccione programa, modalidad y sede primero';
                schedulesSelect.innerHTML = `<option value="">${message}</option>`;
                schedulesAlternativeSelect.innerHTML = `<option value="">${message}</option>`;
                return;
            }

            // Filtrar horarios con normalización
            const filteredSchedules = Array.isArray(schedules) ? schedules.filter(sch => {
                const normalize = str => str ? str.trim().toLowerCase() : '';
                const matchProgram = normalize(sch.program) === normalize(selectedProgram);
                const matchMode = normalize(sch.mode) === normalize(currentMode);
                const matchHeadquarters = normalize(sch.headquarters) === normalize(currentHeadquarters);

                console.log('Comparando:', {
                    horario: sch.schedule,
                    programa: [normalize(sch.program), normalize(selectedProgram), matchProgram],
                    modalidad: [normalize(sch.mode), normalize(currentMode), matchMode],
                    sede: [normalize(sch.headquarters), normalize(currentHeadquarters), matchHeadquarters],
                    coincide: matchProgram && matchMode && matchHeadquarters
                });

                return matchProgram && matchMode && matchHeadquarters;
            }) : [];

            console.log('Horarios filtrados:', filteredSchedules);

            if (filteredSchedules.length === 0) {
                const alertMessage = `No hay horarios disponibles para ${selectedProgram} en modalidad ${currentMode} en la sede ${currentHeadquarters}`;

                schedulesSelect.innerHTML = `<option value="">No hay horarios disponibles</option>`;
                schedulesAlternativeSelect.innerHTML = `<option value="">No hay horarios disponibles</option>`;

                Swal.fire({
                    title: '¡Atención!',
                    text: alertMessage,
                    icon: 'warning',
                    confirmButtonColor: '#066aab',
                });

                return;
            }

            // Agregar opciones de horarios encontrados
            filteredSchedules.forEach(sch => {
                const opt = document.createElement('option');
                opt.value = sch.schedule;
                opt.textContent = sch.schedule;
                schedulesSelect.appendChild(opt.cloneNode(true));
            });

            console.log('Horarios agregados al select:', filteredSchedules.length);
        }

        function updateAlternativeSchedules() {
            const selectedProgram = programSelect.value;
            const selectedSchedule = schedulesSelect.value;
            const currentMode = modeSelect.value;
            const currentHeadquarters = headquartersSelect.value;

            schedulesAlternativeSelect.innerHTML = '<option value="">Seleccione horario alternativo</option>';

            if (!selectedSchedule) {
                return;
            }

            const filteredSchedules = schedules.filter(sch =>
                sch.program === selectedProgram &&
                sch.mode === currentMode &&
                sch.headquarters === currentHeadquarters &&
                sch.schedule !== selectedSchedule
            );

            if (filteredSchedules.length === 0) {
                const opt = document.createElement('option');
                opt.value = selectedSchedule;
                opt.textContent = selectedSchedule;
                schedulesAlternativeSelect.appendChild(opt);
                return;
            }

            filteredSchedules.forEach(sch => {
                const opt = document.createElement('option');
                opt.value = sch.schedule;
                opt.textContent = sch.schedule;
                schedulesAlternativeSelect.appendChild(opt);
            });
        }

        if (programSelect) {
            programSelect.addEventListener('change', updateScheduleOptions);
        }

        if (schedulesSelect) {
            schedulesSelect.addEventListener('change', updateAlternativeSchedules);
        }

        if (modeSelect) {
            modeSelect.addEventListener('change', updateScheduleOptions);
        }

        if (headquartersSelect) {
            headquartersSelect.addEventListener('change', updateScheduleOptions);
        }

        console.log('Sistema de horarios inicializado');
    </script>
    <script>
        document.getElementById('schedules').addEventListener('change', function() {
            if (this.value !== "") {
                var modal = new bootstrap.Modal(document.getElementById('scheduleModal'));
                modal.show();
            }
        });
    </script>
    <script>
        // Referencias a los elementos
        const priorKnowledgeSelect = document.getElementById('prior_knowledge');
        const levelSelect = document.getElementById('level');
        levelSelect.innerHTML = `
                <option value="">No has contestado a la pregunta anterior</option>
            `;
        levelSelect.removeAttribute('readonly'); // Quitar readonly

        // Función para actualizar las opciones del campo nivel
        function updateLevelOptions() {
            const selectedKnowledge = priorKnowledgeSelect.value;

            if (selectedKnowledge === 'Sí') {
                // Restaurar todas las opciones y habilitar el campo
                levelSelect.innerHTML = `
                <option value="">Seleccionar</option>
                <option value="Explorador">Explorador (Conocimientos básicos)</option>
                <option value="Integrador">Integrador (Conocimientos intermedios)</option>
                <option value="Innovador">Innovador (Conocimientos avanzados)</option>
            `;
                levelSelect.removeAttribute('readonly'); // Quitar readonly
            } else if (selectedKnowledge === 'No') {
                // Establecer el nivel por defecto en "Explorador" y hacerlo solo lectura
                levelSelect.innerHTML = `
                <option value="Explorador" selected>Explorador (Conocimientos básicos)</option>
            `;
                levelSelect.setAttribute('readonly', true); // Añadir readonly
            }
        }

        // Evento al cambiar la selección de conocimientos previos

        priorKnowledgeSelect.addEventListener('change', updateLevelOptions);
    </script>
    <script>
        // Referencias a los elementos
        const disabilitySelect = document.getElementById('disability');
        const typeDisabilitySelect = document.getElementById('type_disability');
        typeDisabilitySelect.innerHTML = `
                <option value="">No has contestado a la pregunta anterior</option>
            `;
        levelSelect.removeAttribute('readonly'); // Quitar readonly
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
                'address': document.querySelector('[name="address"]'),
                'people_charge': document.querySelector('[name="people_charge"]'),
                'vulnerable_population': document.querySelector('[name="vulnerable_population"]'),
                'vulnerable_type': document.querySelector('[name="vulnerable_type"]'),
                'ethnic_group': document.querySelector('[name="ethnic_group"]'),
                'stratum': document.querySelector('[name="stratum"]:checked'), // Para radio buttons
                'residence_area': document.querySelector('[name="residence_area"]'),
                'country_person': document.querySelector('[name="country_person"]'),
                'training_level': document.querySelector('[name="training_level"]'),
                'occupation': document.querySelector('[name="occupation"]'),
                'time_obligations': document.querySelector('[name="time_obligations"]'),
                'motivations_belong_program': document.querySelector('[name="motivations_belong_program"]'),
                'current_situation': document.querySelector('[name="current_situation"]'),
                'impediment_complete_course': document.querySelector('[name="impediment_complete_course"]'),
                'availability': document.querySelector('[name="availability"]'),
                'mode': document.querySelector('[name="mode"]'),
                'headquarters': document.querySelector('[name="headquarters"]'),
                'program': document.querySelector('[name="program"]'),
                'schedules': document.querySelector('[name="schedules"]'),
                'prior_knowledge': document.querySelector('[name="prior_knowledge"]'),
                'level': document.querySelector('[name="level"]'),
                'languages': document.querySelector('[name="languages"]'),
                'languages_level': document.querySelector('[name="languages_level"]'),
                'medical_condition': document.querySelector('[name="medical_condition"]'),
                'disability': document.querySelector('[name="disability"]'),
                'type_disability': document.querySelector('[name="type_disability"]'),
                'pregnancy': document.querySelector('[name="pregnancy"]'),
                'technologies': document.querySelector('[name="technologies"]'),
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
                'address': document.querySelector('[name="address"]'),
                'people_charge': document.querySelector('[name="people_charge"]'),
                'vulnerable_population': document.querySelector('[name="vulnerable_population"]'),
                'vulnerable_type': document.querySelector('[name="vulnerable_type"]'),
                'ethnic_group': document.querySelector('[name="ethnic_group"]'),
                'stratum': document.querySelector('[name="stratum"]:checked'), // Para radio buttons
                'residence_area': document.querySelector('[name="residence_area"]'),
                'country_person': document.querySelector('[name="country_person"]'),
                'training_level': document.querySelector('[name="training_level"]'),
                'occupation': document.querySelector('[name="occupation"]'),
                'time_obligations': document.querySelector('[name="time_obligations"]'),
                'motivations_belong_program': document.querySelector('[name="motivations_belong_program"]'),
                'current_situation': document.querySelector('[name="current_situation"]'),
                'impediment_complete_course': document.querySelector('[name="impediment_complete_course"]'),
                'availability': document.querySelector('[name="availability"]'),
                'mode': document.querySelector('[name="mode"]'),
                'headquarters': document.querySelector('[name="headquarters"]'),
                'program': document.querySelector('[name="program"]'),
                'schedules': document.querySelector('[name="schedules"]'),
                'prior_knowledge': document.querySelector('[name="prior_knowledge"]'),
                'level': document.querySelector('[name="level"]'),
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

            let error = false;
            let message = '';

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
                'availability': 'que indica la Disponibilidad horaria',
                'mode': 'de modalidad de estudio',
                'headquarters': 'Sede',
                'program': 'Programa',
                'schedules': 'Horarios',
                'prior_knowledge': 'Conocimientos previos',
                'level': 'Nivel',
                'languages': 'que inidica si habla otro idioma',
                'languages_level': 'Nivel de idiomas',
                'medical_condition': 'Condición médica',
                'disability': 'Discapacidad',
                'type_disability': 'Tipo de discapacidad',
                'pregnancy': 'Embarazo',
                'technologies': 'Tecnologías',
                'internet': 'Acceso a internet',
                'knowledge_program': 'Conocimiento del programa',
                'accept_requirements': 'Acepta los requisitos',
                'accepts_tech_talent': 'Acepta talento Tech',
                'accept_data_policies': 'Acepta políticas de datos',
                'file_front_id': 'Archivo de identificación (frontal)',
                'file_back_id': 'Archivo de identificación (reverso)'
            };

            // Validar cada campo requerido
            for (const [key, element] of Object.entries(fieldElements)) {
                if (!element) continue; // Si el elemento no existe, continúa

                // Validación especial para checkboxes
                if (element.type === 'checkbox') {
                    if (element.hasAttribute('required') && !element.checked) {
                        error = true;
                        message = `El campo ${fieldNames[key]} es obligatorio.`;
                        if (element.classList) {
                            element.classList.add('is-invalid');
                        }
                        break;
                    } else if (element.classList) {
                        element.classList.remove('is-invalid');
                    }
                }
                // Validación especial para archivos
                else if (element.type === 'file') {
                    if (element.hasAttribute('required') && (!element.files || element.files.length === 0)) {
                        error = true;
                        message = `El campo ${fieldNames[key]} es obligatorio.`;
                        if (element.classList) {
                            element.classList.add('is-invalid');
                        }
                        break;
                    } else if (element.classList) {
                        element.classList.remove('is-invalid');
                    }
                }
                // Validación para radio buttons (ya seleccionado con :checked)
                else if (element.type === 'radio') {
                    if (!element && document.querySelector(`[name="${key}"]`).hasAttribute('required')) {
                        error = true;
                        message = `El campo ${fieldNames[key]} es obligatorio.`;
                        break;
                    }
                }
                // Validación para otros campos (input, select, textarea)
                else {
                    if (element.hasAttribute && element.hasAttribute('required') && !element.value.trim()) {
                        error = true;
                        message = `El campo ${fieldNames[key]} es obligatorio.`;
                        if (element.classList) {
                            element.classList.add('is-invalid');
                        }
                        break;
                    } else if (element.classList) {
                        element.classList.remove('is-invalid');
                    }
                }
            }

            // Si hay un error, mostrar el mensaje y evitar el envío del formulario
            if (error) {
                Swal.fire({
                    title: '¡Error!',
                    text: message,
                    icon: 'error',
                    showConfirmButton: true,
                });
                event.preventDefault();
            } else {
                // Si todo es válido, proceder con el envío del formulario
                Swal.fire({
                    title: '¡Éxito!',
                    text: 'Formulario enviado correctamente.',
                    icon: 'success',
                    showConfirmButton: false,
                    timer: 2000
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
            // Configuración para el frente del documento
            setupFileUpload('file_front_drag', 'file_front_preview', 'file_front_drop_area');

            // Configuración para el reverso del documento
            setupFileUpload('file_back_drag', 'file_back_preview', 'file_back_drop_area');

            function setupFileUpload(inputId, previewId, dropAreaId) {
                const fileInput = document.getElementById(inputId);
                const previewContainer = document.getElementById(previewId);
                const dropArea = document.getElementById(dropAreaId);

                // Evitar comportamiento por defecto cuando se arrastra un archivo
                ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                    dropArea.addEventListener(eventName, preventDefaults, false);
                });

                function preventDefaults(e) {
                    e.preventDefault();
                    e.stopPropagation();
                }

                // Añadir clase de resaltado cuando se arrastra sobre el área
                ['dragenter', 'dragover'].forEach(eventName => {
                    dropArea.addEventListener(eventName, () => {
                        dropArea.classList.add('highlight');
                    });
                });

                // Quitar clase de resaltado cuando se sale del área
                ['dragleave', 'drop'].forEach(eventName => {
                    dropArea.addEventListener(eventName, () => {
                        dropArea.classList.remove('highlight');
                    });
                });

                // Manejar cuando se suelta un archivo
                dropArea.addEventListener('drop', function(e) {
                    const dt = e.dataTransfer;
                    const files = dt.files;

                    if (files.length) {
                        fileInput.files = files;
                        updateFilePreview(files[0]);
                    }
                });

                // Manejar cuando se selecciona un archivo con el selector
                fileInput.addEventListener('change', function() {
                    if (this.files && this.files[0]) {
                        updateFilePreview(this.files[0]);
                    }
                });

                // Función para actualizar la vista previa
                function updateFilePreview(file) {
                    // Validar el tipo de archivo
                    if (!file.type.startsWith('image/')) {
                        Swal.fire({
                            title: '¡Ojo!',
                            text: 'Solo se permiten archivos de tipo imagen',
                            icon: 'warning',
                            showConfirmButton: false,
                            timer: 5000,
                        });
                        fileInput.value = '';
                        previewContainer.innerHTML = '';
                        return;
                    }

                    // Validar tamaño de archivo (20MB máximo)
                    if (file.size > 20 * 1024 * 1024) {
                        Swal.fire({
                            title: '¡Ojo!',
                            text: 'El archivo es demasiado grande. El tamaño máximo permitido es 20MB.',
                            icon: 'warning',
                            showConfirmButton: false,
                            timer: 5000,
                        });
                        fileInput.value = '';
                        previewContainer.innerHTML = '';
                        return;
                    }

                    // Crear vista previa
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        previewContainer.innerHTML = `
                            <div class="preview-image-container">
                                <img src="${e.target.result}" class="preview-image" />
                                <button type="button" class="btn btn-sm btn-danger remove-preview">
                                    <i class="bi bi-x"></i>
                                </button>
                            </div>
                        `;

                        // Cambiar estilo del área de drop
                        dropArea.classList.add('has-preview');

                        // Añadir funcionalidad para eliminar la vista previa
                        const removeButton = previewContainer.querySelector('.remove-preview');
                        if (removeButton) {
                            removeButton.addEventListener('click', function() {
                                fileInput.value = '';
                                previewContainer.innerHTML = '';
                                dropArea.classList.remove('has-preview');
                            });
                        }
                    };

                    // Para solucionar problemas con iOS
                    if (/iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream) {
                        // En iOS, usamos readAsDataURL por compatibilidad
                        reader.readAsDataURL(file);
                    } else {
                        reader.readAsDataURL(file);
                    }
                }
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
        const isInstitutoTriangulo = <?php echo ($institucion_param === 'Instituto Triangulo' || $institucion_param === 'Lorena Rojas' || $institucion_param === 'SenaTICS') ? 'true' : 'false'; ?>;

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