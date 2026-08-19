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
