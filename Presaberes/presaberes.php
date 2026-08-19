<?php include './conexion.php' ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <section class="container bg-form">
        <form class="needs-validation col-10 mx-auto form" novalidate action="modelPresaberes.php" method="POST">
            <aside class="content-form">
                <h1>Evaluación de Presaberes | Ciberseguridad</h1>
                <p>Instrucciones importantes para la prueba:</p>
                <ol start="1">
                    <li><h5 style="color:red"><b>No es obligatorio alcanzar un puntaje mínimo: La prueba está diseñada únicamente para identificar tu nivel actual.</b></h4></li>
                    <li><h5 style="color:red"><b>Responde de forma honesta y sin ayuda externa: Esto garantiza que seas asignado al nivel que corresponde a tus conocimientos, evitando dificultades en el futuro.</b></h4></li>
                    <li><h5 style="color:red"><b>Confía en tus habilidades: No te preocupes por los errores, cada respuesta nos ayuda a ubicarte en el nivel adecuado para tu desarrollo.</b></h4></li>
                </ol>
            </aside>
            <aside class="col-md-12 content-form">
                <label class="form-label" for="cedula">Cédula de Ciudadanía <span style="color: red;">*</span></label>
                <input class="form-control" name="cedula" id="cedula" type="number" placeholder="Tu respuesta" required>
                <aside class="invalid-feedback">
                    Este campo es obligatorio.
                </aside>
            </aside>
            <aside class="col-md-12 content-form">
                <label class="form-label" for="primerNombre">Primer nombre <span style="color: red;">*</span></label>
                <input class="form-control" name="primerNombre" id="primerNombre" type="text" placeholder="Tu respuesta" required>
                <aside class="invalid-feedback">
                    Este campo es obligatorio.
                </aside>
            </aside>
            <aside class="col-md-12 content-form">
                <label class="form-label" for="segundoNombre">Segundo nombre <span style="color: red;">*</span></label>
                <input class="form-control" name="segundoNombre" id="segundoNombre" type="text" placeholder="Tu respuesta" required>
                <aside class="invalid-feedback">
                    Este campo es obligatorio.
                </aside>
            </aside>
            <aside class="col-md-12 content-form">
                <label class="form-label" for="primerApellido">Primer apellido <span style="color: red;">*</span></label>
                <input class="form-control" name="primerApellido" id="primerApellido" type="text" placeholder="Tu respuesta" required>
                <aside class="invalid-feedback">
                    Este campo es obligatorio.
                </aside>
            </aside>
            <aside class="col-md-12 content-form">
                <label class="form-label" for="segundoApellido">Segundo apellido <span style="color: red;">*</span></label>
                <input class="form-control" name="segundoApellido" id="segundoApellido" type="text" placeholder="Tu respuesta" required>
                <aside class="invalid-feedback">
                    Este campo es obligatorio.
                </aside>
            </aside>
            <aside class="col-md-12 content-form">
                <label class="form-label" for="email">Correo electrónico <span style="color: red;">*</span></label>
                <input class="form-control" name="email" id="email" type="email" placeholder="Tu respuesta" required>
                <aside class="invalid-feedback">
                    Este campo es obligatorio.
                </aside>
            </aside>
            <aside class="col-md-12 content-form">
                <p>Descripción de los niveles:</p>
                <h6><strong>Nivel 1. Explorador</strong></h6> (básico): Este nivel busca proporcionar y desarrollar conocimientos esenciales relacionados con habilidades digitales e inglés que servirán como la base para todo el proceso formativo. Orientada a personas que no tienen formación o experiencia previa en el campo.</p>
                <h6><strong>Nivel 2. Integrador</strong></h6> (Intermedio): Esta fase se hace una inmersión en el desarrollo práctico de casos previamente formulados para dar guía y poner en práctica los conocimientos adquiridos. Así mismo, potenciar las habilidades de poder de los participantes. Se orienten a profundizar y fortalecer las competencias de perfiles técnicos básicos (perfil junior).</p>
                <h6><strong>Nivel 3. Innovador</strong></h6> (Avanzado): En esta nivel se desarrollarán propuestas novedosas y de impacto para el contexto real, a fin de establecer enlaces con el mundo laboral de manera efectiva y oportuna, así como en asegurarse de que cumple con los requisitos y estándares de la industria. Este nivel se dirige a individuos con experiencia previa y un conocimiento sólido en el campo, brindándoles la oportunidad de desarrollar habilidades avanzadas. Se realiza una evaluación exhaustiva para garantizar que la solución sea efectiva y responda a las necesidades planteada.</p>
            </aside>
            <?php
            $sql = "SELECT * FROM preguntas ORDER BY RAND() LIMIT 11;";
            $result = mysqli_query($conn, $sql);
            while ($row = mysqli_fetch_assoc($result)) { ?>
            <aside class="col-12 content-form" id="<?php echo $row["id"] ?>">
                <p><?php echo $row["pregunta"] ?></p>
                <!-- <p>¿Cuál de los siguientes es uno de los tres pilares fundamentales de la seguridad de la información? <span style="color: red;">*</span></p> -->
                <?php 
                $alfabeto = range('a', 'z');
                $sqlOption = "SELECT * FROM opciones WHERE id_pregunta = ".$row["id"].";";
                $options = mysqli_query($conn, $sqlOption);
                $i = 0;
                while ($opt = mysqli_fetch_assoc($options)) {
                $letra = strtoupper($alfabeto[$i]);
                $i++;
                ?>
                <aside class="mb-3" id="<?php echo $row["id"] ?>">
                    <input class="form-check-input" type="radio" id="P<?php echo $row["id"].$letra ?>" name="<?php echo $row["id"] ?>" value="<?php echo $letra ?>" required>
                    <label class="form-check-label d-inline" for="P<?php echo $row["id"].$letra ?>"><?php echo $opt["opcion"] ?></label>
                </aside>
                <?php  } ?>
            </aside>
            <?php } ?>
            <!-- <aside class="col-12 content-form">
                <p>¿Qué es una amenaza en el contexto de la seguridad informática? <span style="color: red;">*</span></p>
                <aside class="mb-3 d">
                    <input class="form-check-input" type="radio" id="P2A" name="preguntaDos" value="Un dispositivo que protege la red de intrusos." required>
                    <label class="form-check-label d-inline" for="P2A">A. Un dispositivo que protege la red de intrusos.</label>
                </aside>
                <aside class="mb-3">
                    <input class="form-check-input" type="radio" id="P2B" name="preguntaDos" value="Un evento que puede dañar o comprometer un recurso." required>
                    <label class="form-check-label d-inline" for="P2B">B. Un evento que puede dañar o comprometer un recurso.</label>
                </aside>
                <aside class="mb-3">
                    <input class="form-check-input" type="radio" id="P2C" name="preguntaDos" value="Un parche de seguridad para un sistema operativo." required>
                    <label class="form-check-label d-inline" for="P2C">C. Un parche de seguridad para un sistema operativo.</label>
                </aside>
                <aside class="mb-3">
                    <input class="form-check-input" type="radio" id="P2D" name="preguntaDos" value="Una copia de seguridad de información." required>
                    <label class="form-check-label d-inline" for="P2D">D. Una copia de seguridad de información.</label>
                    <aside class="invalid-feedback">
                        Debes elegir una de las opciones.
                    </aside>
                </aside>
            </aside> -->
            <aside class="col-12 mt-2">
                <button class="btn btn-primary" type="submit">Enviar</button>
            </aside>
        </form>
    </section>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="./validate.js"></script>
    <?php
    if (isset($_GET['status'])) {
        if ($_GET['status'] == 'success') {
            echo "
            <script>
                alertSuccessfull(true,'Formulario guardado exitosamente.')
                window.history.replaceState(null, null, window.location.pathname);
            </script>";
        }
        if ($_GET['status'] == 'error') {
            echo "
            <script>
                alertSuccessfull(false,'Error al guardar el formulario. Por favor, inténtalo de nuevo.')
                window.history.replaceState(null, null, window.location.pathname);
            </script>";
        }
        if ($_GET['status'] == 'exist') {
            echo "
            <script>
                alertSuccessfull(false,'El usuario ya tiene un registro anterior.')
                window.history.replaceState(null, null, window.location.pathname);
            </script>";
        }
    }
    ?>
</body>

</html>