<?php

// ============================================
// CONFIGURACIÓN
// ============================================
$moodleUrl = "https://campus.cenditech.com.co/webservice/rest/server.php";
$token = "c4bc5a8ef9d02d713c1e5283da17c29f";
$courseId = 31;

// Modo simulación: true = solo muestra qué borraría, no borra nada
$dryRun = false;

// ============================================
// 1. Lista de nombres a eliminar (exactos o por coincidencia)
// ============================================

// Labels de texto duplicado (coincidencia parcial - las primeras palabras)
$labelsAEliminar = [
    "La API File en JavaScript es una interfaz",
    "API Communication: Enviando Datos",
    "Los generadores y las excepciones son dos concepto",
    "Programación Orientada a Objetos (POO) La Programa",
    "Las cadenas son secuencias de caracteres",
    "Archivos Externos Los archivos externos son utiliz",
    "Interfaces Gráficas con Tkinter",
    "Funciones Especiales Las funciones especiales",
    "Decoradores, Documentación y Pruebas en Python",
    "Introducción a Django Django es un framework",
    "Referencia del video público y libre",
    "Condicionales, Filtros y Cargadores de Plantillas",
    "Configuración de PostgreSQL en Django",
    "Manejo de tablas: El Panel de Administración",
    "Formularios envían datos al servidor",
];

// Nombres exactos de módulos (resources) a eliminar
$recursosAEliminar = [
    "SIMULADOR API DRAG DROP",
    "SIMULADOR API GEOLOCATION, WEB STORAGE",
    "SIMULADOR API FILE",
    "SIMULADOR API COMUNICATION",
];

// Qbanks fuera de tema
$qbanksAEliminar = [
    "Ingles",
];

// ============================================
// 2. Función para llamar a la API de Moodle
// ============================================
function llamarMoodle($moodleUrl, $params) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $moodleUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        die("Error cURL: " . curl_error($ch) . "\n");
    }
    curl_close($ch);

    return json_decode($response, true);
}

// ============================================
// 3. Obtener contenido del curso 48
// ============================================
$params = [
    "wstoken" => $token,
    "wsfunction" => "core_course_get_contents",
    "courseid" => $courseId,
    "moodlewsrestformat" => "json"
];

$data = llamarMoodle($moodleUrl, $params);

if (isset($data['exception'])) {
    die("Error al obtener el curso: " . $data['message'] . "\n");
}

// ============================================
// 4. Recorrer secciones/módulos e identificar qué borrar
// ============================================
$idsABorrar = [];

function coincideParcial($nombre, $listaBusqueda) {
    foreach ($listaBusqueda as $busqueda) {
        if (stripos($nombre, $busqueda) === 0) {
            return true;
        }
    }
    return false;
}

foreach ($data as $seccion) {
    if (empty($seccion['modules'])) continue;

    foreach ($seccion['modules'] as $modulo) {
        $nombre = $modulo['name'] ?? '';
        $modname = $modulo['modname'] ?? '';
        $cmid = $modulo['id'] ?? null;

        $debeBorrar = false;
        $motivo = '';

        if ($modname === 'label' && coincideParcial($nombre, $labelsAEliminar)) {
            $debeBorrar = true;
            $motivo = 'label duplicado';
        } elseif (in_array($nombre, $recursosAEliminar)) {
            $debeBorrar = true;
            $motivo = 'simulador redundante';
        } elseif ($modname === 'qbank' && in_array($nombre, $qbanksAEliminar)) {
            $debeBorrar = true;
            $motivo = 'fuera de tema';
        }

        if ($debeBorrar && $cmid) {
            $idsABorrar[] = $cmid;
            echo "🗑️  [$motivo] cmid=$cmid | [$modname] $nombre\n";
        }
    }
}

echo "\n----------------------------------------\n";
echo "Total de módulos a eliminar: " . count($idsABorrar) . "\n";
echo "----------------------------------------\n\n";

if (empty($idsABorrar)) {
    echo "No se encontraron módulos que coincidan con los criterios.\n";
    exit;
}

// ============================================
// 5. Borrar (o simular) los módulos encontrados
// ============================================
if ($dryRun) {
    echo "⚠️  MODO SIMULACIÓN (dryRun=true). No se borró nada.\n";
    echo "Cambia \$dryRun a false para ejecutar el borrado real.\n";
} else {
    // core_course_delete_modules espera un array de cmids
    $deleteParams = [
        "wstoken" => $token,
        "wsfunction" => "core_course_delete_modules",
        "moodlewsrestformat" => "json",
    ];

    foreach ($idsABorrar as $i => $cmid) {
        $deleteParams["cmids[$i]"] = $cmid;
    }

    $resultado = llamarMoodle($moodleUrl, $deleteParams);

    if (isset($resultado['exception'])) {
        echo "❌ Error al borrar: " . $resultado['message'] . "\n";
    } else {
        echo "✅ Módulos eliminados correctamente.\n";
    }
}