<?php
include './conexion.php';

function getUser($connect, $cedula){
    $sql = "SELECT * FROM `usuarios` WHERE cedula = $cedula;";
    return mysqli_query($connect, $sql);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $cedula = $_POST['cedula'];
    $queryCedula = getUser($conn, $cedula);
    if ($queryCedula->num_rows == 0) {
        $primerNombre = $_POST['primerNombre'];
        $segundoNombre = $_POST['segundoNombre'];
        $primerApellido = $_POST['primerApellido'];
        $segundoApellido = $_POST['segundoApellido'];
        $correo = $_POST['email'];
        
        $sqlUsuario = "INSERT INTO `usuarios` (cedula, primer_nombre, segundo_nombre, primer_apellido, segundo_apellido, correo, nivel) VALUES ('$cedula', '$primerNombre', '$segundoNombre', '$primerApellido', '$segundoApellido', '$correo', 0)";
        mysqli_query($conn, $sqlUsuario);

        $idUsuario = mysqli_fetch_assoc(getUser($conn, $cedula))["id"];
        
        $nivel = 0;
        $i = 1;
        foreach ($_POST as $key => $value) {
            if ($i > 6) {
                $sqlRespuesta = "SELECT respuesta_correcta FROM preguntas WHERE id = $key ";
                $queryRespuesta = mysqli_query($conn, $sqlRespuesta);
                $respuesta = mysqli_fetch_assoc($queryRespuesta)["respuesta_correcta"];
                if(strpos($respuesta, $value) !== false){
                    $nivel++;
                }
                $sqlRes = "INSERT INTO respuestas (id_usuario, id_pregunta, respuesta) VALUES ($idUsuario, $key, '$value')";
                mysqli_query($conn, $sqlRes);
            }
            $i++;
        }
        $sql = "UPDATE usuarios SET nivel = '$nivel' WHERE cedula = $cedula";
        $query = mysqli_query($conn, $sql);

        $query = mysqli_query($conn, $sql);
        if ($query) {
            header("location: presaberes.php?status=success");
            exit();
        }else {
            header("location: presaberes.php?status=error");
            exit();
        }
        mysqli_close($conn);
    } else {
        header("location: presaberes.php?status=exist");
        exit();
    }
}
