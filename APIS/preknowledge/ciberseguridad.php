<br>
<?php include 'APIS/preknowledge/modelPresaberes.php'; ?>
<section class="container bg-form shadow p-3">
    <form id="evaluacionForm" class="needs-validation col-10 mx-auto" novalidate  method="POST">
        <aside class="content-form">
            <h1>Instrucciones importantes para la prueba:</h1>
            <ol start="1" style="color:#ff0074;">
                <li><b>No es obligatorio alcanzar un puntaje mínimo: La prueba está diseñada únicamente para identificar tu nivel actual.</b></li>
                <li><b>Responde de forma honesta y sin ayuda externa: Esto garantiza que seas asignado al nivel que corresponde a tus conocimientos, evitando dificultades en el futuro.</b></li>
                <li><b>Confía en tus habilidades: No te preocupes por los errores, cada respuesta nos ayuda a ubicarte en el nivel adecuado para tu desarrollo.</b></li>
            </ol>
        </aside>

        <?php include 'controllers/dataUser.php'; ?>

        <aside class="col-md-12 content-form" style="color:#ff0074;">
            <br>
            <p><b>Bienvenido/a al examen de evaluación inicial. Este cuestionario tiene como objetivo validar tu nivel actual de conocimientos en programación, con el fin de ubicarte en el nivel más adecuado dentro del bootcamp.</b></p>
            <p><b>Por favor, siéntete en confianza de responder con sinceridad y basándote en tus conocimientos actuales, sin recurrir a fuentes externas como páginas web o herramientas como ChatGPT. Este examen NO afectará tu participación en el programa, pero es crucial para garantizar que quedes en un nivel apropiado para que aproveches al máximo los contenidos y actividades de acuerdo a tu nivel de conocimientos.</b></p>
            <p><b>Las siguientes preguntas cubren aspectos clave de programación ¡Mucho éxito! </b></p>
        </aside>

        <input type="hidden" value="2" name="idForm"> <!-- Id del formulario -->

        <?php
        $sql = "SELECT * FROM preguntas WHERE id_formulario = 2";
        $result = mysqli_query($conn, $sql);
        while ($row = mysqli_fetch_assoc($result)) { ?>
            <aside class="col-12 content-form mb-3" id="pregunta-<?php echo $row["id"]; ?>">
                <p><?php echo $row["pregunta"]; ?></p>
                <?php
                $alfabeto = range('a', 'z');
                $sqlOption = "SELECT * FROM opciones WHERE id_pregunta = " . $row["id"] . ";";
                $options = mysqli_query($conn, $sqlOption);
                $i = 0;
                while ($opt = mysqli_fetch_assoc($options)) {
                    $letra = strtoupper($alfabeto[$i]);
                    $i++;
                ?>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" id="P<?php echo $row["id"] . $letra; ?>" name="<?php echo $row["id"]; ?>" value="<?php echo $letra; ?>" required>
                        <label class="form-check-label" for="P<?php echo $row["id"] . $letra; ?>"><?php echo $opt["opcion"]; ?></label>
                        <div class="invalid-feedback">Por favor selecciona una opción.</div>
                    </div>
                <?php } ?>
            </aside>
        <?php } ?>

        <aside class="col-12 mt-4 text-center">
            <button class="btn" style="background-color: #066aab; color:#ffffff" type="submit">Enviar respuestas</button>
            <button class="btn" style="background-color:#ff0074; color:#ffffff" type="reset">Cancelar</button>
        </aside>
    </form>
</section>
<br>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.all.min.js"></script>

<script>
    document.getElementById('evaluacionForm').addEventListener('submit', function (e) {
        e.preventDefault(); // Evita el envío inmediato del formulario
        let formIsValid = true; // Bandera para el estado del formulario

        // Validación de preguntas de opción múltiple
        const preguntas = Array.from(new Set(
            Array.from(document.querySelectorAll('input[type="radio"]')).map(input => input.name)
        ));

        preguntas.forEach(pregunta => {
            const radios = document.querySelectorAll(`input[name="${pregunta}"]`);
            const isAnswered = document.querySelector(`input[name="${pregunta}"]:checked`) !== null;

            radios.forEach(radio => {
                if (!isAnswered) {
                    radio.classList.add('is-invalid');
                    formIsValid = false;
                } else {
                    radio.classList.remove('is-invalid');
                }
            });
        });

        // Validación de campos adicionales
        const requiredFields = ['cedula', 'primerNombre', 'segundoNombre', 'primerApellido', 'segundoApellido', 'email'];
        requiredFields.forEach(field => {
            const input = document.getElementById(field);
            if (!input.value.trim()) {
                input.classList.add('is-invalid');
                formIsValid = false;
            } else {
                input.classList.remove('is-invalid');
            }
        });

        // Si el formulario no está válido, mostramos un mensaje de alerta
        if (!formIsValid) {
            Swal.fire({
                icon: 'error',
                title: '¡Faltan respuestas!',
                text: 'Por favor, responde todas las preguntas y completa todos los campos obligatorios.',
                confirmButtonText: 'Entendido'
            });
        } else {
            // Si todo está completo, mostramos mensaje y enviamos el formulario
            Swal.fire({
                icon: 'success',
                title: 'Formulario completo',
                text: '¡Gracias por responder! Enviando tus respuestas...',
                showConfirmButton: false,
                timer: 5000
            }).then(() => {
                e.target.submit(); // Envía el formulario
            });
        }
    });
</script>


<?php include 'controllers/alerts.php'; ?>
