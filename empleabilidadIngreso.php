<?php
// Habilitar la visualización de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>

<?php
include("controller/conexion.php"); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Empleabilidad</title>
    <link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="node_modules/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css?v=1.9">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="icon" href="img/icono_ct.png" type="image/x-icon">
</head>

<body>
    <img src="img/empleabilidad_inicio.jpg" alt="banner top" class="w-100" id="headerBanner">
    <div class="container">

        <?php include("APIS/register_student/newEmpLoteUno.php"); ?>

        <!-- Modal Informativo -->
        <div class="modal fade" id="infoModal" tabindex="-1" aria-labelledby="infoModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="infoModalLabel">FORMULARIO DE EMPLEABILIDAD - INGRESO PROYECTO: Talento TECH.</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Desde la Unión Temporal Innova Digital nos complace que seas participante de los procesos de generación de habilidades digitales bajo la modalidad de bootcamps del MINTIC.
                        <br>
                        En el marco de la estrategia de empleabilidad del proyecto Talento TECH en Bogotá, el presente formulario busca conocer tu actual perfil de empleo, con el propósito de identificar oportunidades asociadas que te faciliten conectar con el ecosistema de empleo del sector TI.
                        <br>
                        ¡Bienvenido!
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn" data-bs-dismiss="modal" style="background-color:#066aab ; color:white">Entendido</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <footer class="text-center" style="margin-top: -50px;">
        <small class="text-muted">
            <?php
            $empresa = '';
            $queryCompany = mysqli_query($conn, "SELECT nombre,nit FROM company");
            while ($empresaLog = mysqli_fetch_array($queryCompany)) {
                $empresa = $empresaLog['nombre'];
            }
            ?>
            <br>
            <b>SYGNIA</b> &copy; Copyright <?php echo date("Y"); ?> Todos los derechos de uso para
            <label style="color: #30336b;"><b><?php echo $empresa; ?></b></label> |
            <a class="text-muted" href="https://agenciaeaglesoftware.com/" target="_blank">Agencia Eagle Software</a>
        </small>
        <img src="img/footer.webp" alt="banner 'bottom'" class="w-100">
    </footer>

    <!-- Script para mostrar el modal al cargar la página -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var myModal = new bootstrap.Modal(document.getElementById('infoModal'));
            myModal.show();
        });
    </script>

</body>
<?php include("controller/scripts.php"); ?>

</html>