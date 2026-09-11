"
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
        }

        .content p {
            margin: 10px 0;
        }

        a.button {
            display: inline-block;
            margin: 20px 0;
            padding: 10px 20px;
            background: #066aab;
            color: #fff;
            /* Letra blanca */
            text-decoration: none;
            /* Sin subrayado */
            font-weight: bold;
            border-radius: 5px;
            text-align: center;
        }

        a.button:hover,
        a.button:visited {
            color: #fff;
            /* Mantener el texto blanco incluso cuando el enlace se haya visitado o esté en hover */
            text-decoration: none;
            /* Eliminar subrayado en hover */
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
            <h1>¡Bienvenido al Bootcamp de $program!</h1>
        </div>
        <div class='content'>
            <p>Hola <b>$first_name</b>,</p>
            <p>¡Felicitaciones! 🎉 Nos emociona darte la bienvenida al <b>Bootcamp de $program</b> Cenditech.</p>
            <p>Este Bootcamp es el primer paso hacia un futuro lleno de posibilidades en una de las áreas más demandadas del mercado. Aprenderás habilidades clave, trabajarás en proyectos prácticos y te prepararás para enfrentar los desafíos del mundo digital.</p>
            <h3>Próximos Pasos:</h3>
            <ol>
                <li><b>Realiza tu Prueba de Saberes:</b><br>
                    Antes de iniciar el Bootcamp, necesitamos que completes una Prueba de Saberes. Esta evaluación nos ayudará a reconocer tus conocimientos previos y asignarte al nivel que mejor se adapte a tus necesidades de aprendizaje.<br>
                    <a class='button' href='#'>Realizar Prueba de Saberes</a>
                </li>
                <li><b>Revisa tu correo:</b><br>
                    Después de la prueba, te enviaremos toda la información necesaria para comenzar tu formación: horarios, plataforma y recursos.</li>
                <li><b>Prepárate para el inicio:</b><br>
                    Asegúrate de contar con un dispositivo adecuado y una conexión estable a internet para sacar el máximo provecho del programa.</li>
            </ol>
            <p>Si tienes alguna duda o necesitas apoyo, no dudes en contactarnos a través de este correo. ¡Estamos aquí para ayudarte en cada etapa de tu formación!</p>
            <p>Gracias por confiar en nosotros y ser parte de esta gran comunidad. ¡Nos vemos pronto futuro campista! 🚀</p>
        </div>
        <div class='footer'>
            <p>Cenditech</p>
        </div>
    </div>
</body>

</html>"