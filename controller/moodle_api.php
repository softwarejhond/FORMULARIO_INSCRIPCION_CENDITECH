<?php
$api_url = "https://campus.cenditech.com.co/webservice/rest/server.php";
$token   = "c4bc5a8ef9d02d713c1e5283da17c29f";
$format  = "json";

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
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        curl_close($ch);
        return ['error' => 'Error al conectar con Moodle: ' . curl_error($ch)];
    }
    curl_close($ch);
    return json_decode($response, true);
}

function getCoursesB() {
    return callMoodleAPIB('core_course_get_courses');
}

/**
 * Normaliza un texto para Moodle: mayúsculas y sin tildes.
 */
function normalizarMoodle($texto) {
    $texto = trim($texto);
    $texto = mb_strtoupper($texto, 'UTF-8');
    $caracteres = [
        'Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U',
        'Ä' => 'A', 'Ë' => 'E', 'Ï' => 'I', 'Ö' => 'O', 'Ü' => 'U',
        'À' => 'A', 'È' => 'E', 'Ì' => 'I', 'Ò' => 'O', 'Ù' => 'U',
        'Â' => 'A', 'Ê' => 'E', 'Î' => 'I', 'Ô' => 'O', 'Û' => 'U',
        'Ñ' => 'N', 'Ç' => 'C',
    ];
    return strtr($texto, $caracteres);
}

/**
 * Crea un usuario en Moodle con cambio de contraseña obligatorio.
 *
 * @param array $data username, idnumber, password, firstname, lastname, email
 * @return array Respuesta de core_user_create_users
 */
function crearUsuarioMoodle($data) {
    $params = [
        'users' => [
            [
                'username'    => $data['username'],
                'idnumber'    => $data['idnumber'],
                'password'    => $data['password'],
                'firstname'   => $data['firstname'],
                'lastname'    => $data['lastname'],
                'email'       => $data['email'],
                'auth'        => 'manual',
                'preferences' => [
                    ['type' => 'auth_forcepasswordchange', 'value' => 1],
                ],
            ],
        ],
    ];
    return callMoodleAPIB('core_user_create_users', $params);
}

/**
 * Busca un usuario de Moodle por su username.
 */
function getUsuarioMoodlePorUsername($username) {
    $result = callMoodleAPIB('core_user_get_users_by_field', [
        'field'  => 'username',
        'values' => [$username],
    ]);
    return !empty($result) ? $result[0] : null;
}

/**
 * Matricula un usuario en un curso con rol de estudiante (roleid = 5).
 */
function matricularUsuarioCurso($userId, $courseId) {
    return callMoodleAPIB('enrol_manual_enrol_users', [
        'enrolments' => [
            [
                'roleid'   => 5,
                'userid'   => $userId,
                'courseid' => $courseId,
            ],
        ],
    ]);
}
