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
    <title>Document</title>
    <link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="node_modules/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css?v=1.9">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="icon" href="img/icono_ct.png" type="image/x-icon">
    <style>
        .btn-sede {
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .btn-sede:hover {
            transform: scale(1.12) rotate(-2deg);
            box-shadow: 0 8px 24px rgba(236, 0, 140, 0.25), 0 1.5px 8px #30336b;
            filter: brightness(1.08);
        }

        @font-face {
            font-family: 'Sparose';
            src: url('css/fonts/fonnts.com-Sparose.ttf') format('truetype');
            font-weight: normal;
            font-style: normal;
            font-display: swap;
        }

        .eagle-link {
            font-family: 'Sparose', sans-serif;
            font-size: 1em;
            color: #30336B !important;
            text-decoration: none !important;
        }
    </style>
</head>

<body>
    <img src="img/baner_inscripcion.webp" alt="banner top" class="w-100">

    <div class="container mt-4">
        <div class="row justify-content-center align-items-end">

            <?php
            // Consulta de sedes que tienen horarios disponibles en schedules
            // Convertir a la misma collación para evitar errores de comparación
            $queryAllHeadquarters = "SELECT DISTINCT hr.* 
                                    FROM headquarters_registrations hr
                                    INNER JOIN schedules_registrations sr 
                                        ON hr.name COLLATE utf8mb4_general_ci = sr.headquarters COLLATE utf8mb4_general_ci
                                    ORDER BY hr.name";
            $resultAllHeadquarters = $conn->query($queryAllHeadquarters);

            if ($resultAllHeadquarters && $resultAllHeadquarters->num_rows > 0) {
                while ($row = $resultAllHeadquarters->fetch_assoc()) {
                    $displayName = htmlspecialchars($row['name']);
                    $shortName = mb_strimwidth($displayName, 0, 20, "...");
                    // Usar la foto de la sede si existe, si no usar la imagen por defecto
                    $imgSrc = !empty($row['photo']) ? "dashboard/img/sedes/" . $row['photo'] : "img/sede_default.jpg";
                    $isVirtual = ($displayName === 'No aplica');
                    $titleText = $isVirtual ? 'Virtual' : $shortName;
                    $popoverTitle = $isVirtual ? '' : 'Modalidad presencial';
                    $modeText = htmlspecialchars($row['mode']);
            ?>
                    <div class="col-6 col-sm-4 col-md-2 col-lg-2 d-flex flex-column align-items-center mb-4">
                        <a href="preRegistroSede.php?sede=<?php echo $row['id']; ?>"
                            class="btn-sede btn p-0 rounded-circle d-flex align-items-center justify-content-center mb-2"
                            style="
                           width:150px; height:150px; border:none; overflow:hidden; position:relative;
                           box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                       "
                            data-bs-toggle="popover"
                            data-bs-trigger="hover focus"
                            data-bs-html="true"
                            data-bs-title="<?php echo $popoverTitle; ?>"
                            data-bs-content="<?php echo $displayName; ?>"
                            title="<?php echo $titleText; ?>">
                            <img src="<?php echo $imgSrc; ?>" alt="<?php echo $displayName; ?>" style="
                            width:100%; height:100%; object-fit:cover; position:absolute; top:0; left:0; z-index:1; opacity:0.7;">
                            <span style="
                            position:absolute; top:0; left:0; width:100%; height:100%;
                            z-index:2; border-radius:50%;">
                            </span>
                        </a>
                        <span class="text-center" style="color:#30336bcc; font-weight:bold;"><?php echo $modeText; ?></span>
                        <span class="text-center" style="font-weight:bold; color:#ec008c;"><?php echo $isVirtual ? 'Virtual' : $shortName; ?></span>
                    </div>
            <?php
                }
            }
            ?>
        </div>
    </div>

    <div class="text-center mt-2">
        <small class="text-muted" style="display: flex; align-items: center; justify-content: center; gap: 6px;">
            Made by
            <span style="height: 18px; display: inline-block; vertical-align: middle; margin-bottom: 12px;">
                <img src="img/eagle_indigo.svg" alt="Eagle Software" style="height: 24px; vertical-align: middle;">
            </span>
            <a href="https://www.agenciaeaglesoftware.com/" class="eagle-link">Eagle Software</a>
        </small>
    </div>

    <img src="img/footer.webp" alt="banner top" class="w-100">
    <!-- Script para mostrar el modal al cargar la página -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var myModal = new bootstrap.Modal(document.getElementById('infoModal'));
            myModal.show();
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
            popoverTriggerList.map(function(popoverTriggerEl) {
                new bootstrap.Popover(popoverTriggerEl, {
                    html: true,
                    title: popoverTriggerEl.getAttribute('data-bs-title') || ''
                });
            });
        });
    </script>

</body>
<?php include("controller/scripts.php"); ?>

</html>