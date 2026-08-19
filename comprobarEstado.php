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
<?php include("controller/head.php"); ?>

<body>
    <img src="img/banner_comprobar.png" alt="banner top" class="w-100">
    <div class="container">

        <?php include("APIS/certificateStatus/buscarEstado.php"); ?>
        <br><br><br>

    </div>

    <footer class="text-center text-lg-start text-light fixed-bottom text-white pt-1" style="font-size: 0.85rem; min-height: 40px;">
        <!-- Copyright -->
        <div class="text-center p-1" style="background-color: #30336b; line-height: 1.2;">

            <?php
            $queryCompany = mysqli_query($conn, "SELECT nombre,nit FROM company");
            while ($empresaLog = mysqli_fetch_array($queryCompany)) {
                $empresa = $empresaLog['nombre'] . '</label>';
            }
            ?>
            <b>SYGNYA</b> &copy; Copyright <?php echo date("Y"); ?> Todos los derechos de uso para <label style="color: #66cc00;"><b><?php echo $empresa ?> </b></label>|
            <a class="text-light sparose-font" href="https://agenciaeaglesoftware.com/" target="_blank" style="text-decoration: none;">Agencia Eagle Software</a>
            <a href="https://www.linkedin.com/company/89372098/admin/feed/posts/" target="_blank" class="linkFooter"><i class="bi bi-linkedin text-white"></i></a>
            <a href="https://www.instagram.com/eaglesoftwares/" target="_blank" class="linkFooter"><i class="bi bi-instagram text-white"></i></a>
            <a href="https://www.facebook.com/eaglesoftwares/" target="_blank" class="linkFooter"><i class="bi bi-facebook text-white"></i></a>

        </div>
        <!-- Copyright -->

    </footer>

    <style>
        @font-face {
            font-family: 'Sparose';
            src: url('css/fonts/fonnts.com-Sparose.ttf') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        .sparose-font {
            font-family: 'Sparose', Arial, sans-serif !important;
        }
        footer {
            font-size: 0.85rem !important;
        }
        .linkFooter i {
            font-size: 1rem !important;
        }
    </style>

</body>
<script src="node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.all.min.js"></script>

</html>