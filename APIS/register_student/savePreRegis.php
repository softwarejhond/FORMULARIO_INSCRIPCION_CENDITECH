<?php
// filepath: c:\xampp\htdocs\INNOVA-FORM-REGISTER\APIS\register_student\savePreRegis.php
header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("../../controller/conexion.php");

function normalizar_nombre($nombre) {
    $nombre = mb_strtoupper($nombre, 'UTF-8');
    $nombre = str_replace(
        ['Á','É','Í','Ó','Ú','À','È','Ì','Ò','Ù','Ä','Ë','Ï','Ö','Ü'],
        ['A','E','I','O','U','A','E','I','O','U','A','E','I','O','U'],
        $nombre
    );
    return $nombre;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validar datos requeridos
        $required_fields = [
            'typeID', 'number_id', 'number_id_very', 'email', 'email_very',
            'email2', 'email2_very', 'phone1', 'phone2', 'first_name',
            'first_last', 'second_last', 'sede', 'programa'
        ];

        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("El campo $field es requerido");
            }
        }

        // Validaciones específicas
        if ($_POST['number_id'] !== $_POST['number_id_very']) {
            throw new Exception("Los números de identificación no coinciden");
        }

        if ($_POST['email'] !== $_POST['email_very']) {
            throw new Exception("Los correos electrónicos no coinciden");
        }

        if ($_POST['email2'] !== $_POST['email2_very']) {
            throw new Exception("Los segundos correos electrónicos no coinciden");
        }

        // Verificar si el número de identificación ya existe
        $number_id = $conn->real_escape_string($_POST['number_id']);
        $checkQuery = "SELECT id FROM pre_registrations WHERE number_id = '$number_id'";
        $checkResult = $conn->query($checkQuery);
        if ($checkResult && $checkResult->num_rows > 0) {
            throw new Exception("El número de identificación ya está registrado");
        }

        // Verificar si el email ya existe
        $email = $conn->real_escape_string($_POST['email']);
        $checkEmailQuery = "SELECT id FROM pre_registrations WHERE email = '$email'";
        $checkEmailResult = $conn->query($checkEmailQuery);
        if ($checkEmailResult && $checkEmailResult->num_rows > 0) {
            throw new Exception("El correo electrónico ya está registrado");
        }

        // Obtener información de la sede
        $sede_id = intval($_POST['sede']);
        $sedeQuery = "SELECT name FROM headquarters_registrations WHERE id = $sede_id";
        $sedeResult = $conn->query($sedeQuery);
        if (!$sedeResult || $sedeResult->num_rows === 0) {
            throw new Exception("Sede no válida");
        }
        $sedeData = $sedeResult->fetch_assoc();

        // Preparar datos para inserción
        $type_id = $conn->real_escape_string($_POST['typeID']);
        $number_id_very = $conn->real_escape_string($_POST['number_id_very']);
        $email_very = $conn->real_escape_string($_POST['email_very']);
        $email2 = $conn->real_escape_string($_POST['email2']);
        $email2_very = $conn->real_escape_string($_POST['email2_very']);
        $phone1 = $conn->real_escape_string($_POST['phone1']);
        $phone2 = $conn->real_escape_string($_POST['phone2']);
        $first_name = normalizar_nombre($conn->real_escape_string($_POST['first_name']));
        $second_name = normalizar_nombre($conn->real_escape_string($_POST['second_name'] ?? ''));
        $first_last = normalizar_nombre($conn->real_escape_string($_POST['first_last']));
        $second_last = normalizar_nombre($conn->real_escape_string($_POST['second_last']));
        $sede_name = $conn->real_escape_string($sedeData['name']);
        $programa = $conn->real_escape_string($_POST['programa']);
        $horario = $conn->real_escape_string($_POST['horario'] ?? '');
        
        // Convertir checkboxes a 1 o 0
        $accept_requirements = isset($_POST['accept_requirements']) ? 1 : 0;
        $accept_data_policies = isset($_POST['accept_data_policies']) ? 1 : 0;

        // Insertar en la base de datos
        $insertQuery = "INSERT INTO pre_registrations (
            type_id, number_id, number_id_very, email, email_very, email2, email2_very,
            phone1, phone2, first_name, second_name, first_last, second_last,
            sede_id, sede_name, programa, horario, accept_requirements, accept_data_policies
        ) VALUES (
            '$type_id', '$number_id', '$number_id_very', '$email', '$email_very', '$email2', '$email2_very',
            '$phone1', '$phone2', '$first_name', '$second_name', '$first_last', '$second_last',
            $sede_id, '$sede_name', '$programa', '$horario', $accept_requirements, $accept_data_policies
        )";

        if ($conn->query($insertQuery)) {
            $registration_id = $conn->insert_id;
            
            echo json_encode([
                'success' => true,
                'message' => 'Pre-registro guardado exitosamente',
                'registration_id' => $registration_id
            ]);
        } else {
            throw new Exception("Error al guardar en la base de datos: " . $conn->error);
        }

    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
}
?>