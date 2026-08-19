<?php

$message = "";
$card = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['number_id'])) {
    $number_id = mysqli_real_escape_string($conn, $_POST['number_id']);
    
    // Consulta para obtener nombre, curso y statusAdmin
    $query = "SELECT g.full_name, g.program, ur.statusAdmin FROM groups g INNER JOIN user_register ur ON g.number_id = ur.number_id WHERE g.number_id = '$number_id'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $full_name = $row['full_name'];
        $program = $row['program'];
        $statusAdmin = $row['statusAdmin'];
        
        $statusText = "";
        switch ($statusAdmin) {
            case 3:
                $statusText = "Matriculado o en formación";
                break;
            case 10:
                $statusText = "Formado, terminó la formación y está a espera del certificado";
                break;
            case 6:
                $statusText = "Certificado";
                break;
            default:
                $statusText = "Estado desconocido";
        }
        
        $card = "<div class='card mt-4'>
                    <div class='card-body'>
                        <h5 class='card-title'>$full_name</h5>
                        <p class='card-text'><strong>Curso:</strong> $program</p>
                        <p class='card-text'><strong>Estado:</strong> $statusText</p>";
        
        if ($statusAdmin == 6) {
            // Verificar si hay certificado
            $queryCert = "SELECT link FROM certificates WHERE number_id = '$number_id'";
            $resultCert = mysqli_query($conn, $queryCert);
            if (mysqli_num_rows($resultCert) > 0) {
                $rowCert = mysqli_fetch_assoc($resultCert);
                $link = $rowCert['link'];
                $card .= "<p class='card-text'>Certificado disponible.</p>
                          <a href='$link' target='_blank' class='btn btn-success me-2 mb-3'>Ver Certificado</a>
                          <p class='card-text'>Link: <a href='$link' target='_blank'>$link</a></p>
                          <button class='btn btn-secondary' onclick=\"copyToClipboard('$link')\">Copiar Link</button>";
            } else {
                $card .= "<p class='card-text'>El certificado sigue pendiente por llegar a la plataforma, pero se recomienda revisar los correos electrónicos (institucional y personal).</p>";
            }
        }
        
        $card .= "</div></div>";
    } else {
        $message = "<div class='alert alert-danger mt-3'>No se encontró información para el número de ID proporcionado.</div>";
    }
}
?>

<div class="buscador-container">
    <div class="card">
        <div class="card-header bg-indigo-dark text-white">
            <h5 class="card-title mb-0">Buscar Estado de Persona</h5>
        </div>
        <div class="card-body">
            <form method="post" action="">
                <div class="mb-3">
                    <label for="number_id" class="form-label">Número de ID:</label>
                    <input type="text" id="number_id" name="number_id" class="form-control" required>
                </div>
                <button type="submit" class="btn bg-magenta-dark text-white w-100">Buscar</button>
            </form>
        </div>
    </div>
    
    <?php if ($message) echo $message; ?>
    <?php echo $card; ?>
</div>

<style>
    :root {
        --magenta-dark: #ec008c;
        --magenta-light: rgb(240, 59, 168);
        --indigo-dark: #30336b;
        --indigo-light: rgb(69, 73, 153);
        --gray-light: #f4f6f7;
    }

    .bg-indigo-dark {
        background-color: var(--indigo-dark) !important;
        color: white !important;
    }

    .bg-indigo-dark:hover {
        background-color: var(--indigo-light) !important;
        color: var(--gray-light) !important;
    }

    .bg-magenta-dark {
        background-color: var(--magenta-dark) !important;
        color: white !important;
    }

    .bg-magenta-dark:hover {
        background-color: var(--magenta-light) !important;
        color: var(--gray-light) !important;
    }

    .buscador-container {
        max-width: 600px;
        margin: 50px auto;
    }
</style>

<script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            Swal.fire({
                icon: 'success',
                title: '¡Copiado!',
                text: 'Link copiado al portapapeles',
                confirmButtonColor: '#066aab'
            });
        }, function(err) {
            console.error('Error al copiar: ', err);
        });
    }
</script>