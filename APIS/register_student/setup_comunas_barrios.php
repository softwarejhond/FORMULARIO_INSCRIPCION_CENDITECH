<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/../../controller/conexion.php';

header('Content-Type: text/html; charset=utf-8');

$log = [];

function report($msg) {
    global $log;
    $log[] = $msg;
}

// 1. Agregar columnas a user_register (después de longitud)
$columnsResult = $conn->query("SHOW COLUMNS FROM user_register");
$existingColumns = [];
if ($columnsResult) {
    while ($col = $columnsResult->fetch_assoc()) {
        $existingColumns[] = $col['Field'];
    }
}

if (!in_array('comuna_corregimiento', $existingColumns)) {
    $sql = "ALTER TABLE user_register ADD comuna_corregimiento VARCHAR(150) NULL AFTER longitud";
    if ($conn->query($sql)) {
        report('Columna comuna_corregimiento agregada.');
    } else {
        report('Error agregando comuna_corregimiento: ' . $conn->error);
    }
} else {
    report('Columna comuna_corregimiento ya existe.');
}

if (!in_array('barrio', $existingColumns)) {
    $sql = "ALTER TABLE user_register ADD barrio VARCHAR(200) NULL AFTER comuna_corregimiento";
    if ($conn->query($sql)) {
        report('Columna barrio agregada.');
    } else {
        report('Error agregando barrio: ' . $conn->error);
    }
} else {
    report('Columna barrio ya existe.');
}

// 2. Crear tablas
$sqlComunas = "CREATE TABLE IF NOT EXISTS comunas_corregimientos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(20) NOT NULL,
    nombre VARCHAR(200) NULL,
    identificacion VARCHAR(100) NULL,
    limite_municipio_id VARCHAR(20) NULL,
    subtipo_comunacorregimiento INT NULL,
    UNIQUE KEY uq_comuna_codigo (codigo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

if ($conn->query($sqlComunas)) {
    report('Tabla comunas_corregimientos lista.');
} else {
    report('Error creando comunas_corregimientos: ' . $conn->error);
}

$sqlBarrios = "CREATE TABLE IF NOT EXISTS barrios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(20) NOT NULL,
    nombre VARCHAR(200) NULL,
    identificacion VARCHAR(100) NULL,
    limite_comuna_corregimiento_id VARCHAR(20) NULL,
    limite_municipio_id VARCHAR(20) NULL,
    subtipo_barriovereda INT NULL,
    INDEX idx_barrio_comuna (limite_comuna_corregimiento_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

if ($conn->query($sqlBarrios)) {
    report('Tabla barrios lista.');
} else {
    report('Error creando barrios: ' . $conn->error);
}

// 3. Siembra de comunas
$pathComunas = dirname(__DIR__, 2) . '/docs/comunas_corregimientos.json';
$pathBarrios = dirname(__DIR__, 2) . '/docs/barrios.json';

$countComunas = $conn->query("SELECT COUNT(*) AS c FROM comunas_corregimientos")->fetch_assoc()['c'];
if ($countComunas == 0 && file_exists($pathComunas)) {
    $json = json_decode(file_get_contents($pathComunas), true);
    $features = isset($json['features']) ? $json['features'] : [];
    $inserted = 0;
    $stmt = $conn->prepare("INSERT INTO comunas_corregimientos (codigo, nombre, identificacion, limite_municipio_id, subtipo_comunacorregimiento) VALUES (?, ?, ?, ?, ?)");
    foreach ($features as $f) {
        $a = $f['attributes'] ?? [];
        $nombre = $a['nombre'] ?? null;
        if ($nombre === null || $nombre === '') {
            continue;
        }
        $codigo = (string)($a['codigo'] ?? '');
        $identificacion = $a['identificacion'] !== null ? (string)$a['identificacion'] : null;
        $limiteMunicipio = $a['limitemunicipioid'] !== null ? (string)$a['limitemunicipioid'] : null;
        $subtipo = isset($a['subtipo_comunacorregimiento']) ? (int)$a['subtipo_comunacorregimiento'] : null;
        $stmt->bind_param('ssssi', $codigo, $nombre, $identificacion, $limiteMunicipio, $subtipo);
        $stmt->execute();
        $inserted++;
    }
    $stmt->close();
    report("Comunas insertadas: $inserted");
} else {
    report('Tabla comunas_corregimientos ya tiene datos (' . $countComunas . '), se omite siembra.');
}

// 4. Siembra de barrios
$countBarrios = $conn->query("SELECT COUNT(*) AS c FROM barrios")->fetch_assoc()['c'];
if ($countBarrios == 0 && file_exists($pathBarrios)) {
    $json = json_decode(file_get_contents($pathBarrios), true);
    $features = isset($json['features']) ? $json['features'] : [];
    $inserted = 0;
    $stmt = $conn->prepare("INSERT INTO barrios (codigo, nombre, identificacion, limite_comuna_corregimiento_id, limite_municipio_id, subtipo_barriovereda) VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($features as $f) {
        $a = $f['attributes'] ?? [];
        $nombre = $a['nombre'] ?? null;
        if ($nombre === null || $nombre === '') {
            continue;
        }
        $codigo = (string)($a['codigo'] ?? '');
        $identificacion = $a['identificacion'] !== null ? (string)$a['identificacion'] : null;
        $limiteComuna = $a['limitecomunacorregimientoid'] !== null ? (string)$a['limitecomunacorregimientoid'] : null;
        $limiteMunicipio = $a['limitemunicipioid'] !== null ? (string)$a['limitemunicipioid'] : null;
        $subtipo = isset($a['subtipo_barriovereda']) ? (int)$a['subtipo_barriovereda'] : null;
        $stmt->bind_param('sssssi', $codigo, $nombre, $identificacion, $limiteComuna, $limiteMunicipio, $subtipo);
        $stmt->execute();
        $inserted++;
    }
    $stmt->close();
    report("Barrios insertados: $inserted");
} else {
    report('Tabla barrios ya tiene datos (' . $countBarrios . '), se omite siembra.');
}

echo '<h2>Resultado de la migración</h2><ul>';
foreach ($log as $msg) {
    echo '<li>' . htmlspecialchars($msg) . '</li>';
}
echo '</ul>';
?>
