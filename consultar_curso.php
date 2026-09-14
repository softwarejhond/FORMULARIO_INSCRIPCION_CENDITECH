<?php

// --- Configuración ---
$moodleUrl = "https://campus.cenditech.com.co/webservice/rest/server.php";
$token = "c4bc5a8ef9d02d713c1e5283da17c29f";
$courseId = 31;
$archivoSalida = __DIR__ . "/resultado_curso.txt";

// --- Parámetros de la llamada ---
$params = [
    "wstoken" => $token,
    "wsfunction" => "core_course_get_contents",
    "courseid" => $courseId,
    "moodlewsrestformat" => "json"
];

// --- Llamada a la API con cURL ---
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $moodleUrl . "?" . http_build_query($params));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // pon false solo si tienes problemas de certificado en dev

$response = curl_exec($ch);

if (curl_errno($ch)) {
    die("Error cURL: " . curl_error($ch));
}
curl_close($ch);

$data = json_decode($response, true);

// --- Verificar si hubo error ---
if (isset($data['exception'])) {
    $salida = "Error: " . $data['message'] . "\n";
    file_put_contents($archivoSalida, $salida);
    echo $salida;
    exit;
}

// --- Recorrer secciones y módulos ---
$salida = "";
foreach ($data as $seccion) {
    $salida .= "\n📂 Sección: " . $seccion['name'] . "\n";

    if (!empty($seccion['modules'])) {
        foreach ($seccion['modules'] as $modulo) {
            $salida .= "   - [" . $modulo['modname'] . "] " . $modulo['name'] . "\n";

            if (!empty($modulo['contents'])) {
                foreach ($modulo['contents'] as $contenido) {
                    $filename = $contenido['filename'] ?? '(sin nombre)';
                    $fileurl = $contenido['fileurl'] ?? '';
                    $salida .= "       📄 $filename -> $fileurl\n";
                }
            }
        }
    }
}

file_put_contents($archivoSalida, $salida);
echo $salida;