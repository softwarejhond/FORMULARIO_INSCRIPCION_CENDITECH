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
$first_name = $_POST['first_name'] ?? '';
$second_name = $_POST['second_name'] ?? '';
$first_last = $_POST['first_last'] ?? '';
$second_last = $_POST['second_last'] ?? '';
$email = $_POST['email'] ?? '';
$interest = $_POST['interest'] ?? '';
$start_training_date = $_POST['start_training_date'] ?? '';
$grupos_poblacionales = $_POST['grupos_poblacionales'] ?? '';
$nivel_educativo = $_POST['nivel_educativo'] ?? '';
$gender = $_POST['gender'] ?? '';
$current_employment_status = $_POST['current_employment_status'] ?? '';
$current_tech_job = $_POST['current_tech_job'] ?? '';
$employment_obtained_by = $_POST['employment_obtained_by'] ?? '';
$contract_type = $_POST['contract_type'] ?? '';
$income_level = $_POST['income_level'] ?? '';
$current_job_role = $_POST['current_job_role'] ?? '';
$employment_route_spaces = isset($_POST['employment_route_spaces']) && is_array($_POST['employment_route_spaces']) ? implode(',', $_POST['employment_route_spaces']) : '';
$content_usefulness = intval($_POST['content_usefulness'] ?? 0);
$employment_support = intval($_POST['employment_support'] ?? 0);
$general_satisfaction = $_POST['general_satisfaction'] ?? '';
$improvement_action = $_POST['improvement_action'] ?? '';
$accept_requirements = isset($_POST['accept_requirements']) ? 1 : 0;
$accept_data_policies = isset($_POST['accept_data_policies']) ? 1 : 0;

// Validaciones básicas
if (
    empty($typeID) || empty($number_id) || empty($first_name) || empty($first_last) ||
    empty($second_last) || empty($email) || empty($interest) || empty($start_training_date) ||
    empty($grupos_poblacionales) || empty($nivel_educativo) || empty($gender) ||
    empty($current_employment_status) || empty($current_tech_job) || empty($employment_obtained_by) ||
    empty($contract_type) || empty($income_level) || empty($current_job_role) ||
    empty($general_satisfaction) || empty($improvement_action)
) {
    echo json_encode(['success' => false, 'message' => 'Faltan campos obligatorios']);
    exit;
}

// Verificar si el email es válido
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Email no válido']);
    exit;
}

// Normalizar nombres
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

$first_name = normalizar_nombre($first_name);
$second_name = normalizar_nombre($second_name);
$first_last = normalizar_nombre($first_last);
$second_last = normalizar_nombre($second_last);

// Verificar duplicado por number_id
$checkStmt = $conn->prepare("SELECT COUNT(*) FROM employability_close WHERE number_id = ?");
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

// Preparar consulta (NO incluir 'id' ni 'created_at' ya que son AUTO_INCREMENT y DEFAULT)
$stmt = $conn->prepare(
    "INSERT INTO employability_close (
        typeID, number_id, first_name, second_name, first_last, second_last, email, interest, start_training_date,
        grupos_poblacionales, nivel_educativo, gender, current_employment_status, current_tech_job,
        employment_obtained_by, contract_type, income_level, current_job_role, employment_route_spaces,
        content_usefulness, employment_support, general_satisfaction, improvement_action,
        accept_requirements, accept_data_policies
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
);

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Error en prepare: ' . $conn->error]);
    exit;
}

// Bind parameters - 25 parámetros
$stmt->bind_param(
    "sisssssssssssssssssiissii",
    $typeID,                // s - VARCHAR(10)
    $number_id,             // i - INT
    $first_name,            // s - VARCHAR(50)
    $second_name,           // s - VARCHAR(50)
    $first_last,            // s - VARCHAR(50)
    $second_last,           // s - VARCHAR(50)
    $email,                 // s - VARCHAR(100)
    $interest,              // s - VARCHAR(50)
    $start_training_date,   // s - DATE
    $grupos_poblacionales,  // s - VARCHAR(100)
    $nivel_educativo,       // s - VARCHAR(50)
    $gender,                // s - VARCHAR(20)
    $current_employment_status, // s - VARCHAR(20)
    $current_tech_job,      // s - VARCHAR(5)
    $employment_obtained_by,// s - VARCHAR(100)
    $contract_type,         // s - VARCHAR(50)
    $income_level,          // s - VARCHAR(30)
    $current_job_role,      // s - VARCHAR(100)
    $employment_route_spaces, // s - TEXT
    $content_usefulness,    // i - TINYINT
    $employment_support,    // i - TINYINT
    $general_satisfaction,  // s - VARCHAR(30)
    $improvement_action,    // s - VARCHAR(150)
    $accept_requirements,   // i - TINYINT(1)
    $accept_data_policies   // i - TINYINT(1)
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