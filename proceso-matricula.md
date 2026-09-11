### ESTO SOLO SON EJEMPLOS DE COMO SE APLICO EN OTRO PROYECTO


### MOODLE FUNCTIONS

```php 

<?php
class MoodleAPI
{
    private $api_url;
    private $token;
    private $format;

    public function __construct()
    {
        $this->api_url = "https://campus.cenditech.com.co/webservice/rest/server.php";
        $this->token = "--";
        $this->format = "json";
    }

    // Hacer público el método callAPI
    public function callAPI($function, $params = [])
    {
        $params['wstoken'] = $this->token;
        $params['wsfunction'] = $function;
        $params['moodlewsrestformat'] = $this->format;

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->api_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($params),
            CURLOPT_SSL_VERIFYPEER => false
        ]);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new Exception("Error en la llamada API: " . $error);
        }

        return json_decode($response, true);
    }

    /**
     * Función para normalizar texto: convertir a mayúsculas y eliminar tildes
     */
    private function normalizar($texto)
    {
        // Convertir a mayúsculas
        $texto = mb_strtoupper($texto, 'UTF-8');

        // Array de caracteres con tilde y su equivalente sin tilde
        $caracteres = [
            'Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U',
            'Ä' => 'A', 'Ë' => 'E', 'Ï' => 'I', 'Ö' => 'O', 'Ü' => 'U',
            'À' => 'A', 'È' => 'E', 'Ì' => 'I', 'Ò' => 'O', 'Ù' => 'U',
            'Â' => 'A', 'Ê' => 'E', 'Î' => 'I', 'Ô' => 'O', 'Û' => 'U',
            'Ñ' => 'N', 'Ç' => 'C'
        ];

        return strtr($texto, $caracteres);
    }

    public function createUser($userData)
    {
        // Normalizar nombre y apellido usando el método de la clase
        $firstname = $this->normalizar($userData['firstname']);
        $lastname = $this->normalizar($userData['lastname']);

        $params = [
            'users[0][idnumber]' => $userData['username'],
            'users[0][username]' => $userData['username'],
            'users[0][password]' => $userData['password'],
            'users[0][firstname]' => $firstname,
            'users[0][lastname]' => $lastname,
            'users[0][email]' => $userData['email'],
            'users[0][auth]' => 'manual',
            'users[0][preferences][0][type]' => 'auth_forcepasswordchange',
            'users[0][preferences][0][value]' => 1
        ];

        return $this->callAPI('core_user_create_users', $params);
    }

    public function getUserByUsername($username)
    {
        $params = [
            'field' => 'username',
            'values[0]' => $username
        ];
        $response = $this->callAPI('core_user_get_users_by_field', $params);
        return !empty($response) ? $response[0] : null;
    }

    public function enrollUserInCourses($userId, $courses)
    {
        $results = [];
        $errors = [];

        if (empty($userId) || !is_array($courses) || empty($courses)) {
            throw new Exception("ID de usuario o cursos inválidos");
        }

        foreach ($courses as $courseId) {
            if (empty($courseId)) {
                continue; // Saltamos cursos con ID vacío
            }

            try {
                $params = [
                    'enrolments[0][roleid]' => 5, // 5 = estudiante
                    'enrolments[0][userid]' => $userId,
                    'enrolments[0][courseid]' => $courseId
                ];

                $result = $this->callAPI('enrol_manual_enrol_users', $params);

                // Verificar si hay un error en la respuesta
                if (isset($result['exception'])) {
                    // Si el error es que el usuario ya está inscrito, lo consideramos exitoso
                    if (strpos($result['message'], 'already enrolled') !== false) {
                        $results[] = ['status' => 'already_enrolled', 'courseId' => $courseId];
                        continue;
                    }

                    $errors[] = "Error en el curso {$courseId}: {$result['message']}";
                    continue;
                }

                $results[] = $result;
            } catch (Exception $e) {
                $errors[] = "Error en el curso {$courseId}: {$e->getMessage()}";
            }
        }

        // Si hubo errores pero también éxitos, consideramos la operación exitosa parcialmente
        if (!empty($results) && !empty($errors)) {
            error_log("Matriculación parcial: " . implode(", ", $errors));
            return $results;
        }

        // Si solo hubo errores, lanzamos excepción
        if (empty($results) && !empty($errors)) {
            throw new Exception("Error al matricular en cursos: " . implode(", ", $errors));
        }

        return $results;
    }

    /**
     * Actualiza el statusAdmin del usuario en la base de datos
     * 
     * @param string $numberId Número de identificación del usuario
     * @param mysqli $conn Conexión a la base de datos
     * @param string $status Estado a establecer (por defecto '3')
     * @return bool True si la actualización fue exitosa
     * @throws Exception Si hay algún error durante la actualización
     */
    public function updateUserStatus($numberId, $conn, $status = '3')
    {
        // Verificar que tengamos una conexión válida
        if (!$conn || $conn->connect_error) {
            throw new Exception("Error de conexión a la base de datos: " .
                ($conn ? $conn->connect_error : "Conexión no disponible"));
        }

        // Verificar que el numberId sea válido
        if (empty($numberId)) {
            throw new Exception("El número de identificación no puede estar vacío");
        }

        try {
            $updateSql = "UPDATE user_register SET statusAdmin = ? WHERE number_id = ?";
            $updateStmt = $conn->prepare($updateSql);

            if (!$updateStmt) {
                throw new Exception("Error al preparar la actualización de estado: " . $conn->error);
            }

            $updateStmt->bind_param('ss', $status, $numberId);

            if (!$updateStmt->execute()) {
                throw new Exception("Error al actualizar statusAdmin: " . $updateStmt->error);
            }

            // Verificar que se haya actualizado al menos una fila
            if ($updateStmt->affected_rows === 0) {
                throw new Exception("No se encontró ningún usuario con el número de identificación proporcionado: {$numberId}");
            }

            $updateStmt->close();
            return true;
        } catch (Exception $e) {
            throw new Exception("Error al actualizar el estado del usuario {$numberId}: " . $e->getMessage());
        }
    }

    public function assignTeacherToCourse($userId, $courseId)
    {
        if (empty($userId) || empty($courseId)) {
            throw new Exception("ID de usuario o curso inválido");
        }

        try {
            $params = [
                'enrolments[0][roleid]' => 3, // 3 = profesor
                'enrolments[0][userid]' => $userId,
                'enrolments[0][courseid]' => $courseId
            ];

            $result = $this->callAPI('enrol_manual_enrol_users', $params);

            // Verificar si hay un error en la respuesta
            if (isset($result['exception'])) {
                // Si el error es que el usuario ya está inscrito, lo consideramos exitoso
                if (strpos($result['message'], 'already enrolled') !== false) {
                    return ['status' => 'already_enrolled', 'courseId' => $courseId];
                }

                throw new Exception("Error al asignar profesor: {$result['message']}");
            }

            return $result;
        } catch (Exception $e) {
            throw new Exception("Error al asignar profesor al curso {$courseId}: {$e->getMessage()}");
        }
    }
}
?>

``` 


### Registros multiples

