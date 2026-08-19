<?php
function getUser($conn, $cedula) {
    $sql = "SELECT * FROM `usuarios` WHERE cedula = '$cedula';";
    return mysqli_query($conn, $sql);
}

function insertUser($conn, $cedula, $primerNombre, $segundoNombre, $primerApellido, $segundoApellido, $correo, $idForm) {
    $sql = "INSERT INTO `usuarios` (cedula, primer_nombre, segundo_nombre, primer_apellido, segundo_apellido, correo, id_formulario, nivel)
            VALUES ('$cedula', '$primerNombre', '$segundoNombre', '$primerApellido', '$segundoApellido', '$correo', '$idForm', 0)";
    return mysqli_query($conn, $sql);
}

function updateUserLevel($conn, $cedula, $nivel) {
    $sql = "UPDATE usuarios SET nivel = '$nivel' WHERE cedula = '$cedula'";
    return mysqli_query($conn, $sql);
}

function insertAnswer($conn, $idUsuario, $key, $value) {
    $sql = "INSERT INTO respuestas (id_usuario, id_pregunta, respuesta) VALUES ($idUsuario, $key, '$value')";
    return mysqli_query($conn, $sql);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $cedula = $_POST['cedula'];
    $idForm = $_POST['idForm'];

    // Asignación del nombre de la página basado en el formulario
    $pageNames = [
        1 => "preKnowPrograming",
        2 => "preKnowCybersecurity",
        3 => "preKnBlockchain.php",
        4 => "preKnowIntelligence",
        5 => "preKnowArchitecture",
        6 => "preKnowAnalysis"
    ];
    $namePage = $pageNames[$idForm] ?? "default";

    // Verificar si el usuario ya existe
    $queryCedula = getUser($conn, $cedula);
    if ($queryCedula->num_rows == 0) {
        $primerNombre = $_POST['primerNombre'];
        $segundoNombre = $_POST['segundoNombre'];
        $primerApellido = $_POST['primerApellido'];
        $segundoApellido = $_POST['segundoApellido'];
        $correo = $_POST['email'];

        // Insertar nuevo usuario
        insertUser($conn, $cedula, $primerNombre, $segundoNombre, $primerApellido, $segundoApellido, $correo, $idForm);

        // Obtener el ID del usuario recién creado
        $idUsuario = mysqli_fetch_assoc(getUser($conn, $cedula))["id"];
        
        $nivel = 0;
        $i = 1;

        // Procesar respuestas
        foreach ($_POST as $key => $value) {
            if ($i > 7) { // Ignorar los primeros 7 campos
                $sqlRespuesta = "SELECT respuesta_correcta FROM preguntas WHERE id = $key";
                $queryRespuesta = mysqli_query($conn, $sqlRespuesta);
                $respuesta = mysqli_fetch_assoc($queryRespuesta)["respuesta_correcta"];
                if (strpos($respuesta, $value) !== false) {
                    $nivel++;
                }
                insertAnswer($conn, $idUsuario, $key, $value);
            }
            $i++;
        }

        // Actualizar nivel del usuario
        if (updateUserLevel($conn, $cedula, $nivel)) {
            header("Location: {$namePage}.php?status=success");
        } else {
            header("Location: {$namePage}.php?status=error");
        }
        exit();
    } else {
        header("Location: {$namePage}.php?status=exist");
        exit();
    }
    mysqli_close($conn);
}
?>
