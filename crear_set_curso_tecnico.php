<?php
/**
 * Crea un SET de cursos: 1 curso técnico + sus 2 obligatorios (Inglés y
 * Habilidades Blandas), duplicando plantillas y con numeración de serie
 * automática basada en los cursos que ya existan en la categoría técnica.
 *
 * Ejemplo: al crear un set para BLO (Blockchain), si ya existen BLO-1 y BLO-2,
 * este script crea:
 *   - Blockchain          -> shortname BLO-3      (categoría 21)
 *   - Inglés              -> shortname ING-BLO-3  (categoría 14)
 *   - Habilidades Blandas -> shortname BH-BLO-3   (categoría 15)
 */

$api_url = "https://campus.cenditech.com.co/webservice/rest/server.php";
$token   = "c4bc5a8ef9d02d713c1e5283da17c29f";
$format  = "json";

// ==========================================================
// CONFIGURACIÓN: base de datos
// ==========================================================
$db_host = "127.0.0.1"; // "db" si se ejecuta dentro de Docker
$db_user = "root";
$db_pass = "root";
$db_name = "cendi_tech";

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// ==========================================================
// CONFIGURACIÓN: categorías técnicas
// ==========================================================
// 'template_courseid' => ID del curso plantilla a duplicar para ese técnico.
// Déjalo en null hasta que tengas las plantillas creadas; el script
// avisará claramente si intentas usar uno que aún no está configurado.
$CATEGORIAS_TECNICAS = [
    'IA'    => ['categoryid' => 16, 'nombre' => 'Inteligencia Artificial',      'template_courseid' => 32],
    'PDS'   => ['categoryid' => 17, 'nombre' => 'Programación y Desarrollo',    'template_courseid' => 31],
    'DT'    => ['categoryid' => 18, 'nombre' => 'Análisis de Datos',            'template_courseid' => 37],
    'CIBER' => ['categoryid' => 19, 'nombre' => 'Ciberseguridad',               'template_courseid' => 36],
    'CN'    => ['categoryid' => 20, 'nombre' => 'Computación en la Nube',       'template_courseid' => 38],
    'BLO'   => ['categoryid' => 21, 'nombre' => 'Blockchain',                   'template_courseid' => 34],
    'RA'    => ['categoryid' => 22, 'nombre' => 'Robótica y Automatización',    'template_courseid' => 35],
    'IOT'   => ['categoryid' => 23, 'nombre' => 'Internet de las Cosas',        'template_courseid' => 33],
];

// ==========================================================
// CONFIGURACIÓN: cursos obligatorios
// ==========================================================
$CATEGORIAS_OBLIGATORIAS = [
    'ING' => ['categoryid' => 14, 'nombre' => 'Inglés',              'template_courseid' => 28],
    'BH'  => ['categoryid' => 15, 'nombre' => 'Habilidades Blandas', 'template_courseid' => 29],
];

// ==========================================================
// FUNCIONES BASE
// ==========================================================

function callMoodleAPIB($function, $params = []) {
    global $api_url, $token, $format;
    $params['wstoken'] = $token;
    $params['wsfunction'] = $function;
    $params['moodlewsrestformat'] = $format;
    $url = $api_url . '?' . http_build_query($params);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        curl_close($ch);
        return ['error' => 'Error al conectar con Moodle: ' . curl_error($ch)];
    }
    curl_close($ch);
    return json_decode($response, true);
}

function getCourseByIdB($courseId) {
    $result = callMoodleAPIB('core_course_get_courses', [
        'options' => ['ids' => [$courseId]],
    ]);
    return $result[0] ?? null;
}

/**
 * Obtiene todos los cursos de una categoría (para calcular la serie).
 */
function getCoursesByCategoryB($categoryId) {
    $result = callMoodleAPIB('core_course_get_courses_by_field', [
        'field' => 'category',
        'value' => $categoryId,
    ]);
    return $result['courses'] ?? [];
}

