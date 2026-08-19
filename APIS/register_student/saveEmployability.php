<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Conexión a la base de datos
require __DIR__ . '/conexion.php';

if (!$conn || $conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Error de conexión: ' . ($conn ? $conn->connect_error : 'No se creó el objeto de conexión')]);
    exit;
}

$conn->set_charset('utf8mb4');

// Sanitización y recolección de datos
$typeID = $_POST['typeID'] ?? '';
$number_id = intval($_POST['number_id'] ?? 0);
$lote = intval($_POST['lote'] ?? 0);
$first_name = $_POST['first_name'] ?? '';
$second_name = $_POST['second_name'] ?? '';
$first_last = $_POST['first_last'] ?? '';
$second_last = $_POST['second_last'] ?? '';
$email = $_POST['email'] ?? '';
$interest = $_POST['interest'] ?? '';
$start_training_date = $_POST['start_training_date'] ?? '';
$personal_description = $_POST['personal_description'] ?? '';
$localidad = $_POST['localidad'] ?? '';
$nivel_educativo = $_POST['nivel_educativo'] ?? '';
$gender = $_POST['gender'] ?? '';
$work_experience = $_POST['work_experience'] ?? '';
$current_employment_status = $_POST['current_employment_status'] ?? '';
$tech_experience = $_POST['tech_experience'] ?? '';
$job_profile = $_POST['job_profile'] ?? '';
$tech_experience_years = intval($_POST['tech_experience_years'] ?? 0);
$last_tech_role = $_POST['last_tech_role'] ?? '';
$skills_knowledge = $_POST['skills_knowledge'] ?? '';
$desired_role = $_POST['desired_role'] ?? '';
$accept_requirements = isset($_POST['accept_requirements']) ? 1 : 0;
$accept_data_policies = isset($_POST['accept_data_policies']) ? 1 : 0;

// Manejo de arrays (checkbox múltiples)
$digital_skills = isset($_POST['digital_skills']) && is_array($_POST['digital_skills']) ? implode(',', $_POST['digital_skills']) : '';
$soft_skills = isset($_POST['soft_skills']) && is_array($_POST['soft_skills']) ? implode(',', $_POST['soft_skills']) : '';
$professional_networks = isset($_POST['professional_networks']) && is_array($_POST['professional_networks']) ? implode(',', $_POST['professional_networks']) : '';

// Validaciones básicas
if (empty($typeID) || empty($number_id) || empty($first_name) || empty($first_last) || empty($second_last) || empty($email)) {
    echo json_encode(['success' => false, 'message' => 'Faltan campos obligatorios']);
    exit;
}

// Verificar si el email es válido
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Email no válido']);
    exit;
}

function normalizar_nombre($nombre) {
    $nombre = mb_strtoupper($nombre, 'UTF-8');
    $nombre = strtr($nombre, [
        'Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U',
        'À' => 'A', 'È' => 'E', 'Ì' => 'I', 'Ò' => 'O', 'Ù' => 'U',
        'ä' => 'A', 'ë' => 'E', 'ï' => 'I', 'ö' => 'O', 'ü' => 'U',
        'á' => 'A', 'é' => 'E', 'í' => 'I', 'ó' => 'O', 'ú' => 'U',
        'à' => 'A', 'è' => 'E', 'ì' => 'I', 'ò' => 'O', 'ù' => 'U',
        'ñ' => 'Ñ'
    ]);
    return $nombre;
}

$first_name = normalizar_nombre($_POST['first_name'] ?? '');
$second_name = normalizar_nombre($_POST['second_name'] ?? '');
$first_last = normalizar_nombre($_POST['first_last'] ?? '');
$second_last = normalizar_nombre($_POST['second_last'] ?? '');

// Verificar duplicado por number_id
$checkStmt = $conn->prepare("SELECT COUNT(*) FROM employability WHERE number_id = ?");
$checkStmt->bind_param("i", $number_id);
$checkStmt->execute();
$checkStmt->bind_result($count);
$checkStmt->fetch();
$checkStmt->close();

if ($count > 0) {
    echo json_encode(['success' => false, 'message' => 'Ya existe un registro con ese número de identificación.']);
    $conn->close();
    exit;
}

// Preparar consulta (NO incluir 'id' ni 'fecha_registro' ya que son AUTO_INCREMENT y DEFAULT)
$stmt = $conn->prepare(
    "INSERT INTO employability (
        typeID, number_id, lote, first_name, second_name, first_last, second_last, 
        email, interest, start_training_date, personal_description, localidad, 
        nivel_educativo, gender, work_experience, current_employment_status, 
        tech_experience, job_profile, tech_experience_years, last_tech_role, 
        skills_knowledge, digital_skills, soft_skills, professional_networks, 
        desired_role, accept_requirements, accept_data_policies
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
);

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Error en prepare: ' . $conn->error]);
    exit;
}

// Bind parameters - 27 parámetros exactos
$stmt->bind_param(
    "siisssssssssssssssissssssii", // 27 caracteres para 27 parámetros
    $typeID,                       // s - VARCHAR(10)
    $number_id,                    // i - INT
    $lote,                         // i - TINYINT
    $first_name,                   // s - VARCHAR(50)
    $second_name,                  // s - VARCHAR(50)
    $first_last,                   // s - VARCHAR(50)
    $second_last,                  // s - VARCHAR(50)
    $email,                        // s - VARCHAR(100)
    $interest,                     // s - VARCHAR(50)
    $start_training_date,          // s - DATE
    $personal_description,         // s - VARCHAR(255)
    $localidad,                    // s - VARCHAR(50)
    $nivel_educativo,              // s - VARCHAR(50)
    $gender,                       // s - VARCHAR(20)
    $work_experience,              // s - VARCHAR(255)
    $current_employment_status,    // s - VARCHAR(20)
    $tech_experience,              // s - VARCHAR(5)
    $job_profile,                  // s - VARCHAR(255)
    $tech_experience_years,        // i - INT
    $last_tech_role,               // s - VARCHAR(100)
    $skills_knowledge,             // s - VARCHAR(255)
    $digital_skills,               // s - TEXT
    $soft_skills,                  // s - TEXT
    $professional_networks,        // s - TEXT
    $desired_role,                 // s - VARCHAR(100)
    $accept_requirements,          // i - TINYINT(1)
    $accept_data_policies          // i - TINYINT(1)
);

try {
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Datos guardados correctamente']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al guardar: ' . $stmt->error]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error de ejecución: ' . $e->getMessage()]);
}

$stmt->close();
$conn->close();
?>