```html

<?php
// Verificar conexión a la base de datos
require __DIR__ . '../../../controller/conexion.php';
if (!isset($conn) || $conn->connect_error) {
    die("Error de conexión a la base de datos: " .
        (isset($conn) ? $conn->connect_error : "No se pudo establecer la conexión"));
}

include __DIR__ . '/../moodle_api.php';

$courses_data = getCoursesB();

// Consulta para obtener usuarios
$sql = "SELECT user_register.*, departamentos.departamento,
    EXISTS(
        SELECT 1 
        FROM participantes p 
        WHERE p.numero_documento = user_register.number_id
    ) AS tiene_certificado
    FROM user_register
    INNER JOIN departamentos ON user_register.department = departamentos.id_departamento
    WHERE departamentos.id_departamento = 5
      AND user_register.status = '1' 
      AND user_register.statusAdmin IN ('1', '8')
    ORDER BY user_register.first_name ASC";

$result = $conn->query($sql);
$data = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
} else {
    echo '<div class="alert alert-info">No hay datos disponibles.</div>';
}

// Obtener datos únicos para los filtros
$departamentos = ['BOGOTÁ, D.C.'];
$programas = [];
$modalidades = [];
$sedes = []; // Agregar array para sedes
$niveles = ['Explorador', 'Integrador', 'Innovador'];
$horarios = [];

foreach ($data as $row) {
    $depto = $row['departamento'];
    $sede = $row['headquarters'];


    // Obtener sedes únicas
    if (!in_array($sede, $sedes) && !empty($sede)) {
        $sedes[] = $sede;
    }

    // Obtener programas únicos
    if (!in_array($row['program'], $programas)) {
        $programas[] = $row['program'];
    }

    // Obtener modalidades únicas
    if (!in_array($row['mode'], $modalidades)) {
        $modalidades[] = $row['mode'];
    }

    // Obtener horarios únicos
    if (!in_array($row['schedules'], $horarios)) {
        $horarios[] = $row['schedules'];
    }
}

?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
    /* Estilos para la tabla con scroll */
    /* Estilos para la tabla con encabezados fijos */
    .table-container {
        max-height: 60vh;
        overflow: hidden;
        border: 1px solidrgb(49, 28, 75);
        border-radius: 4px;
        padding: 5px;
    }

    .table-wrapper {
        overflow-y: auto;
        max-height: inherit;
    }

    .table {
        margin-bottom: 0;
    }

    .table thead th {
        position: sticky;
        top: 0;
        z-index: 1;
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
        box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.1);
    }

    /* Ajuste para el ancho de las columnas */
    .table th,
    .table td {
        white-space: nowrap;
        padding: 8px;
    }

    /* Ajuste para el scroll horizontal */
    .table-responsive {
        overflow-x: auto;
        margin-bottom: 1rem;
    }

    /* Asegurar que el encabezado se mantenga visible al hacer scroll horizontal */
    .table thead {
        position: sticky;
        top: 0;
        z-index: 2;
    }

    /* Estilo para el fondo del encabezado */
    .thead-dark {
        background-color: #343a40;
        color: white;
    }
</style>
<div class="container-fluid px-2">
    <div class="table-responsive">

        <div class="row">
            <div class="col-md-3">
                <b class="text-left mb-1"><i class="bi bi-card-checklist"></i> Seleccionar cursos</b>

                <div class="col-md-12 col-sm-12 col-lg-12">
                    <div class="course-title text-indigo-dark "><i class="bi bi-laptop"></i> Bootcamp</div>
                    <div class="card course-card card-bootcamp" data-icon="💻">
                        <div class="card-body">
                            <select id="bootcamp" class="form-select course-select">
                                <?php if (!empty($courses_data)): ?>
                                    <?php foreach ($courses_data as $course): ?>
                                        <?php
                                        $categoryAllowed = in_array($course['categoryid'], [3, 7, 8, 9, 10, 11, 12]);
                                        if ($categoryAllowed):
                                        ?>
                                            <option value="<?php echo htmlspecialchars($course['id']); ?>">
                                                <?php echo htmlspecialchars($course['id'] . ' - ' . $course['fullname']); ?>
                                            </option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?></option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 col-sm-12 col-lg-12">
                    <div class="course-title text-indigo-dark"><i class="bi bi-translate"></i> Inglés nivelatorio</div>
                    <div class="card course-card card-ingles" data-icon="🌍">
                        <div class="card-body">
                            <select id="ingles" class="form-select course-select">
                                <?php if (!empty($courses_data)): ?>
                                    <?php foreach ($courses_data as $course): ?>
                                        <?php if ($course['categoryid'] == 4): ?>
                                            <option value="<?php echo htmlspecialchars($course['id']); ?>">
                                                <?= htmlspecialchars($course['id'] . ' - ' . $course['fullname']) ?>
                                            </option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 col-sm-12 col-lg-12">
                    <div class="course-title text-indigo-dark"><i class="bi bi-code-slash"></i> English Code</div>
                    <div class="card course-card card-english-code" data-icon="👨‍💻">
                        <div class="card-body">
                            <select id="english_code" class="form-select course-select">
                                <?php if (!empty($courses_data)): ?>
                                    <?php foreach ($courses_data as $course): ?>
                                        <?php if ($course['categoryid'] == 5): ?>
                                            <option value="<?php echo htmlspecialchars($course['id']); ?>">
                                                <?= htmlspecialchars($course['id'] . ' - ' . $course['fullname']) ?>
                                            </option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 col-sm-12 col-lg-12">
                    <div class="course-title text-indigo-dark"><i class="bi bi-lightbulb"></i> Habilidades</div>
                    <div class="card course-card card-skills" data-icon="💡">
                        <div class="card-body">
                            <select id="skills" class="form-select course-select">
                                <?php if (!empty($courses_data)): ?>
                                    <?php foreach ($courses_data as $course): ?>
                                        <?php if ($course['categoryid'] == 6): ?>
                                            <option value="<?php echo htmlspecialchars($course['id']); ?>">
                                                <?= htmlspecialchars($course['id'] . ' - ' . $course['fullname']) ?>
                                            </option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                <div class="d-flex gap-2 mb-3">
                    <!-- Button to open filter modal -->
                    <button type="button" class="btn bg-indigo-dark text-white" data-bs-toggle="modal" data-bs-target="#filterModal">
                        <i class="bi bi-filter-circle"></i> Filtros personalizados
                    </button>

                    <!-- Botón para exportar a Excel -->
                    <button id="exportarExcel" class="btn btn-success"
                        onclick="window.location.href='components/registerMoodle/export_excel_enrolled.php?action=export'">
                        <i class="bi bi-file-earmark-excel"></i> Exportar a Excel
                    </button>

                    <!-- Botón para mostrar usuarios seleccionados -->
                    <button class="btn bg-magenta-dark text-white d-flex align-items-center gap-2"
                        type="button"
                        data-bs-toggle="offcanvas"
                        data-bs-target="#selectedUsersList">
                        <i class="bi bi-list-check"></i>
                        <span>Gestionar seleccionados (<span id="floatingSelectedCount">0</span>)</span>
                    </button>

                </div>
                <div class="table-container">
                    <div class="table-wrapper">
                        <table id="listaInscritos" class="table table-hover table-bordered">
                            <thead class="thead-dark text-center">
                                <tr>
                                    <th>Tipo ID</th>
                                    <th>Número</th>
                                    <th>Nombre</th>
                                    <th>Lote</th>
                                    <th>Programa</th>
                                    <th>Preferencia</th>
                                    <th>Modalidad</th>
                                    <th class="text-center">
                                        <i class="bi bi-patch-check-fill"></i>
                                    </th>
                                    <th>Email</th>
                                    <th>Enviar Email</th>
                                    <th>Nuevo Email</th>
                                    <th>Departamento</th>
                                    <th>Sede</th>
                                    <th>Horario</th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                <?php foreach ($data as $row):
                                    // Procesar datos del usuario
                                    $firstName   = ucwords(strtolower(trim($row['first_name'])));
                                    $secondName  = ucwords(strtolower(trim($row['second_name'])));
                                    $firstLast   = ucwords(strtolower(trim($row['first_last'])));
                                    $secondLast  = ucwords(strtolower(trim($row['second_last'])));
                                    $fullName = $firstName . " " . $secondName . " " . $firstLast . " " . $secondLast;
                                    $nuevoCorreo = strtolower(substr(trim($row['first_name']), 0, 1))
                                        . strtolower(substr(trim($row['second_name']), 0, 1))
                                        . substr(trim($row['number_id']), -4)
                                        . strtolower(substr(trim($row['first_last']), 0, 1))
                                        . strtolower(substr(trim($row['second_last']), 0, 1))
                                        . '.ut@cendi.edu.co';
                                ?>
                                    <tr data-type-id="<?php echo htmlspecialchars($row['typeID']); ?>"
                                        data-number-id="<?php echo htmlspecialchars($row['number_id']); ?>"
                                        data-full-name="<?php echo htmlspecialchars($fullName); ?>"
                                        data-email="<?php echo htmlspecialchars($row['email']); ?>"
                                        data-institutional-email="<?php echo htmlspecialchars($nuevoCorreo); ?>"
                                        data-department="<?= htmlspecialchars($row['departamento']) ?>"
                                        data-headquarters="<?= htmlspecialchars($row['headquarters']) ?>"
                                        data-program="<?= htmlspecialchars($row['program']) ?>"
                                        data-level="<?= htmlspecialchars($row['level']) ?>"
                                        data-schedule="<?= htmlspecialchars($row['schedules']) ?>"
                                        data-mode="<?= htmlspecialchars($row['mode']) ?>"
                                        data-send-email="1">
                                        <td><?php echo htmlspecialchars($row['typeID']); ?></td>
                                        <td><?php echo htmlspecialchars($row['number_id']); ?></td>
                                        <td style="width: 350px; min-width: 350px; max-width: 380px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            <?php echo htmlspecialchars($fullName); ?>
                                            <?php if ($row['tiene_certificado']): ?>
                                                <button class="btn text-white ms-2" style="background-color: #ffbf00;"
                                                    onclick="mostrarCertificacionAlert('<?php echo htmlspecialchars($fullName); ?>')"
                                                    data-bs-toggle="popover"
                                                    data-bs-trigger="hover"
                                                    data-bs-placement="top"
                                                    data-bs-content="El estudiante cuenta con una certificación">
                                                    <i class="fa-solid fa-graduation-cap fa-beat text-black"></i>
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['lote']); ?></td>
                                        <td><?php echo htmlspecialchars($row['program']); ?></td>
                                        <td><?php echo htmlspecialchars($row['level']); ?></td>
                                        <td><?php echo htmlspecialchars($row['mode']); ?></td>
                                        <td>
                                            <input type="checkbox" class="form-check-input usuario-checkbox"
                                                style="width: 25px; height: 25px; appearance: none; background-color: white; border: 2px solid #ec008c; cursor: pointer; position: relative;"
                                                onclick="this.style.backgroundColor = this.checked ? 'magenta' : 'white'">
                                        </td>
                                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                                        <td>
                                            <div class="form-check form-switch d-flex justify-content-center">
                                                <input class="form-check-input send-email-checkbox"
                                                    type="checkbox"
                                                    checked
                                                    style="width: 2.5em; height: 1.5em; background-color: #fff; border: 2px solid #30336b; transition: background-color 0.2s;"
                                                    onchange="this.style.backgroundColor = this.checked ? '#30336b' : '#fff';"
                                                    onload="this.style.backgroundColor = this.checked ? '#30336b' : '#fff';">
                                            </div>
                                            <script>
                                                // Asegura el color inicial al cargar
                                                document.addEventListener('DOMContentLoaded', function() {
                                                    document.querySelectorAll('.send-email-checkbox').forEach(function(cb) {
                                                        cb.style.backgroundColor = cb.checked ? '#30336b' : '#fff';
                                                    });
                                                });
                                            </script>
                                        </td>
                                        <td><?php echo htmlspecialchars($nuevoCorreo); ?></td>

                                        <td>
                                            <?php
                                            $departamento = htmlspecialchars($row['departamento']);
                                            if ($departamento === 'CUNDINAMARCA') {
                                                echo "<button class='btn bg-lime-light w-100'><b>{$departamento}</b></button>"; // Botón verde para CUNDINAMARCA
                                            } elseif ($departamento === 'BOYACÁ') {
                                                echo "<button class='btn bg-indigo-light w-100'><b>{$departamento}</b></button>"; // Botón azul para BOYACÁ
                                            } else {
                                                echo "<span>{$departamento}</span>"; // Texto normal para otros valores
                                            }
                                            ?>
                                        </td>
                                        <td><b class="text-center"><?php echo htmlspecialchars($row['headquarters']); ?></b></td>
                                        <td class="text-center">
                                            <a class="btn bg-indigo-light"
                                                tabindex="0" role="button" data-toggle="popover" data-trigger="focus" data-placement="top"
                                                title="<?php echo empty($row['schedules']) ? 'Sin horario asignado' : htmlspecialchars($row['schedules']); ?>">
                                                <i class="bi bi-clock-history"></i>
                                            </a>
                                        </td>

                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>



                <!-- Filter Modal -->
                <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="filterModalLabel">
                                    <i class="bi bi-filter-circle"></i> Filtros de Búsqueda
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6 col-sm-12 mb-3">
                                        <div class="filter-title"><i class="bi bi-map"></i> Departamento</div>
                                        <div class="card filter-card card-department" data-icon="📍">
                                            <div class="card-body">
                                                <select id="filterDepartment" class="form-select">
                                                    <option value="">Todos los departamentos</option>
                                                    <option value="BOGOTÁ, D.C.">BOGOTÁ, D.C.</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-sm-12 mb-3">
                                        <div class="filter-title"><i class="bi bi-building"></i> Sede</div>
                                        <div class="card filter-card card-headquarters" data-icon="🏫">
                                            <div class="card-body">
                                                <select id="filterHeadquarters" class="form-select">
                                                    <option value="">Todas las sedes</option>
                                                    <?php foreach ($sedes as $sede): ?>
                                                        <option value="<?= htmlspecialchars($sede) ?>"><?= htmlspecialchars($sede) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-sm-12 mb-3">
                                        <div class="filter-title"><i class="bi bi-mortarboard"></i> Programa</div>
                                        <div class="card filter-card card-program" data-icon="🎓">
                                            <div class="card-body">
                                                <select id="filterProgram" class="form-select">
                                                    <option value="">Todos los programas</option>
                                                    <?php foreach ($programas as $programa): ?>
                                                        <option value="<?= htmlspecialchars($programa) ?>"><?= htmlspecialchars($programa) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-sm-12 mb-3">
                                        <div class="filter-title"><i class="bi bi-laptop"></i> Modalidad</div>
                                        <div class="card filter-card card-mode" data-icon="💻">
                                            <div class="card-body">
                                                <select id="filterMode" class="form-select">
                                                    <option value="">Todas las modalidades</option>
                                                    <option value="Virtual">Virtual</option>
                                                    <option value="Presencial">Presencial</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-sm-12 mb-3">
                                        <div class="filter-title"><i class="bi bi-layers"></i> Preferencia</div>
                                        <div class="card filter-card card-level" data-icon="⭐">
                                            <div class="card-body">
                                                <select id="filterLevel" class="form-select">
                                                    <option value="">Todos los niveles</option>
                                                    <option value="Explorador">Explorador</option>
                                                    <option value="Integrador">Integrador</option>
                                                    <option value="Innovador">Innovador</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-sm-12 mb-3">
                                        <div class="filter-title"><i class="bi bi-clock"></i> Horario</div>
                                        <div class="card filter-card card-schedule" data-icon="⏰">
                                            <div class="card-body">
                                                <select id="filterSchedule" class="form-select">
                                                    <option value="">Todos los horarios</option>
                                                    <?php foreach ($horarios as $horario): ?>
                                                        <option value="<?= htmlspecialchars($horario) ?>"><?= htmlspecialchars($horario) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn bg-magenta-dark text-white" data-bs-dismiss="modal">
                                    <i class="bi bi-check2-circle"></i> Ver resultados
                                </button>
                            </div>
                        </div>
                    </div>
                </div>



                <!-- Agregar después de la tabla -->
                <div class="offcanvas offcanvas-end" tabindex="-1" id="selectedUsersList" aria-labelledby="selectedUsersListLabel">
                    <div class="offcanvas-header">
                        <h5 class="offcanvas-title" id="selectedUsersListLabel">
                            <i class="bi bi-person-check"></i> Beneficiarios seleccionados (<span id="selectedCount">0</span>)
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>

                    </div>


                    <div class="offcanvas-body">
                        <div class="m-3">
                            <button id="enrollSelectedUsers" class="btn bg-magenta-dark text-white w-100">
                                <i class="bi bi-patch-check-fill"></i> Matricular Seleccionados
                            </button>
                        </div>
                        <div id="selectedUsersContainer"></div>

                    </div>
                </div>


            </div>
        </div>
    </div>
    <script>
        const selectedUsers = new Map();

        document.addEventListener("DOMContentLoaded", function() {
            // Resto del código existente para los checkboxes
            const checkboxes = document.querySelectorAll(".usuario-checkbox");

            function actualizarContador() {
                // Cuenta los checkboxes que están marcados
                const seleccionados = document.querySelectorAll(".usuario-checkbox:checked").length;
                // Actualizar solo los contadores que existen
                const selectedCount = document.getElementById('selectedCount');
                const floatingSelectedCount = document.getElementById('floatingSelectedCount');

                if (selectedCount) selectedCount.textContent = seleccionados;
                if (floatingSelectedCount) floatingSelectedCount.textContent = seleccionados;
            }

            // Agrega un evento a cada checkbox para actualizar el contador
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener("change", function() {
                    const row = this.closest('tr');
                    toggleUserSelection(this, row);
                    actualizarContador();
                });
            });
        });

        // No necesitamos este evento ya que está duplicado más abajo con el ID correcto 'enrollSelectedUsers'

        function confirmBulkEnrollment(usersToEnroll) {
            if (usersToEnroll.length === 0) {
                Swal.fire('Error', 'Por favor seleccione al menos un estudiante', 'error');
                return;
            }

            Swal.fire({
                title: 'Confirmar matrícula',
                text: `¿Está seguro que desea matricular a ${usersToEnroll.length} estudiantes seleccionados?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, matricular',
                cancelButtonText: 'Cancelar'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        const result = await processEnrollments(usersToEnroll);

                        Swal.fire({
                            title: 'Resultado del proceso',
                            html: result.message,
                            icon: result.icon,
                            confirmButtonText: 'Entendido'
                        }).then(() => {
                            updateSelectedUsersList();
                            window.location.reload();
                        });
                    } catch (error) {
                        console.error("Error en el proceso de matrícula:", error);
                        Swal.fire({
                            title: 'Error inesperado',
                            html: `Ha ocurrido un error durante el proceso:<br>${error.message}`,
                            icon: 'error',
                            confirmButtonText: 'Entendido'
                        });
                    }
                }
            });
        }

        async function processEnrollments(usersToEnroll) {
            const errors = [];
            let successes = 0;
            let processed = 0;
            let emailSuccesses = 0;

            // Iniciar SweetAlert con el contador de progreso
            Swal.fire({
                title: 'Procesando matrículas',
                html: `<div id="enrollmentProgress">Procesando: <b>0</b> de ${usersToEnroll.length}<br>Por favor espere...</div>`,
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            for (const formData of usersToEnroll) {
                try {
                    // ***** DEBUGGING - MOSTRAR QUÉ DATOS LLEGAN *****
                    console.log(`Procesando usuario ${formData.number_id}:`);
                    console.log(`- send_email: ${formData.send_email}`);
                    console.log(`- Datos completos:`, formData);

                    // Matrícula
                    const enrollResponse = await fetch('components/registerMoodle/enroll_user_multiple.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(formData)
                    });

                    let enrollData;
                    try {
                        enrollData = await enrollResponse.json();
                    } catch (jsonError) {
                        throw new Error(`Error al procesar respuesta JSON: ${jsonError.message}\nCódigo HTTP: ${enrollResponse.status}\nEstado: ${enrollResponse.statusText}`);
                    }

                    // Verificar si la respuesta HTTP no es exitosa
                    if (!enrollResponse.ok) {
                        throw new Error(
                            `Error HTTP ${enrollResponse.status}: ${enrollResponse.statusText}\n` +
                            `Detalles: ${enrollData?.message || 'No hay detalles disponibles'}\n` +
                            `Usuario: ${formData.full_name} (${formData.number_id})`
                        );
                    }

                    processed++;

                    // Actualizar la interfaz con el progreso
                    const swalContent = document.getElementById('enrollmentProgress');
                    if (swalContent) {
                        swalContent.innerHTML = `
                            Procesando: <b>${processed}</b> de ${usersToEnroll.length}<br>
                            Éxitos: <b>${successes}</b><br>
                            Errores: <b>${errors.length}</b><br>
                            Por favor espere...
                        `;
                    }

                    // Si la matrícula fue exitosa
                    if (enrollData.success) {
                        successes++;

                        // Obtener el carnet
                        let carnetFilePath = null;
                        try {
                            // Intentar generar/obtener el carnet
                            const carnetResponse = await fetch(`components/listCredentials/generate_carnet.php?generate_carnet=1&number_id=${formData.number_id}&username=${encodeURIComponent(formData.full_name)}`);
                            if (carnetResponse.ok) {
                                const carnetData = await carnetResponse.json();
                                if (carnetData.success && carnetData.file_path) {
                                    carnetFilePath = carnetData.file_path;
                                } else {
                                    console.warn(`Advertencia al generar/obtener carnet para ${formData.number_id}: ${carnetData.message || 'Respuesta no exitosa o sin ruta de archivo.'}`);
                                    errors.push({
                                        student: formData.number_id,
                                        message: `Matrícula exitosa, pero advertencia al generar/obtener carnet: ${carnetData.message || 'Error desconocido en carnet'}`,
                                        type: 'carnet_warning'
                                    });
                                }
                            } else {
                                const errorText = await carnetResponse.text();
                                console.error(`Error HTTP al generar/obtener carnet para ${formData.number_id}: ${carnetResponse.status} - ${errorText}`);
                                errors.push({
                                    student: formData.number_id,
                                    message: `Matrícula exitosa, pero error HTTP (${carnetResponse.status}) al generar/obtener carnet.`,
                                    type: 'carnet_http_error'
                                });
                            }
                        } catch (carnetError) {
                            console.error(`Error en la llamada para generar/obtener carnet para ${formData.number_id}:`, carnetError);
                            errors.push({
                                student: formData.number_id,
                                message: `Matrícula exitosa, pero error al procesar generación/obtención de carnet: ${carnetError.message}`,
                                type: 'carnet_fetch_error'
                            });
                        }

                        // ***** AQUÍ ESTÁ LA VERIFICACIÓN CRÍTICA *****
                        console.log(`¿Enviar email? ${formData.send_email} para ${formData.number_id}`);

                        if (formData.send_email) { // Solo enviar email si el switch está activado
                            console.log(`SÍ enviando email para ${formData.number_id}`);
                            try {
                                const emailResponse = await sendEnrollmentEmail(formData, carnetFilePath);
                                if (emailResponse && emailResponse.success) {
                                    emailSuccesses++;
                                } else {
                                    errors.push({
                                        student: formData.number_id,
                                        message: `Matrícula exitosa, pero error al enviar correo: ${emailResponse?.message || 'Error desconocido'}\nDetalles: ${JSON.stringify(emailResponse)}`,
                                        type: 'email'
                                    });
                                }
                            } catch (emailError) {
                                errors.push({
                                    student: formData.number_id,
                                    message: `Matrícula exitosa, pero error al enviar correo: ${emailError.message}\nDetalles: ${emailError.stack || 'No disponible'}`,
                                    type: 'email'
                                });
                            }
                        } else {
                            console.log(`NO enviando email para ${formData.number_id} - switch desactivado`);
                        }
                    } else {
                        errors.push({
                            student: formData.number_id,
                            message: `Error en la matrícula: ${enrollData.message || 'Error desconocido'}\nDetalles: ${JSON.stringify(enrollData)}`,
                            type: 'enroll'
                        });
                    }
                } catch (error) {
                    processed++; // Asegurarse de que processed se incremente incluso si hay un error temprano.
                    errors.push({
                        student: formData.number_id || 'Desconocido', // formData puede no estar completamente definido si el error es muy temprano
                        message: `Error: ${error.message}\nDetalles: ${error.stack || 'No disponible'}\nDatos enviados: ${JSON.stringify(formData)}`,
                        type: 'server'
                    });

                    // Actualizar progreso incluso en caso de error
                    const swalContent = document.getElementById('enrollmentProgress');
                    if (swalContent) {
                        swalContent.innerHTML = `
                            Procesando: <b>${processed}</b> de ${usersToEnroll.length}<br>
                            Éxitos: <b>${successes}</b><br>
                            Errores: <b>${errors.length}</b><br>
                            Por favor espere...
                        `;
                    }
                }
            }

            // Generar mensaje de resumen detallado
            let message = `<h4>Resumen de matrícula</h4>`;
            message += `<p>Total procesados: <b>${processed}</b></p>`;
            message += `<p>Matrículas exitosas: <b>${successes}</b></p>`;
            message += `<p>Correos enviados: <b>${emailSuccesses}</b></p>`;
            message += `<p>Errores totales: <b>${errors.length}</b></p>`;

            if (errors.length > 0) {
                message += '<hr><h5>Detalles de errores/advertencias:</h5>';
                message += '<div style="max-height: 200px; overflow-y: auto; text-align: left;">';
                errors.forEach((err, index) => {
                    message += `
                        <p><b>${index + 1}. Usuario:</b> ${err.student}</p>
                        <p><b>Tipo:</b> ${err.type}</p>
                        <p><b>Mensaje:</b> ${err.message}</p>
                        <hr>
                    `;
                });
                message += '</div>';
            }

            return {
                message,
                icon: errors.length === 0 ? 'success' : (successes > 0 ? 'warning' : 'error')
            };
        }

        // Modificar la función getFormDataFromRow en multipleRegistrations.php
        async function getFormDataFromRow(row) {
            // Obtener datos del usuario desde el Map de usuarios seleccionados
            const numberId = row.dataset.numberId;
            const userData = selectedUsers.get(numberId) || {};

            const getCourseData = (prefix) => {
                const select = document.getElementById(prefix);
                if (!select) {
                    console.error(`Select no encontrado: ${prefix}`);
                    return {
                        id: '0',
                        name: 'No seleccionado'
                    };
                }
                const option = select.options[select.selectedIndex];
                const fullText = option.text;
                const id = option.value;
                const name = fullText.split(' - ').slice(1).join(' - ').trim();

                return {
                    id,
                    name
                };
            };

            // Leer dinámicamente el estado del switch
            const sendEmailCheckbox = row.querySelector('.send-email-checkbox');
            const send_email = sendEmailCheckbox ? sendEmailCheckbox.checked : true;

            const formData = {
                type_id: userData.type_id || row.dataset.typeId,
                number_id: numberId,
                full_name: userData.full_name || row.dataset.fullName,
                email: userData.email || row.dataset.email,
                institutional_email: userData.institutional_email || row.dataset.institutionalEmail,
                department: userData.department || row.dataset.department,
                headquarters: userData.headquarters || row.dataset.headquarters,
                program: userData.program || row.dataset.program,
                mode: userData.mode || row.dataset.mode,
                password: userData.password || numberId,
                id_bootcamp: getCourseData('bootcamp').id,
                bootcamp_name: getCourseData('bootcamp').name,
                id_leveling_english: getCourseData('ingles').id,
                leveling_english_name: getCourseData('ingles').name,
                id_english_code: getCourseData('english_code').id,
                english_code_name: getCourseData('english_code').name,
                id_skills: getCourseData('skills').id,
                skills_name: getCourseData('skills').name,
                send_email: send_email // Usar el valor leído del switch, no hardcodeado
            };

            return formData;
        }

        // Modificar el evento de matrícula
        document.getElementById('enrollSelectedUsers').addEventListener('click', function() {
            if (selectedUsers.size === 0) {
                Swal.fire('Error', 'No hay usuarios seleccionados', 'error');
                return;
            }

            try {
                // LEER DINÁMICAMENTE el estado del switch para cada usuario
                const usersToEnroll = Array.from(selectedUsers.values()).map(userData => {
                    const row = document.querySelector(`tr[data-number-id="${userData.number_id}"]`);
                    if (!row) {
                        throw new Error(`No se encontraron los datos completos para el usuario ${userData.full_name}`);
                    }

                    // Leer el estado ACTUAL del switch
                    const sendEmailCheckbox = row.querySelector('.send-email-checkbox');
                    const send_email = sendEmailCheckbox ? sendEmailCheckbox.checked : true;

                    // Combinar datos guardados con el estado actual del switch
                    return {
                        ...userData,
                        send_email: send_email // Estado actual del switch
                    };
                });

                confirmBulkEnrollment(usersToEnroll);
            } catch (error) {
                Swal.fire('Error', error.message, 'error');
            }
        });

        // Modificar la función toggleUserSelection para guardar todos los campos necesarios
        function toggleUserSelection(checkbox, row) {
            const numberId = row.dataset.numberId;

            if (checkbox.checked) {
                // Obtener datos de los cursos seleccionados
                const getCourseData = (prefix) => {
                    const select = document.getElementById(prefix);
                    if (!select) {
                        console.error(`Select no encontrado: ${prefix}`);
                        return {
                            id: '0',
                            name: 'No seleccionado'
                        };
                    }
                    const option = select.options[select.selectedIndex];
                    return {
                        id: option.value,
                        name: option.text.split(' - ').slice(1).join(' - ').trim()
                    };
                };

                const bootcamp = getCourseData('bootcamp');
                const ingles = getCourseData('ingles');
                const englishCode = getCourseData('english_code');
                const skills = getCourseData('skills');

                // NO guardamos send_email aquí - siempre se leerá dinámicamente
                selectedUsers.set(numberId, {
                    type_id: row.dataset.typeId,
                    number_id: numberId,
                    full_name: row.dataset.fullName,
                    email: row.dataset.email,
                    institutional_email: row.dataset.institutionalEmail,
                    department: row.dataset.department,
                    headquarters: row.dataset.headquarters,
                    program: row.dataset.program,
                    mode: row.dataset.mode,
                    password: numberId,
                    id_bootcamp: bootcamp.id,
                    bootcamp_name: bootcamp.name,
                    id_leveling_english: ingles.id,
                    leveling_english_name: ingles.name,
                    id_english_code: englishCode.id,
                    english_code_name: englishCode.name,
                    id_skills: skills.id,
                    skills_name: skills.name
                    // SIN send_email - se leerá dinámicamente
                });
            } else {
                selectedUsers.delete(numberId);
            }

            updateSelectedUsersList();
        }

        function updateSelectedUsersList() {
            const container = document.getElementById('selectedUsersContainer');
            const selectedCount = document.getElementById('selectedCount');
            const floatingSelectedCount = document.getElementById('floatingSelectedCount');

            container.innerHTML = '';
            selectedUsers.forEach((userData, numberId) => {
                const userCard = document.createElement('div');
                userCard.className = 'card mb-2';
                userCard.innerHTML = `
            <div class="card-body d-flex flex-column text-center">
                <h6 class="card-title mb-2"><b>${userData.full_name}</b></h6>
                <p class="card-text mb-1">
                    <strong>ID:</strong> ${numberId}
                </p>
                <p class="card-text mb-1">
                    <strong>Email:</strong> ${userData.institutional_email}
                </p>
                <button class="btn border-0" type="button" disabled>
                    <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
                    <span role="status">En espera para matricular</span>
                </button>
                <button class="btn btn-danger btn-sm mt-auto delete-selection" data-number-id="${numberId}">
                    <i class="bi bi-trash"></i> Eliminar selección
                </button>
            </div>
        `;
                container.appendChild(userCard);

                // Agregar el evento click al botón de eliminar
                const deleteButton = userCard.querySelector('.delete-selection');
                deleteButton.addEventListener('click', function() {
                    const numberId = this.getAttribute('data-number-id');
                    removeSelectedUser(numberId);
                });
            });

            const count = selectedUsers.size;
            if (selectedCount) selectedCount.textContent = count;
            if (floatingSelectedCount) floatingSelectedCount.textContent = count;
        }

        document.getElementById('enrollSelectedUsers').addEventListener('click', function() {
            if (selectedUsers.size === 0) {
                Swal.fire('Error', 'No hay usuarios seleccionados', 'error');
                return;
            }

            // Convertir el Map a un array de usuarios para procesar
            const usersToEnroll = Array.from(selectedUsers.values());
            confirmBulkEnrollment(usersToEnroll);
        });

        function removeSelectedUser(numberId) {
            selectedUsers.delete(numberId);
            // Desmarcar el checkbox en la tabla si está visible
            const checkbox = document.querySelector(`tr[data-number-id="${numberId}"] input[type="checkbox"]`);
            if (checkbox) {
                checkbox.checked = false;
            }
            updateSelectedUsersList();
        }

        // Agregar esta nueva función para enviar el correo
        async function sendEnrollmentEmail(userData, carnetFilePath = null) {
            try {
                const emailData = {
                    email: userData.email,
                    program: userData.program,
                    first_name: userData.full_name.split(' ')[0],
                    usuario: userData.number_id,
                    password: userData.password
                };

                if (carnetFilePath) {
                    emailData.carnet_file_path = carnetFilePath;
                }

                const response = await fetch('components/registerMoodle/send_email.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(emailData)
                });

                const data = await response.json();

                // Mostrar errores HTTP en consola
                if (!response.ok || !data.success) {
                    console.error('Error enviando email:', data.message || response.statusText);
                }

                return data;
            } catch (error) {
                console.error('Error enviando email:', error);
                return {
                    success: false,
                    message: 'Error enviando el correo electrónico: ' + error.message
                };
            }
        }

        $(document).ready(function() {
            $('[data-toggle="popover"]').popover({
                placement: 'top',
                trigger: 'focus',
                html: true
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const tableWrapper = document.querySelector('.table-wrapper');

            // Mantener la posición del scroll al filtrar
            function saveScrollPosition() {
                sessionStorage.setItem('tableScrollPosition', tableWrapper.scrollTop);
            }

            function restoreScrollPosition() {
                const scrollPosition = sessionStorage.getItem('tableScrollPosition');
                if (scrollPosition) {
                    tableWrapper.scrollTop = parseInt(scrollPosition);
                }
            }

            // Guardar posición del scroll cuando el usuario hace scroll
            tableWrapper.addEventListener('scroll', saveScrollPosition);

            // Restaurar posición del scroll después de filtrar
            const filterSelects = document.querySelectorAll('select[id^="filter"]');
            filterSelects.forEach(select => {
                select.addEventListener('change', function() {
                    setTimeout(restoreScrollPosition, 100);
                });
            });
        });

        // Agregar esta función al final del archivo, dentro de las etiquetas <script>
        function mostrarCertificacionAlert(nombreEstudiante) {
            Swal.fire({
                icon: 'warning',
                title: 'Estudiante con certificación previa',
                html: `
                    <div class="alert alert-warning">
                        <p><strong>${nombreEstudiante.toUpperCase()}</strong> ya tiene registrada una certificación en otro lote o región.</p>
                        <p>Tenga esto en cuenta antes de continuar con el proceso de asignación.</p>
                    </div>
                `,
                confirmButtonText: 'Entendido',
                confirmButtonColor: '#ffbf00',
                allowOutsideClick: true
            });
        }

        // Asegúrate de que los popovers estén inicializados
        document.addEventListener('DOMContentLoaded', function() {
            const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
            if (popoverTriggerList.length > 0) {
                [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl));
            }
        });
    </script>
    </body>

    </html>

```


### Multiple V2


```html 

<?php
require __DIR__ . '../../../controller/conexion.php';
if (!isset($conn) || $conn->connect_error) {
    die("Error de conexión a la base de datos: " .
        (isset($conn) ? $conn->connect_error : "No se pudo establecer la conexión"));
}

include __DIR__ . '/../moodle_api.php';
$courses_data = getCoursesB();

// Consulta para obtener usuarios
$sql = "SELECT ur.*, d.departamento, ca.*
    FROM user_register ur
    INNER JOIN departamentos d ON ur.department = d.id_departamento 
    LEFT JOIN course_assignments ca ON ur.number_id = ca.student_id
    WHERE d.id_departamento = 11
      AND ur.status = '1' 
    AND ur.statusAdmin IN ('1', '8')";

$result = $conn->query($sql);
$data = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
} else {
    echo '<div class="alert alert-info">No hay datos disponibles.</div>';
}