function duplicateCourseB($courseIdOrigen, $newFullname, $newShortname, $categoryIdDestino = null) {
    $original = getCourseByIdB($courseIdOrigen);
    if (!$original) {
        return ['error' => "No se encontró el curso plantilla id={$courseIdOrigen}"];
    }

    return callMoodleAPIB('core_course_duplicate_course', [
        'courseid'   => $courseIdOrigen,
        'fullname'   => $newFullname,
        'shortname'  => $newShortname,
        'categoryid' => $categoryIdDestino ?? $original['categoryid'],
        'visible'    => 1,
        'options'    => [
            ['name' => 'activities',       'value' => 1],
            ['name' => 'blocks',           'value' => 1],
            ['name' => 'filters',          'value' => 1],
            ['name' => 'users',            'value' => 0],
            ['name' => 'role_assignments', 'value' => 0],
            ['name' => 'comments',         'value' => 0],
            ['name' => 'userscompletion',  'value' => 0],
            ['name' => 'logs',             'value' => 0],
            ['name' => 'grade_histories',  'value' => 0],
        ],
    ]);
}

// ==========================================================
// REGISTRO EN BASE DE DATOS
// ==========================================================

/**
 * Inserta un curso individual en la tabla `cursos`.
 */
function registrarCursoBD($courseId, $courseCode, $courseName) {
    global $conn;
    if (!$conn) {
        return ['ok' => false, 'error' => 'Sin conexión a la base de datos'];
    }
    $stmt = mysqli_prepare($conn, "INSERT INTO cursos (course_id, course_code, course_name) VALUES (?, ?, ?)");
    if (!$stmt) {
        return ['ok' => false, 'error' => mysqli_error($conn)];
    }
    mysqli_stmt_bind_param($stmt, "iss", $courseId, $courseCode, $courseName);
    $ok = mysqli_stmt_execute($stmt);
    $error = $ok ? null : mysqli_stmt_error($stmt);
    mysqli_stmt_close($stmt);
    return $ok ? ['ok' => true] : ['ok' => false, 'error' => $error];
}

/**
 * Inserta la tripleta de cursos en la tabla `sets_cursos`.
 */
function registrarSetBD($codigoTecnico, $serie, $cursoTecnicoId, $cursoInglesId, $cursoHabilidadesId) {
    global $conn;
    if (!$conn) {
        return ['ok' => false, 'error' => 'Sin conexión a la base de datos'];
    }
    $stmt = mysqli_prepare($conn, "INSERT INTO sets_cursos (codigo_tecnico, serie, curso_tecnico_id, curso_ingles_id, curso_habilidades_id) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        return ['ok' => false, 'error' => mysqli_error($conn)];
    }
    mysqli_stmt_bind_param($stmt, "siiii", $codigoTecnico, $serie, $cursoTecnicoId, $cursoInglesId, $cursoHabilidadesId);
    $ok = mysqli_stmt_execute($stmt);
    $error = $ok ? null : mysqli_stmt_error($stmt);
    mysqli_stmt_close($stmt);
    return $ok ? ['ok' => true] : ['ok' => false, 'error' => $error];
}

// ==========================================================
// LÓGICA DE SERIE AUTOMÁTICA
// ==========================================================

/**
 * Busca en una categoría el número de serie más alto para un código dado
 * (shortnames tipo "CODIGO-N") y devuelve el siguiente número disponible.
 */
function siguienteSerieB($categoryId, $codigo) {
    $cursos = getCoursesByCategoryB($categoryId);
    $max = 0;

    foreach ($cursos as $curso) {
        if (preg_match('/^' . preg_quote($codigo, '/') . '-(\d+)$/', $curso['shortname'], $m)) {
            $num = (int) $m[1];
            if ($num > $max) {
                $max = $num;
            }
        }
    }

    return $max + 1;
}

// ==========================================================
// FUNCIÓN PRINCIPAL: crea el set completo (técnico + obligatorios)
// ==========================================================

/**
 * @param string $codigoTecnico Código del área técnica, ej: 'BLO', 'IA', 'DT'...
 * @return array Resultado con los 3 cursos creados o el detalle del error.
 */
