<?php
// Limpiar cualquier salida previa
ob_clean();

// Configurar manejo de errores para que no interfiera con JSON
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

// Configurar header JSON antes que cualquier cosa
header('Content-Type: application/json; charset=utf-8');

try {
    require_once '../../controller/conexion.php';

    // Validar y obtener datos del POST
    $number_id = isset($_POST['number_id']) ? preg_replace('/\D/', '', $_POST['number_id']) : '';
    $fecha = isset($_POST['fecha']) ? $_POST['fecha'] : '';
    $curso = isset($_POST['curso']) ? $_POST['curso'] : '';
    $clases_nivelacion = isset($_POST['clases_equivalentes']) ? intval($_POST['clases_equivalentes']) : 0;

    // Log de debug (opcional - comentar en producción)
    error_log("POST data - number_id: $number_id, fecha: $fecha, curso: $curso, clases_nivelacion: $clases_nivelacion");

    if (!$number_id || !$fecha || !$curso) {
        echo json_encode(['success' => false, 'message' => 'Datos incompletos.']);
        exit;
    }

    // Validar formato de fecha (YYYY-MM-DD)
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
        echo json_encode(['success' => false, 'message' => 'Formato de fecha inválido.']);
        exit;
    }

    // Validar que number_id sea numérico y positivo
    if (!ctype_digit($number_id) || intval($number_id) <= 0) {
        echo json_encode(['success' => false, 'message' => 'Número de identificación inválido.']);
        exit;
    }

    // Verificar conexión a la base de datos
    if (!isset($conn) || !$conn) {
        echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos.']);
        exit;
    }

    // Convertir number_id a BIGINT para que coincida con la tabla
    $number_id_bigint = (int)$number_id;

    // Validar que el number_id existe en groups y el curso coincide con id_bootcamp
    $query_group = "SELECT id_bootcamp FROM groups WHERE number_id = ?";
    $stmt_group = $conn->prepare($query_group);
    if (!$stmt_group) {
        error_log("Error preparando query_group: " . $conn->error);
        echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta de grupo.']);
        exit;
    }
    $stmt_group->bind_param("i", $number_id_bigint);
    if (!$stmt_group->execute()) {
        error_log("Error ejecutando query_group: " . $stmt_group->error);
        echo json_encode(['success' => false, 'message' => 'Error al verificar grupo.']);
        $stmt_group->close();
        exit;
    }
    $result_group = $stmt_group->get_result();
    if ($result_group->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'El número de identificación no está registrado en el sistema.']);
        $stmt_group->close();
        exit;
    }
    $group_info = $result_group->fetch_assoc();
    if ($group_info['id_bootcamp'] != $curso) {
        echo json_encode(['success' => false, 'message' => 'El curso no corresponde al bootcamp asignado a este número de identificación.']);
        $stmt_group->close();
        exit;
    }
    $stmt_group->close();

    // Verificar si ya existe asistencia para ese usuario, curso y fecha
    $query_check = "SELECT id FROM asistencias_masterclass WHERE number_id = ? AND code = ? AND fecha = ?";
    $stmt_check = $conn->prepare($query_check);
    
    if (!$stmt_check) {
        error_log("Error preparando query_check: " . $conn->error);
        echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta de verificación.']);
        exit;
    }

    // Usar 'i' para BIGINT, 's' para VARCHAR, 's' para DATE
    $stmt_check->bind_param("iss", $number_id_bigint, $curso, $fecha);
    
    if (!$stmt_check->execute()) {
        error_log("Error ejecutando query_check: " . $stmt_check->error);
        echo json_encode(['success' => false, 'message' => 'Error al verificar asistencia existente.']);
        $stmt_check->close();
        exit;
    }

    $result_check = $stmt_check->get_result();
    
    if ($result_check === false) {
        error_log("Error obteniendo resultado query_check: " . $conn->error);
        echo json_encode(['success' => false, 'message' => 'Error al obtener resultado de verificación.']);
        $stmt_check->close();
        exit;
    }

    if ($result_check->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Ya registraste tu asistencia para esta masterclass.']);
        $stmt_check->close();
        exit;
    }
    $stmt_check->close();

    // Insertar asistencia con clases_nivelacion
    $query_insert = "INSERT INTO asistencias_masterclass (number_id, code, fecha, clases_nivelacion) VALUES (?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($query_insert);
    
    if (!$stmt_insert) {
        error_log("Error preparando query_insert: " . $conn->error);
        echo json_encode(['success' => false, 'message' => 'Error al preparar el registro de asistencia.']);
        exit;
    }

    $stmt_insert->bind_param("issi", $number_id_bigint, $curso, $fecha, $clases_nivelacion);

    if ($stmt_insert->execute()) {
        echo json_encode(['success' => true, 'message' => '¡Asistencia registrada correctamente!']);

        // --- INICIO LÓGICA DE NIVELACIÓN DE ASISTENCIAS ---
        // 1. Obtener teacher_id del curso
        $query_teacher = "SELECT teacher FROM courses WHERE code = ?";
        $stmt_teacher = $conn->prepare($query_teacher);
        $stmt_teacher->bind_param("i", $curso);
        $stmt_teacher->execute();
        $result_teacher = $stmt_teacher->get_result();
        $teacher_id = null;
        if ($row_teacher = $result_teacher->fetch_assoc()) {
            $teacher_id = $row_teacher['teacher'];
        }
        $stmt_teacher->close();

        // 2. Obtener modalidad y sede del grupo
        $query_group_info = "SELECT mode, headquarters FROM groups WHERE number_id = ?";
        $stmt_group_info = $conn->prepare($query_group_info);
        $stmt_group_info->bind_param("i", $number_id_bigint);
        $stmt_group_info->execute();
        $result_group_info = $stmt_group_info->get_result();
        $mode = '';
        $sede = '';
        if ($row_group_info = $result_group_info->fetch_assoc()) {
            $mode = $row_group_info['mode'];
            $sede = $row_group_info['headquarters'];
        }
        $stmt_group_info->close();

        // 3. Obtener las fechas de clase más antiguas del curso
        $query_dates = "SELECT class_date FROM attendance_records WHERE course_id = ? ORDER BY class_date ASC";
        $stmt_dates = $conn->prepare($query_dates);
        $stmt_dates->bind_param("i", $curso);
        $stmt_dates->execute();
        $result_dates = $stmt_dates->get_result();

        $fechas_clase = [];
        while ($row_date = $result_dates->fetch_assoc()) {
            $fechas_clase[] = $row_date['class_date'];
        }
        $stmt_dates->close();

        $cambios_realizados = 0;
        foreach ($fechas_clase as $class_date) {
            if ($cambios_realizados >= $clases_nivelacion) break;

            // Verificar si ya existe registro para el estudiante en esa fecha
            $query_check_att = "SELECT id, attendance_status FROM attendance_records WHERE student_id = ? AND course_id = ? AND modality = ? AND sede = ? AND class_date = ?";
            $stmt_check_att = $conn->prepare($query_check_att);
            $student_id_str = strval($number_id); // student_id es varchar
            $stmt_check_att->bind_param("sisss", $student_id_str, $curso, $mode, $sede, $class_date);
            $stmt_check_att->execute();
            $result_check_att = $stmt_check_att->get_result();

            if ($row_att = $result_check_att->fetch_assoc()) {
                // Si existe y está como 'ausente' o 'tarde', actualizar a 'presente'
                if ($row_att['attendance_status'] == 'ausente' || $row_att['attendance_status'] == 'tarde') {
                    $query_update = "UPDATE attendance_records SET attendance_status = 'presente' WHERE id = ?";
                    $stmt_update = $conn->prepare($query_update);
                    $stmt_update->bind_param("i", $row_att['id']);
                    $stmt_update->execute();
                    $stmt_update->close();
                    $cambios_realizados++;
                }
            } else {
                // Si no existe, crear el registro
                $query_insert_att = "INSERT INTO attendance_records (teacher_id, student_id, course_id, modality, sede, class_date, recorded_hours, attendance_status) VALUES (?, ?, ?, ?, ?, ?, 0, 'presente')";
                $stmt_insert_att = $conn->prepare($query_insert_att);
                $stmt_insert_att->bind_param("isisss", $teacher_id, $student_id_str, $curso, $mode, $sede, $class_date);
                $stmt_insert_att->execute();
                $stmt_insert_att->close();
                $cambios_realizados++;
            }
        }
        // --- FIN LÓGICA DE NIVELACIÓN DE ASISTENCIAS ---

    } else {
        error_log("Error ejecutando query_insert: " . $stmt_insert->error);
        echo json_encode(['success' => false, 'message' => 'No se pudo registrar la asistencia. Intente nuevamente.']);
    }
    $stmt_insert->close();

} catch (Exception $e) {
    error_log("Exception en procesarAsistencia.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error interno del servidor.']);
} catch (Error $e) {
    error_log("Error fatal en procesarAsistencia.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error interno del servidor.']);
} finally {
    if (isset($conn) && $conn) {
        $conn->close();
    }
}
?>