// Obtener datos únicos para los filtros
$departamentos = ['BOGOTÁ, D.C.'];
$programas = [];
$modalidades = [];
$sedes = []; // Agregar array para sedes
$niveles = ['Explorador', 'Integrador', 'Innovador'];
$horarios = [];

foreach ($data as $row) {
    $depto = $row['departamento'];
    $sede = $row['headquarters'];


    // Obtener sedes únicas
    if (!in_array($sede, $sedes) && !empty($sede)) {
        $sedes[] = $sede;
    }

    // Obtener programas únicas
    if (!in_array($row['program'], $programas)) {
        $programas[] = $row['program'];
    }

    // Obtener modalidades únicas
    if (!in_array($row['mode'], $modalidades)) {
        $modalidades[] = $row['mode'];
    }

    // Obtener horarios únicos
    if (!in_array($row['schedules'], $horarios)) {
        $horarios[] = $row['schedules'];
    }
}

?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    /* Estilos para la tabla con scroll */
    /* Estilos para la tabla con encabezados fijos */
    .table-container {
        max-height: 60vh;
        overflow: hidden;
        border: 1px solidrgb(49, 28, 75);
        border-radius: 4px;
        padding: 5px;
    }

    .table-wrapper {
        overflow-y: auto;
        max-height: inherit;
    }

    .table {
        margin-bottom: 0;
    }

    .table thead th {
        border: 1px solid transparent;
        position: sticky;
        top: 0;
        z-index: 3;
    }

    /* Evitar bordes automáticos */
    .table-bordered th {
        border: 1px solid transparent !important;
    }

    .table td {
        white-space: nowrap;
        padding: 8px;
    }

    /* Ajuste para el scroll horizontal */
    .table-responsive {
        overflow-x: auto;
        margin-bottom: 1rem;
    }

    /* Asegurar que el encabezado se mantenga visible al hacer scroll horizontal */
    .table thead {
        position: sticky;
        top: 0;
        z-index: 2;
    }

    /* Estilo para el fondo del encabezado */
    .thead-dark {
        background-color: #343a40;
        color: white;
    }

    .table-container {
        max-height: 60vh;
        overflow: hidden;
        border: 1px solid #ddd;
        border-radius: 4px;
        margin-top: 20px;
    }

    .table-wrapper {
        overflow-y: auto;
        max-height: inherit;
    }

    .table {
        width: 100%;
        margin-bottom: 0;
        background-color: white;
    }

    /* Asegurarse que la tabla sea visible cuando se muestre */
    .table.table-bordered {
        display: table !important;
        visibility: visible !important;
    }