function crearSetCursoTecnicoB($codigoTecnico) {
    global $CATEGORIAS_TECNICAS, $CATEGORIAS_OBLIGATORIAS;

    $codigoTecnico = strtoupper($codigoTecnico);

    if (!isset($CATEGORIAS_TECNICAS[$codigoTecnico])) {
        return ['error' => "Código técnico desconocido: {$codigoTecnico}"];
    }

    $tecnico = $CATEGORIAS_TECNICAS[$codigoTecnico];
    $ing     = $CATEGORIAS_OBLIGATORIAS['ING'];
    $bh      = $CATEGORIAS_OBLIGATORIAS['BH'];

    // Validar que las 3 plantillas estén configuradas antes de hacer nada
    $faltantes = [];
    if (empty($tecnico['template_courseid'])) $faltantes[] = "plantilla técnica ({$codigoTecnico})";
    if (empty($ing['template_courseid']))     $faltantes[] = "plantilla de Inglés (ING)";
    if (empty($bh['template_courseid']))      $faltantes[] = "plantilla de Habilidades Blandas (BH)";

    if (!empty($faltantes)) {
        return [
            'error' => 'Faltan plantillas por configurar: ' . implode(', ', $faltantes) .
                       '. Edita el array $CATEGORIAS_TECNICAS / $CATEGORIAS_OBLIGATORIAS al inicio del script.',
        ];
    }

    // 1. Calcular número de serie según la categoría técnica
    $serie = siguienteSerieB($tecnico['categoryid'], $codigoTecnico);

    // 2. Construir shortnames/fullnames de los 3 cursos
    $shortTecnico = "{$codigoTecnico}-{$serie}";
    $shortIng     = "ING-{$codigoTecnico}-{$serie}";
    $shortBh      = "BH-{$codigoTecnico}-{$serie}";

    $fullTecnico = "{$tecnico['nombre']} {$shortTecnico}";
    $fullIng     = "{$ing['nombre']} {$shortIng}";
    $fullBh      = "{$bh['nombre']} {$shortBh}";

    // 3. Duplicar los 3 cursos
    $resultado = [
        'codigo_tecnico' => $codigoTecnico,
        'serie'          => $serie,
        'cursos'         => [],
    ];

    $pasos = [
        'tecnico' => [$tecnico['template_courseid'], $fullTecnico, $shortTecnico, $tecnico['categoryid']],
        'ingles'  => [$ing['template_courseid'],     $fullIng,     $shortIng,     $ing['categoryid']],
        'blandas' => [$bh['template_courseid'],      $fullBh,      $shortBh,      $bh['categoryid']],
    ];

    $idsCreados = [];

    foreach ($pasos as $clave => [$templateId, $fullname, $shortname, $categoryId]) {
        $r = duplicateCourseB($templateId, $fullname, $shortname, $categoryId);

        if (isset($r['error']) || isset($r['exception']) || !isset($r['id'])) {
            $resultado['cursos'][$clave] = [
                'ok'    => false,
                'error' => $r['message'] ?? $r['error'] ?? 'Error desconocido',
                'detalle_crudo' => $r,
            ];
            // Si falla uno, se detiene el proceso (para no dejar el set incompleto sin avisar)
            $resultado['detenido_en'] = $clave;
            return $resultado;
        }

        $courseid = $r['id'];

        // Registrar el curso individual en la base de datos
        $db = registrarCursoBD($courseid, $shortname, $fullname);

        $resultado['cursos'][$clave] = [
            'ok'        => true,
            'courseid'  => $courseid,
            'fullname'  => $fullname,
            'shortname' => $shortname,
            'bd'        => $db,
        ];

        $idsCreados[$clave] = $courseid;
    }

    // Registrar la tripleta en la base de datos
    $resultado['set_bd'] = registrarSetBD(
        $codigoTecnico,
        $serie,
        $idsCreados['tecnico'],
        $idsCreados['ingles'],
        $idsCreados['blandas']
    );

    return $resultado;
}

// ==========================================================
// EJECUCIÓN: crea un set para TODOS los técnicos
// ==========================================================

$codigos = array_keys($CATEGORIAS_TECNICAS);

echo "<pre>";
foreach ($codigos as $codigo) {
    $respuesta = crearSetCursoTecnicoB($codigo);

    if (isset($respuesta['error'])) {
        echo "== {$codigo} ==\nERROR: {$respuesta['error']}\n\n";
    } elseif (isset($respuesta['detenido_en'])) {
        echo "== {$codigo} ==\nEl proceso se detuvo al intentar crear: {$respuesta['detenido_en']}\n";
        print_r($respuesta['cursos']);
        echo "\n";
    } else {
        echo "== {$codigo} ==\nSet creado (serie {$respuesta['serie']}):\n";
        foreach ($respuesta['cursos'] as $clave => $c) {
            echo "   - {$clave}: {$c['fullname']} ({$c['shortname']}) -> courseid={$c['courseid']} [bd: " . ($c['bd']['ok'] ? 'ok' : 'error') . "]\n";
        }
        echo "   set_bd: " . ($respuesta['set_bd']['ok'] ? 'ok' : 'error: ' . ($respuesta['set_bd']['error'] ?? '?')) . "\n\n";
    }
}
echo "</pre>";