</style>
<div class="container-fluid px-2">
    <div class="table-responsive">

        <div class="row">
            <div class="col-md-3">
                <b class="text-left mb-1"><i class="bi bi-card-checklist"></i> Seleccionar cursos</b>

                <div class="col-md-12 col-sm-12 col-lg-12">
                    <div class="course-title text-indigo-dark "><i class="bi bi-laptop"></i> Bootcamp</div>
                    <div class="card course-card card-bootcamp" data-icon="💻">
                        <div class="card-body">
                            <select id="bootcamp" class="form-select course-select">
                                <?php if (!empty($courses_data)): ?>
                                    <?php foreach ($courses_data as $course): ?>
                                        <?php
                                        $categoryAllowed = in_array($course['categoryid'], [3, 7, 8, 9, 10, 11, 12]);
                                        $noCopiar = stripos($course['fullname'], 'Copiar') === false; // Excluir cursos con 'copiar'
                                        if ($categoryAllowed && $noCopiar):
                                        ?>
                                            <option value="<?php echo htmlspecialchars($course['id']); ?>">
                                                <?php echo htmlspecialchars($course['id'] . ' - ' . $course['fullname']); ?>
                                            </option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?></option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Agregar después del selector de bootcamp -->
                <div class="col-md-12 mt-2" id="activeFilterInfo" style="display: none;">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        Filtro activo por código: <strong id="activeCodeFilter"></strong>
                        <button class="btn btn-sm btn-outline-secondary ms-2" id="clearFilter">
                            <i class="bi bi-x-circle"></i> Limpiar filtro
                        </button>
                    </div>
                </div>

                <div class="col-md-12 col-sm-12 col-lg-12">
                    <div class="course-title text-indigo-dark"><i class="bi bi-translate"></i> Inglés nivelatorio</div>
                    <div class="card course-card card-ingles" data-icon="🌍">
                        <div class="card-body">
                            <select id="ingles" class="form-select course-select" readonly>
                                <?php if (!empty($courses_data)): ?>
                                    <?php foreach ($courses_data as $course): ?>
                                        <?php
                                        $categoryAllowed = in_array($course['categoryid'], [4]);
                                        $noCopiar = stripos($course['fullname'], 'Copiar') === false; // Excluir cursos con 'Copiar'
                                        if ($categoryAllowed && $noCopiar):
                                        ?>
                                            <option value="<?php echo htmlspecialchars($course['id']); ?>">
                                                <?= htmlspecialchars($course['id'] . ' - ' . $course['fullname']) ?>
                                            </option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 col-sm-12 col-lg-12">
                    <div class="course-title text-indigo-dark"><i class="bi bi-code-slash"></i> English Code</div>
                    <div class="card course-card card-english-code" data-icon="👨‍💻">
                        <div class="card-body">
                            <select id="english_code" class="form-select course-select" readonly>
                                <?php if (!empty($courses_data)): ?>
                                    <?php foreach ($courses_data as $course): ?>
                                        <?php
                                        $categoryAllowed = in_array($course['categoryid'], [5]);
                                        $noCopiar = stripos($course['fullname'], 'Copiar') === false; // Excluir cursos con 'Copiar'
                                        if ($categoryAllowed && $noCopiar):
                                        ?>
                                            <option value="<?php echo htmlspecialchars($course['id']); ?>">
                                                <?= htmlspecialchars($course['id'] . ' - ' . $course['fullname']) ?>
                                            </option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 col-sm-12 col-lg-12">
                    <div class="course-title text-indigo-dark"><i class="bi bi-lightbulb"></i> Habilidades</div>
                    <div class="card course-card card-skills" data-icon="💡">
                        <div class="card-body">
                            <select id="skills" class="form-select course-select" readonly>
                                <?php if (!empty($courses_data)): ?>
                                    <?php foreach ($courses_data as $course): ?>
                                        <?php
                                        $categoryAllowed = in_array($course['categoryid'], [6]);
                                        $noCopiar = stripos($course['fullname'], 'Copiar') === false; // Excluir cursos con 'Copiar'
                                        if ($categoryAllowed && $noCopiar):
                                        ?>
                                            <option value="<?php echo htmlspecialchars($course['id']); ?>">
                                                <?= htmlspecialchars($course['id'] . ' - ' . $course['fullname']) ?>
                                            </option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                <div class="d-flex gap-2 mb-3">

                    <!-- Button to open filter modal -->
                    <!-- <button type="button" class="btn bg-indigo-dark text-white" data-bs-toggle="modal" data-bs-target="#filterModal">
                        <i class="bi bi-filter-circle"></i> Filtros personalizados
                    </button> -->

                    <!-- Botón para exportar a Excel -->
                    <button id="exportarExcel" class="btn btn-success"
                        onclick="window.location.href='components/registerMoodle/export_excel_enrolled.php?action=export'">
                        <i class="bi bi-file-earmark-excel"></i> Exportar a Excel
                    </button>

                    <!-- Botón para mostrar usuarios seleccionados -->
                    <button class="btn bg-magenta-dark text-white d-flex align-items-center gap-2"
                        type="button"
                        data-bs-toggle="offcanvas"
                        data-bs-target="#selectedUsersList">
                        <i class="bi bi-list-check"></i>
                        <span>Gestionar seleccionados (<span id="floatingSelectedCount">0</span>)</span>
                    </button>

                </div>
                <div class="table-container">
                    <div class="table-wrapper">

                        <table class="table table-hover table-bordered" style="display: none;">
                            <thead class="thead-dark text-center">
                                <tr>
                                    <th>#</th>
                                    <th>Tipo ID</th>
                                    <th>Número</th>
                                    <th>Nombre</th>
                                    <th>Programa</th>
                                    <th>Preferencia</th>
                                    <th>Modalidad</th>
                                    <th class="text-center">
                                        <input type="checkbox" id="selectAllCheckbox" class="form-check-input"
                                            style="width: 25px; height: 25px; appearance: none; background-color: white; border: 2px solid #ec008c; cursor: pointer; position: relative;">
                                    </th>
                                    <th>Email</th>
                                    <th>Enviar email</th>
                                    <th>Nuevo Email</th>
                                    <th>Departamento</th>
                                    <th>Sede</th>
                                    <th>Bootcamp pre-asignado</th>
                                    <th>Ingles nivelador pre-asignado</th>
                                    <th>English code pre-asignado</th>
                                    <th>Habilidades de poder pre-asignado</th>
                                    <th>Horario</th>
                                </tr>
                            </thead>
                            <tbody class="text-center" id="studentsTableBody">
                                <!-- Los datos se cargarán dinámicamente -->
                            </tbody>
                            <div id="initialMessage" class="text-center p-5">
                                <i class="bi bi-arrow-left-circle fs-1 text-muted"></i>
                                <h4 class="mt-3">Seleccione un bootcamp para cargar los estudiantes</h4>
                                <p class="text-muted">Los datos se cargarán automáticamente al seleccionar un curso</p>
                            </div>
                        </table>
                    </div>
                </div>



                <!-- Filter Modal -->
                <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="filterModalLabel">
                                    <i class="bi bi-filter-circle"></i> Filtros de Búsqueda
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6 col-sm-12 mb-3">
                                        <div class="filter-title"><i class="bi bi-map"></i> Departamento</div>
                                        <div class="card filter-card card-department" data-icon="📍">
                                            <div class="card-body">
                                                <select id="filterDepartment" class="form-select">
                                                    <option value="">Todos los departamentos</option>
                                                    <option value="BOYACÁ">BOYACÁ</option>
                                                    <option value="CUNDINAMARCA">CUNDINAMARCA</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-sm-12 mb-3">
                                        <div class="filter-title"><i class="bi bi-building"></i> Sede</div>
                                        <div class="card filter-card card-headquarters" data-icon="🏫">
                                            <div class="card-body">
                                                <select id="filterHeadquarters" class="form-select">
                                                    <option value="">Todas las sedes</option>
                                                    <?php foreach ($sedes as $sede): ?>
                                                        <option value="<?= htmlspecialchars($sede) ?>"><?= htmlspecialchars($sede) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-sm-12 mb-3">
                                        <div class="filter-title"><i class="bi bi-mortarboard"></i> Programa</div>
                                        <div class="card filter-card card-program" data-icon="🎓">
                                            <div class="card-body">
                                                <select id="filterProgram" class="form-select">
                                                    <option value="">Todos los programas</option>
                                                    <?php foreach ($programas as $programa): ?>
                                                        <option value="<?= htmlspecialchars($programa) ?>"><?= htmlspecialchars($programa) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-sm-12 mb-3">
                                        <div class="filter-title"><i class="bi bi-laptop"></i> Modalidad</div>
                                        <div class="card filter-card card-mode" data-icon="💻">
                                            <div class="card-body">
                                                <select id="filterMode" class="form-select">
                                                    <option value="">Todas las modalidades</option>
                                                    <option value="Virtual">Virtual</option>
                                                    <option value="Presencial">Presencial</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-sm-12 mb-3">
                                        <div class="filter-title"><i class="bi bi-layers"></i> Preferencia</div>
                                        <div class="card filter-card card-level" data-icon="⭐">
                                            <div class="card-body">
                                                <select id="filterLevel" class="form-select">
                                                    <option value="">Todos los niveles</option>
                                                    <option value="Explorador">Explorador</option>
                                                    <option value="Integrador">Integrador</option>
                                                    <option value="Innovador">Innovador</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-sm-12 mb-3">
                                        <div class="filter-title"><i class="bi bi-clock"></i> Horario</div>
                                        <div class="card filter-card card-schedule" data-icon="⏰">
                                            <div class="card-body">
                                                <select id="filterSchedule" class="form-select">
                                                    <option value="">Todos los horarios</option>
                                                    <?php foreach ($horarios as $horario): ?>
                                                        <option value="<?= htmlspecialchars($horario) ?>"><?= htmlspecialchars($horario) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn bg-magenta-dark text-white" data-bs-dismiss="modal">
                                    <i class="bi bi-check2-circle"></i> Ver resultados
                                </button>
                            </div>
                        </div>
                    </div>
                </div>



                <!-- Agregar después de la tabla -->
                <div class="offcanvas offcanvas-end" tabindex="-1" id="selectedUsersList" aria-labelledby="selectedUsersListLabel">
                    <div class="offcanvas-header">
                        <h5 class="offcanvas-title" id="selectedUsersListLabel">
                            <i class="bi bi-person-check"></i> Beneficiarios seleccionados (<span id="selectedCount">0</span>)
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>

                    </div>


                    <div class="offcanvas-body">
                        <div class="m-3">
                            <button id="enrollSelectedUsers" class="btn bg-magenta-dark text-white w-100">
                                <i class="bi bi-patch-check-fill"></i> Matricular Seleccionados
                            </button>
                        </div>
                        <div id="selectedUsersContainer"></div>

                    </div>
                </div>


            </div>
        </div>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                // 1. Definir las variables globales
                const bootcampSelect = document.getElementById('bootcamp');
                const tableContainer = document.querySelector('.table');
                const initialMessage = document.getElementById('initialMessage');
                let rowCounter = 1; // Agregar variable contador fuera de la función
                const selectedUsers = new Map(); // selectedUsers se define aquí

                // 2. Función para crear el spinner
                function createLoadingSpinner() {
                    const spinner = document.createElement('div');
                    spinner.innerHTML = `
                        <div class="text-center p-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                            <h5 class="mt-3">Cargando estudiantes...</h5>
                        </div>
                    `;
                    return spinner;
                }

                // 3. Función para obtener el código del curso
                function getCourseCode(courseName) {
                    if (courseName && courseName.length >= 6) {
                        const match = courseName.match(/C\d+L\d+-G\d+[A-Z]?/);
                        return match ? match[0] : courseName.slice(-6);
                    }
                    return null;
                }

                // 4. Función para cargar los datos
                async function loadStudentsData(courseCode) {
                    rowCounter = 1; // Resetear contador al cargar nuevos datos
                    const loadingSpinner = createLoadingSpinner();

                    try {
                        // Mostrar spinner y ocultar mensaje inicial
                        initialMessage.style.display = 'none';
                        document.querySelector('.table-wrapper').insertBefore(loadingSpinner, tableContainer);

                        console.log('Cargando datos para el código:', courseCode); // Debug

                        const response = await fetch(`components/registerMoodle/load_students.php?courseCode=${courseCode}`);
                        const data = await response.json();

                        console.log('Datos recibidos:', data); // Debug

                        if (data.success) {
                            const tbody = document.getElementById('studentsTableBody');
                            tbody.innerHTML = ''; // Limpiar tabla existente

                            data.data.forEach(row => {
                                const tr = createStudentRow(row);
                                tbody.appendChild(tr);
                            });

                            // Mostrar tabla y ocultar spinner
                            loadingSpinner.remove();
                            tableContainer.style.display = 'table';
                            initialMessage.style.display = 'none';

                            // Asegurarnos de que la tabla sea visible
                            const table = document.querySelector('.table');
                            if (table) {
                                table.style.display = 'table';
                            }

                            updateVisibleRowsCount();
                            if (courseCode) {
                                filterStudentsByCode(courseCode);
                            }
                        } else {
                            throw new Error(data.message || 'No se pudieron cargar los datos');
                        }
                    } catch (error) {
                        console.error('Error cargando datos:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'No se pudieron cargar los datos: ' + error.message
                        });

                        loadingSpinner.remove();
                        initialMessage.style.display = 'block';
                    }
                }

                // 5. Agregar el evento change al selector de bootcamp
                if (bootcampSelect) {
                    bootcampSelect.addEventListener('change', function() {
                        const selectedOption = this.options[this.selectedIndex];
                        const selectedText = selectedOption.text || '';
                        const courseCode = getCourseCode(selectedText);

                        console.log('Bootcamp seleccionado:', selectedText);
                        console.log('Código extraído:', courseCode);

                        if (courseCode) {
                            try {
                                loadStudentsData(courseCode);
                                autoSelectRelatedCourses(courseCode);
                            } catch (error) {
                                console.error('Error al cargar datos:', error);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Error al cargar los datos: ' + error.message
                                });
                            }
                        }
                    });
                }

                // 6. Función para actualizar el contador de filas visibles
                function updateVisibleRowsCount() {
                    const visibleRows = document.querySelectorAll('table tbody tr[style="display: ;"], table tbody tr:not([style*="display"])').length;
                    const totalRows = document.querySelectorAll('table tbody tr').length;

                    let countElement = document.getElementById('rowsCounter');
                    if (!countElement) {
                        countElement = document.createElement('div');
                        countElement.id = 'rowsCounter';
                        countElement.className = 'alert alert-info mt-2';
                        const tableContainer = document.querySelector('.table-container');
                        if (tableContainer) {
                            tableContainer.parentNode.insertBefore(countElement, tableContainer);
                        }
                    }

                    countElement.innerHTML = `Mostrando <b>${visibleRows}</b> de <b>${totalRows}</b> estudiantes`;
                }

                // 7. Función para crear la fila de estudiante
                function createStudentRow(rowData) {
                    const tr = document.createElement('tr');

                    // Construir el nombre completo
                    const fullName = `${rowData.first_name || ''} ${rowData.second_name || ''} ${rowData.first_last || ''} ${rowData.second_last || ''}`.trim();

                    // Generar el correo institucional siguiendo el mismo patrón
                    const nuevoCorreo = (
                        (rowData.first_name ? rowData.first_name.substring(0, 1).toLowerCase() : '') +
                        (rowData.second_name ? rowData.second_name.substring(0, 1).toLowerCase() : '') +
                        (rowData.number_id ? rowData.number_id.slice(-4) : '') +
                        (rowData.first_last ? rowData.first_last.substring(0, 1).toLowerCase() : '') +
                        (rowData.second_last ? rowData.second_last.substring(0, 1).toLowerCase() : '') +
                        '.ut@cendi.edu.co'
                    );

                    // Configurar datasets
                    tr.dataset.typeId = rowData.typeID;
                    tr.dataset.numberId = rowData.number_id;
                    tr.dataset.fullName = fullName;
                    tr.dataset.program = rowData.program;
                    tr.dataset.level = rowData.level;
                    tr.dataset.mode = rowData.mode;
                    tr.dataset.email = rowData.email;
                    tr.dataset.institutionalEmail = nuevoCorreo; // Agregar el nuevo correo al dataset
                    tr.dataset.department = rowData.departamento;
                    tr.dataset.headquarters = rowData.headquarters;
                    tr.dataset.schedule = rowData.schedules;

                    // Modificar la construcción del HTML de la fila para incluir el nuevo correo
                    tr.innerHTML = `
                        <td><span class="badge bg-magenta-dark"></span></td>
                        <td>${rowData.typeID || ''}</td>
                        <td>${rowData.number_id || ''}</td>
                        <td style="max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            ${fullName}
                        </td>
                        <td>${rowData.program || ''}</td>
                        <td>${rowData.level || ''}</td>
                        <td>${rowData.mode || ''}</td>
                        <td class="text-center">
                            <input type="checkbox" 
                                   class="form-check-input student-checkbox" 
                                   data-student-id="${rowData.number_id}"
                                   data-student-name="${fullName}"
                                   data-student-email="${rowData.email}"
                                   style="width: 25px; height: 25px; appearance: none; background-color: white; border: 2px solid #ec008c; cursor: pointer;">
                        </td>
                        <td>${rowData.email || ''}</td>
                        <td>
                            <div class="form-check form-switch d-flex justify-content-center">
                                <input class="form-check-input send-email-checkbox"
                                    type="checkbox"
                                    checked
                                    style="width: 2.5em; height: 1.5em; background-color: #fff; border: 2px solid #30336b; transition: background-color 0.2s;"
                                    onchange="this.style.backgroundColor = this.checked ? '#30336b' : '#fff';"
                                    onload="this.style.backgroundColor = this.checked ? '#30336b' : '#fff';">
                            </div>
                        </td>
                        <td>${nuevoCorreo}</td>
                        <td>${rowData.departamento || ''}</td>
                        <td>${rowData.headquarters || ''}</td>
                        <td>${rowData.bootcamp_pre_asignado || 'Sin asignar'}</td>
                        <td>${rowData.ingles_pre_asignado || 'Sin asignar'}</td>
                        <td>${rowData.english_code_pre_asignado || 'Sin asignar'}</td>
                        <td>${rowData.habilidades_pre_asignado || 'Sin asignar'}</td>
                        <td class="text-center">
                            <a class="btn bg-indigo-light" 
                               tabindex="0" 
                               role="button" 
                               data-bs-toggle="popover" 
                               data-bs-trigger="focus" 
                               data-bs-placement="top"
                               title="${rowData.schedules || 'Sin horario asignado'}">
                                <i class="bi bi-clock-history"></i>
                            </a>
                        </td>
                    `;

                    // Inicializar los popovers
                    const popoverTriggerList = [].slice.call(tr.querySelectorAll('[data-bs-toggle="popover"]'));
                    popoverTriggerList.forEach(popoverTriggerEl => {
                        new bootstrap.Popover(popoverTriggerEl);
                    });

                    // Inicializar el estilo del switch
                    const sendEmailCheckbox = tr.querySelector('.send-email-checkbox');
                    if (sendEmailCheckbox) {
                        sendEmailCheckbox.style.backgroundColor = sendEmailCheckbox.checked ? '#30336b' : '#fff';
                    }

                    return tr;
                }

                // 8. Función para filtrar estudiantes por código
                function filterStudentsByCode(courseCode) {
                    let visibleCounter = 1; // Contador solo para filas visibles

                    if (!courseCode) {
                        document.querySelectorAll('#studentsTableBody tr').forEach(row => {
                            row.style.display = '';
                            // Actualizar el número de la fila visible
                            row.querySelector('td:first-child .badge').textContent = visibleCounter++;
                        });
                        updateVisibleRowsCount();
                        return;
                    }

                    document.querySelectorAll('#studentsTableBody tr').forEach(row => {
                        const bootcampCell = row.querySelector('td:nth-child(13)');
                        const inglesCell = row.querySelector('td:nth-child(14)');
                        const englishCodeCell = row.querySelector('td:nth-child(15)');
                        const skillsCell = row.querySelector('td:nth-child(16)');

                        const bootcampText = bootcampCell?.textContent || '';
                        const inglesText = inglesCell?.textContent || '';
                        const englishCodeText = englishCodeCell?.textContent || '';
                        const skillsText = skillsCell?.textContent || '';

                        if (bootcampText.includes(courseCode) ||
                            inglesText.includes(courseCode) ||
                            englishCodeText.includes(courseCode) ||
                            skillsText.includes(courseCode)) {
                            row.style.display = '';
                            // Actualizar el número solo para las filas visibles
                            row.querySelector('td:first-child .badge').textContent = visibleCounter++;
                        } else {
                            row.style.display = 'none';
                            // Limpiar el número de las filas ocultas
                            row.querySelector('td:first-child .badge').textContent = '';
                        }
                    });

                    updateVisibleRowsCount();
                }

                // 9. Función para autoseleccionar cursos relacionados
                function autoSelectRelatedCourses(courseCode) {
                    if (!courseCode) return;

                    const selectors = ['ingles', 'english_code', 'skills'];

                    selectors.forEach(selector => {
                        const selectElement = document.getElementById(selector);
                        if (!selectElement) return;

                        // Deshabilitar temporalmente el selector
                        selectElement.disabled = true;

                        // Buscar opción con el mismo código
                        const options = Array.from(selectElement.options);
                        const matchingOption = options.find(option =>
                            option.text.includes(courseCode)
                        );

                        if (matchingOption) {
                            selectElement.value = matchingOption.value;
                        } else {
                            // Si no hay coincidencia, habilitar el selector
                            selectElement.disabled = false;
                            selectElement.selectedIndex = 0;
                        }
                    });

                    // Actualizar información del filtro activo
                    const filterInfo = document.getElementById('activeFilterInfo');
                    const activeCodeFilter = document.getElementById('activeCodeFilter');

                    if (filterInfo && activeCodeFilter) {
                        activeCodeFilter.textContent = courseCode;
                        filterInfo.style.display = 'block';
                    }
                }

                // 10. Manejar el botón de limpiar filtro
                const clearFilterBtn = document.getElementById('clearFilter');
                if (clearFilterBtn) {
                    clearFilterBtn.addEventListener('click', function() {
                        // Limpiar selectores
                        ['ingles', 'english_code', 'skills'].forEach(selector => {
                            const selectElement = document.getElementById(selector);
                            if (selectElement) {
                                selectElement.disabled = false;
                                selectElement.selectedIndex = 0;
                            }
                        });

                        // Mostrar todas las filas
                        document.querySelectorAll('#studentsTableBody tr').forEach(row => {
                            row.style.display = '';
                        });

                        // Ocultar info del filtro
                        const filterInfo = document.getElementById('activeFilterInfo');
                        if (filterInfo) {
                            filterInfo.style.display = 'none';
                        }

                        updateVisibleRowsCount();
                    });
                }

                // 11. Modificar el manejo del checkbox "Seleccionar todos"
                const selectAllCheckbox = document.getElementById('selectAllCheckbox');
                if (selectAllCheckbox) {
                    selectAllCheckbox.addEventListener('change', function() {
                        // IMPORTANTE: Solo seleccionar checkboxes de filas VISIBLES
                        const visibleCheckboxes = Array.from(document.querySelectorAll('.student-checkbox'))
                            .filter(checkbox => checkbox.closest('tr').style.display !== 'none');

                        visibleCheckboxes.forEach(checkbox => {
                            checkbox.checked = this.checked;
                            checkbox.style.backgroundColor = this.checked ? '#ec008c' : 'white';

                            // Obtener la fila y actualizar la selección
                            const row = checkbox.closest('tr');
                            if (row) {
                                toggleUserSelection(checkbox, row);
                            }
                        });
                    });
                }

                // 12. Asegurar que toggleUserSelection funcione correctamente
                function toggleUserSelection(checkbox, row) {
                    const numberId = row.dataset.numberId;

                    if (checkbox.checked) {
                        // Obtener todos los datos necesarios de la fila
                        selectedUsers.set(numberId, {
                            type_id: row.dataset.typeId,
                            number_id: numberId,
                            full_name: row.dataset.fullName,
                            email: row.dataset.email,
                            institutional_email: row.dataset.institutionalEmail,
                            department: row.dataset.department,
                            headquarters: row.dataset.headquarters,
                            program: row.dataset.program,
                            mode: row.dataset.mode,
                            level: row.dataset.level,
                            schedule: row.dataset.schedule,
                            password: numberId
                        });
                    } else {
                        selectedUsers.delete(numberId);
                    }

                    // Actualizar la lista y el contador
                    updateSelectedUsersList();
                    updateSelectedCount();
                }

                // 13. Función mejorada para actualizar la lista de usuarios en el offcanvas
                function updateSelectedUsersList() {
                    const container = document.getElementById('selectedUsersContainer');
                    if (!container) return;

                    container.innerHTML = '';
                    let index = 1;

                    selectedUsers.forEach((userData, numberId) => {
                        const userCard = document.createElement('div');
                        userCard.className = 'card mb-2';
                        userCard.innerHTML = `
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="badge bg-magenta-dark">${index++}</span>
                                        <strong class="ms-2">${userData.full_name}</strong>
                                        <br>
                                        <small class="text-muted">
                                            <i class="bi bi-person-vcard"></i> ${userData.number_id}
                                            <br>
                                            <i class="bi bi-envelope"></i> ${userData.email}
                                        </small>
                                    </div>
                                    <div class="text-end">
                                        <button class="btn btn-sm border-0" type="button" disabled>
                                            <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
                                            <span role="status">En espera</span>
                                        </button>
                                        <button class="btn btn-outline-danger btn-sm remove-selected" data-number-id="${numberId}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `;

                        // Agregar evento al botón de eliminar
                        const removeButton = userCard.querySelector('.remove-selected');
                        removeButton.addEventListener('click', function() {
                            removeSelectedUser(numberId);
                        });

                        container.appendChild(userCard);
                    });
                }

                // 14. Función para eliminar usuario seleccionado desde el panel
                function removeSelectedUser(numberId) {
                    // Desmarcar el checkbox en la tabla
                    const checkbox = document.querySelector(`.student-checkbox[data-student-id="${numberId}"]`);
                    if (checkbox) {
                        checkbox.checked = false;
                        checkbox.style.backgroundColor = 'white';
                    }

                    // Eliminar del Map de usuarios seleccionados
                    selectedUsers.delete(numberId);

                    // Actualizar la lista y el contador
                    updateSelectedUsersList();
                    updateSelectedCount();

                    // Actualizar estado del checkbox "Seleccionar todos"
                    updateSelectAllCheckboxState();
                }

                // 15. Agregar listener para los checkbox individuales en filas de la tabla
                document.addEventListener('click', function(e) {
                    if (e.target.classList.contains('student-checkbox')) {
                        const row = e.target.closest('tr');
                        if (row) {
                            toggleUserSelection(e.target, row);
                            e.target.style.backgroundColor = e.target.checked ? '#ec008c' : 'white';
                        }

                        // Actualizar estado del checkbox "Seleccionar todos"
                        updateSelectAllCheckboxState();
                    }
                });

                // 16. Modificar la función updateSelectedCount
                function updateSelectedCount() {
                    const selectedCountElement = document.getElementById('selectedCount');
                    const floatingSelectedCountElement = document.getElementById('floatingSelectedCount');
                    const selectedCount = selectedUsers.size;

                    if (selectedCountElement) {
                        selectedCountElement.textContent = selectedCount;
                    }
                    if (floatingSelectedCountElement) {
                        floatingSelectedCountElement.textContent = selectedCount;
                    }
                }

                // 17. Modificar la función updateSelectAllCheckboxState
                function updateSelectAllCheckboxState() {
                    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
                    if (!selectAllCheckbox) return;

                    const visibleCheckboxes = Array.from(document.querySelectorAll('.student-checkbox'))
                        .filter(checkbox => checkbox.closest('tr').style.display !== 'none');

                    const allChecked = visibleCheckboxes.every(checkbox => checkbox.checked);
                    const someChecked = visibleCheckboxes.some(checkbox => checkbox.checked);

                    selectAllCheckbox.checked = allChecked;
                    selectAllCheckbox.indeterminate = !allChecked && someChecked;
                }

                // 18. Inicialización inicial
                updateVisibleRowsCount();

                // Modificar el evento del botón de matriculación
                document.getElementById('enrollSelectedUsers').addEventListener('click', function() {
                    if (selectedUsers.size === 0) { // Se intenta acceder a selectedUsers aquí
                        Swal.fire('Error', 'No hay usuarios seleccionados', 'error');
                        return;
                    }

                    try {
                        // LEER DINÁMICAMENTE el estado del switch para cada usuario
                        const usersToEnroll = [];

                        selectedUsers.forEach((userData, numberId) => {
                            console.log('Procesando usuario:', userData); // Debug

                            const row = document.querySelector(`tr[data-number-id="${numberId}"]`);
                            if (!row) {
                                throw new Error(`No se encontraron los datos completos para el usuario con ID: ${numberId}`);
                            }

                            // Verificar que userData tenga todos los campos necesarios
                            if (!userData.type_id || !userData.number_id || !userData.full_name) {
                                console.error('Datos incompletos del usuario:', userData);
                                throw new Error(`Datos incompletos para el usuario: ${userData.full_name || numberId}`);
                            }

                            // Leer el estado ACTUAL del switch
                            const sendEmailCheckbox = row.querySelector('.send-email-checkbox');
                            const send_email = sendEmailCheckbox ? sendEmailCheckbox.checked : true;

                            // Crear formData con todos los datos necesarios
                            const formData = {
                                type_id: userData.type_id,
                                number_id: userData.number_id,
                                full_name: userData.full_name,
                                email: userData.email,
                                institutional_email: userData.institutional_email,
                                department: userData.department,
                                headquarters: userData.headquarters,
                                program: userData.program,
                                mode: userData.mode,
                                level: userData.level,
                                schedule: userData.schedule,
                                password: 'UTt@2025!',
                                send_email: send_email
                            };

                            // Agregar datos de cursos
                            const getCourseData = (prefix) => {
                                const select = document.getElementById(prefix);
                                if (!select) {
                                    console.error(`Select no encontrado: ${prefix}`);
                                    return {
                                        id: '0',
                                        name: 'No seleccionado'
                                    };
                                }
                                const option = select.options[select.selectedIndex];
                                const fullText = option.text;
                                const id = option.value;
                                const name = fullText.split(' - ').slice(1).join(' - ').trim();
                                return {
                                    id,
                                    name
                                };
                            };

                            const bootcamp = getCourseData('bootcamp');
                            const ingles = getCourseData('ingles');
                            const englishCode = getCourseData('english_code');
                            const skills = getCourseData('skills');

                            formData.id_bootcamp = bootcamp.id;
                            formData.bootcamp_name = bootcamp.name;
                            formData.id_leveling_english = ingles.id;
                            formData.leveling_english_name = ingles.name;
                            formData.id_english_code = englishCode.id;
                            formData.english_code_name = englishCode.name;
                            formData.id_skills = skills.id;
                            formData.skills_name = skills.name;

                            console.log('FormData completo:', formData); // Debug
                            usersToEnroll.push(formData);
                        });

                        confirmBulkEnrollment(usersToEnroll);
                    } catch (error) {
                        console.error('Error preparando datos:', error);
                        Swal.fire('Error', error.message, 'error');
                    }
                });
            });

            // Función para confirmar la matrícula masiva
            function confirmBulkEnrollment(usersToEnroll) {
                if (usersToEnroll.length === 0) {
                    Swal.fire('Error', 'Por favor seleccione al menos un estudiante', 'error');
                    return;
                }

                Swal.fire({
                    title: 'Confirmar matrícula',
                    text: `¿Está seguro que desea matricular a ${usersToEnroll.length} estudiantes seleccionados?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, matricular',
                    cancelButtonText: 'Cancelar'
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        try {
                            const result = await processEnrollments(usersToEnroll);

                            Swal.fire({
                                title: 'Resultado del proceso',
                                html: result.message,
                                icon: result.icon,
                                confirmButtonText: 'Entendido'
                            }).then(() => {
                                updateSelectedUsersList();
                                window.location.reload();
                            });
                        } catch (error) {
                            console.error("Error en el proceso de matrícula:", error);
                            Swal.fire({
                                title: 'Error inesperado',
                                html: `Ha ocurrido un error durante el proceso:<br>${error.message}`,
                                icon: 'error',
                                confirmButtonText: 'Entendido'
                            });
                        }
                    }
                });
            }

            // Función para procesar las matrículas
            async function processEnrollments(usersToEnroll) {
                const errors = [];
                let successes = 0;
                let processed = 0;
                let emailSuccesses = 0;

                // Iniciar SweetAlert con el contador de progreso
                Swal.fire({
                    title: 'Procesando matrículas',
                    html: `<div id="enrollmentProgress">Procesando: <b>0</b> de ${usersToEnroll.length}<br>Por favor espere...</div>`,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                for (const formData of usersToEnroll) {
                    try {
                        // Matrícula
                        const enrollResponse = await fetch('components/registerMoodle/enroll_user_multiple.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify(formData)
                        });

                        let enrollData;
                        try {
                            enrollData = await enrollResponse.json();
                        } catch (jsonError) {
                            throw new Error(`Error al procesar respuesta JSON: ${jsonError.message}\nCódigo HTTP: ${enrollResponse.status}\nEstado: ${enrollResponse.statusText}`);
                        }

                        // Verificar si la respuesta HTTP no es exitosa
                        if (!enrollResponse.ok) {
                            throw new Error(
                                `Error HTTP ${enrollResponse.status}: ${enrollResponse.statusText}\n` +
                                `Detalles: ${enrollData?.message || 'No hay detalles disponibles'}\n` +
                                `Usuario: ${formData.full_name} (${formData.number_id})`
                            );
                        }

                        processed++;

                        // Actualizar la interfaz con el progreso
                        const swalContent = document.getElementById('enrollmentProgress');
                        if (swalContent) {
                            swalContent.innerHTML = `
                                Procesando: <b>${processed}</b> de ${usersToEnroll.length}<br>
                                Éxitos: <b>${successes}</b><br>
                                Errores: <b>${errors.length}</b><br>
                                Por favor espere...
                            `;
                        }

                        // Si la matrícula fue exitosa
                        if (enrollData.success) {
                            successes++;
                            let carnetFilePath = null;
                            try {
                                // Intentar generar/obtener el carnet
                                // Llamamos a generate_carnet.php sin el parámetro 'download=1' para obtener la ruta del archivo
                                const carnetResponse = await fetch(`components/listCredentials/generate_carnet.php?generate_carnet=1&number_id=${formData.number_id}&username=${encodeURIComponent(formData.full_name)}`);
                                if (carnetResponse.ok) {
                                    const carnetData = await carnetResponse.json();
                                    if (carnetData.success && carnetData.file_path) {
                                        carnetFilePath = carnetData.file_path;
                                    } else {
                                        console.warn(`Advertencia al generar/obtener carnet para ${formData.number_id}: ${carnetData.message || 'Respuesta no exitosa o sin ruta de archivo.'}`);
                                        errors.push({
                                            student: formData.number_id,
                                            message: `Matrícula exitosa, pero advertencia al generar/obtener carnet: ${carnetData.message || 'Error desconocido en carnet'}`,
                                            type: 'carnet_warning'
                                        });
                                    }
                                } else {
                                    const errorText = await carnetResponse.text();
                                    console.error(`Error HTTP al generar/obtener carnet para ${formData.number_id}: ${carnetResponse.status} - ${errorText}`);
                                    errors.push({
                                        student: formData.number_id,
                                        message: `Matrícula exitosa, pero error HTTP (${carnetResponse.status}) al generar/obtener carnet.`,
                                        type: 'carnet_http_error'
                                    });
                                }
                            } catch (carnetError) {
                                console.error(`Error en la llamada para generar/obtener carnet para ${formData.number_id}:`, carnetError);
                                errors.push({
                                    student: formData.number_id,
                                    message: `Matrícula exitosa, pero error al procesar generación/obtención de carnet: ${carnetError.message}`,
                                    type: 'carnet_fetch_error'
                                });
                            }

                            // VERIFICAR SI DEBE ENVIAR EMAIL ANTES DE INTENTAR ENVIARLO
                            if (formData.send_email === true) {
                                try {
                                    console.log(`Enviando email a ${formData.full_name} (${formData.number_id}) - send_email: ${formData.send_email}`); // Debug
                                    const emailResponse = await sendEnrollmentEmail(formData, carnetFilePath); // Pasar la ruta del carnet
                                    if (emailResponse && emailResponse.success) {
                                        emailSuccesses++;
                                    } else {
                                        errors.push({
                                            student: formData.number_id,
                                            message: `Matrícula exitosa, pero error al enviar correo: ${emailResponse?.message || 'Error desconocido'}`,
                                            type: 'email'
                                        });
                                    }
                                } catch (emailError) {
                                    errors.push({
                                        student: formData.number_id,
                                        message: `Matrícula exitosa, pero error al enviar correo: ${emailError.message}\nDetalles: ${emailError.stack || 'No disponible'}`,
                                        type: 'email'
                                    });
                                }
                            } else {
                                // Log para confirmar que NO se envía el email
                                console.log(`NO enviando email a ${formData.full_name} (${formData.number_id}) - send_email: ${formData.send_email}`); // Debug
                            }
                        } else {
                            errors.push({
                                student: formData.number_id,
                                message: `Error en la matrícula: ${enrollData.message || 'Error desconocido'}\nDetalles: ${JSON.stringify(enrollData)}`,
                                type: 'enroll'
                            });
                        }
                    } catch (error) {
                        processed++; // Asegurarse de que processed se incremente incluso si hay un error temprano.
                        errors.push({
                            student: formData.number_id || 'Desconocido', // formData puede no estar completamente definido si el error es muy temprano
                            message: `Error: ${error.message}\nDetalles: ${error.stack || 'No disponible'}\nDatos enviados: ${JSON.stringify(formData)}`,
                            type: 'server'
                        });

                        // Actualizar progreso incluso en caso de error
                        const swalContent = document.getElementById('enrollmentProgress');
                        if (swalContent) {
                            swalContent.innerHTML = `
                                Procesando: <b>${processed}</b> de ${usersToEnroll.length}<br>
                                Éxitos: <b>${successes}</b><br>
                                Errores: <b>${errors.length}</b><br>
                                Por favor espere...
                            `;
                        }
                    }
                }

                // Generar mensaje de resumen detallado
                let message = `<h4>Resumen de matrícula</h4>`;
                message += `<p>Total procesados: <b>${processed}</b></p>`;
                message += `<p>Matrículas exitosas: <b>${successes}</b></p>`;
                message += `<p>Correos enviados: <b>${emailSuccesses}</b></p>`;
                message += `<p>Errores totales: <b>${errors.length}</b></p>`;

                if (errors.length > 0) {
                    message += '<hr><h5>Detalles de errores/advertencias:</h5>';
                    message += '<div style="max-height: 200px; overflow-y: auto; text-align: left;">';
                    errors.forEach((err, index) => {
                        message += `
                            <p><b>${index + 1}. Usuario:</b> ${err.student}</p>
                            <p><b>Tipo:</b> ${err.type}</p>
                            <p><b>Mensaje:</b> ${err.message}</p>
                            <hr>
                        `;
                    });
                    message += '</div>';
                }

                return {
                    message,
                    icon: errors.length === 0 ? 'success' : (successes > 0 ? 'warning' : 'error')
                };
            }

            // Función para obtener datos del formulario para la matrícula
            async function getFormDataFromRow(userData) {
                const getCourseData = (prefix) => {
                    const select = document.getElementById(prefix);
                    if (!select) {
                        console.error(`Select no encontrado: ${prefix}`);
                        return {
                            id: '0',
                            name: 'No seleccionado'
                        };
                    }
                    const option = select.options[select.selectedIndex];
                    const fullText = option.text;
                    const id = option.value;
                    const name = fullText.split(' - ').slice(1).join(' - ').trim();

                    return {
                        id,
                        name
                    };
                };

                const bootcamp = getCourseData('bootcamp');
                const ingles = getCourseData('ingles');
                const englishCode = getCourseData('english_code');
                const skills = getCourseData('skills');

                const formData = {
                    type_id: userData.type_id,
                    number_id: userData.number_id,
                    full_name: userData.full_name,
                    email: userData.email,
                    institutional_email: userData.institutional_email,
                    department: userData.department,
                    headquarters: userData.headquarters,
                    program: userData.program,
                    mode: userData.mode,
                    password: userData.number_id, // Usar la misma contraseña
                    id_bootcamp: bootcamp.id,
                    bootcamp_name: bootcamp.name,
                    id_leveling_english: ingles.id,
                    leveling_english_name: ingles.name,
                    id_english_code: englishCode.id,
                    english_code_name: englishCode.name,
                    id_skills: skills.id,
                    skills_name: skills.name
                };

                return formData;
            }

            // Función para enviar el correo electrónico
            async function sendEnrollmentEmail(userData) {
                try {
                    const response = await fetch('components/registerMoodle/send_email.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            email: userData.email,
                            program: userData.program,
                            first_name: userData.full_name.split(' ')[0], // Toma el primer nombre
                            usuario: userData.number_id,
                            password: userData.password
                        })
                    });

                    if (!response.ok) {
                        // Intentar obtener más detalles del error si la respuesta no es JSON
                        let errorDetails = `Error HTTP: ${response.status}`;
                        try {
                            const errorData = await response.json();
                            errorDetails += ` - ${errorData.message || JSON.stringify(errorData)}`;
                        } catch (e) {
                            errorDetails += ` - ${await response.text()}`;
                        }
                        throw new Error(errorDetails);
                    }

                    const data = await response.json();
                    return data;
                } catch (error) {
                    console.error('Error enviando email:', error);
                    return {
                        success: false,
                        message: 'Error enviando el correo electrónico: ' + error.message
                    };
                }
            }
        </script>
        </body>

        </html>

```

### Ejemplo de cuerpo de Email


```php

<?php
// Asegurarse de que no haya salida antes
ob_start();
session_start(); // Asegúrate de que la sesión esté iniciada si generate_carnet.php la necesita indirectamente o para consistencia.

require __DIR__ . '/../../conexion.php';
require __DIR__ . '../../../vendor/autoload.php';

require __DIR__ . '../../../vendor/phpmailer/phpmailer/src/PHPMailer.php';
require __DIR__ . '../../../vendor/phpmailer/phpmailer/src/SMTP.php';
require __DIR__ . '../../../vendor/phpmailer/phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Establecer headers antes de cualquier salida
header('Content-Type: application/json');

// Capturar errores PHP
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    // No lanzar excepción para notices o warnings que no sean críticos para el JSON
    if (!(error_reporting() & $errno)) {
        return false;
    }
    // Para otros errores, sí lanzar excepción
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});

try {
    $query = "SELECT * FROM smtpConfig WHERE id=3"; 

    $querySMTPResult = mysqli_query($conn, $query);
    if (!$querySMTPResult) {
        throw new Exception('Error en la consulta de configuración SMTP: ' . mysqli_error($conn));
    }
    
    $smtpConfig = mysqli_fetch_array($querySMTPResult);
    if (!$smtpConfig) {
        throw new Exception('No se encontró la configuración SMTP.');
    }

    $host = $smtpConfig['host'];
    $emailSmtp = $smtpConfig['email'];
    $passwordSmtp = $smtpConfig['password']; // Renombrado para evitar confusión con la contraseña del usuario
    $port = $smtpConfig['port'];

    $data = json_decode(file_get_contents('php://input'), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Error decodificando JSON: ' . json_last_error_msg());
    }

    // Validar campos requeridos
    $required = ['email', 'program', 'first_name', 'usuario', 'password'];
    $missing = [];
    foreach ($required as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            $missing[] = $field;
        }
    }

    if (!empty($missing)) {
        throw new Exception('Campos faltantes: ' . implode(', ', $missing));
    }

    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host = $host; 
    $mail->SMTPAuth = true; 
    $mail->Username = $emailSmtp; 
    $mail->Password = $passwordSmtp; 
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
    $mail->Port = $port; 

    // Desactivar verificación de certificado (igual que en multipleEmail)
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );

    $mail->setFrom('noreply@cenditech.com.co', 'Servicio al cliente'); 
    $mail->CharSet = 'UTF-8';  
    $mail->addAddress($data['email']); 
    $mail->isHTML(true);
    $mail->Subject = '¡Bienvenido al Bootcamp de ' . htmlspecialchars($data['program']) . ' de CENDI Tech!';

    // Adjuntar carnet si se proporciona la ruta y el archivo existe
    $carnetAttached = false;
    if (isset($data['carnet_file_path']) && !empty($data['carnet_file_path'])) {
        $carnetPath = $data['carnet_file_path'];
        if (file_exists($carnetPath)) {
            $mail->addAttachment($carnetPath);
            $carnetAttached = true;
        } else {
            error_log("Advertencia: Archivo de carnet no encontrado para adjuntar: " . $carnetPath . " para usuario " . $data['email']);
        }
    }

    // Mensaje adicional sobre el carnet si está adjunto
    $carnetMessage = "";
    if ($carnetAttached) {
        $carnetMessage = "
                <div style='background: #f0f0f0; color: #333; padding: 15px; border-radius: 5px; margin: 15px 0; border-left: 4px solid #066aab;'>
                    <h4 style='margin: 0 0 10px 0;'>Carnet digital adjunto</h4>
                    <p style='margin: 0; line-height: 1.4;'>
                        Descárgalo y guárdalo en tu dispositivo.
                    </p>
                    <p style='margin: 10px 0 0 0; font-size: 14px;'>
                        Si tu modalidad es presencial, debes presentar el carnet (impreso o digital) en cada ingreso.
                    </p>
                </div>";
    }

    // Mensaje HTML modificado para incluir el mensaje del carnet
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
                background: #066aab;
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
                color: #000000;
            }
            .content p {
                margin: 10px 0;
                color: #000000;
            }
            a.button {
                display: inline-block;
                margin: 20px 0;
                padding: 10px 20px;
                background: #066aab;
                color: #fff;
                text-decoration: none;
                font-weight: bold;
                border-radius: 5px;
                text-align: center;
            }
            a.button:hover, 
            a.button:visited {
                color: #fff;
                text-decoration: none;
            }
            .footer {
                text-align: center;
                margin-top: 20px;
                color: #777;
                font-size: 12px;
            }
        </style>
    </head>

    <body>
        <div class='container'>
            <div class='header'>
                <h1>¡Bienvenido a CENDI Tech!</h1>
            </div>
            <div class='content'>
                <p>Hola <b>" . htmlspecialchars($data['first_name']) . "</b>,</p>
                <p>¡Estás un paso más cerca de alcanzar tus metas! 🎉</p>
                <p>Queremos contarte que has sido admitido como beneficiario del programa <b>CENDI Tech</b> como campista.</p>
                
                <h3>Acceso a la plataforma</h3>
                <p>A continuación, encontrarás tus credenciales para acceder a nuestra plataforma de formación:</p>
                <p><b>Usuario:</b> Tu número de cédula (" . htmlspecialchars($data['usuario']) . ")</p>
                <p><b>Contraseña:</b> <code>Cendi@2026</code></p>
                <p>Puedes iniciar sesión haciendo clic en el siguiente botón:</p>
                <a class='button' href='https://talento-tech.uttalento.co/login/index.php' target='_blank'>Acceder a la Plataforma</a>
                
                <p>O también puedes acceder manualmente copiando y pegando el siguiente enlace en tu navegador:</p>
                <p><b>🔗 <a href='https://talento-tech.uttalento.co/login/index.php' target='_blank'>https://talento-tech.uttalento.co/login/index.php</a></b></p>

                    " . $carnetMessage . "

                <p>Esperamos que este camino te acerque a tus objetivos y cuentes con nosotros hasta el final. Este es solo un paso más hacia la realización de tus sueños. 🚀</p>

                <p>Si tienes dudas o inquietudes, puedes comunicarte con nuestro equipo de soporte a través de:</p>
                <p>📞 <b>3125410929</b></p>
                <p>📧 <b><a href='mailto:servicioalcliente.ut2@cendi.edu.co'>servicioalcliente.ut2@cendi.edu.co</a></b></p>
            </div>

            <div class='footer'>
                <p>Equipo CENDI Tech</p>
            </div>
        </div>
    </body>
    </html>";

    $mail->Body = $mensaje;

    // Enviar el correo
    $mail->send();
    
    // Limpiar cualquier salida anterior
    ob_get_clean(); // Limpiar buffer antes de enviar JSON
    
    echo json_encode([
        'success' => true,
        'message' => 'Correo enviado exitosamente'
    ]);

} catch (Exception $e) {
    ob_get_clean(); // Limpiar buffer en caso de error general
    http_response_code(500); // Es buena práctica establecer un código de error HTTP
    echo json_encode([
        'success' => false,
        'message' => 'Error general en el proceso de envío de correo: ' . $e->getMessage() . ' (Línea: ' . $e->getLine() . ' Archivo: ' . $e->getFile() . ')'
    ]);
}

// Asegurarse de que no haya más salida
exit();


```