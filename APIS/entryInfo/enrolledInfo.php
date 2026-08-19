<?php
// Obtener el number_id desde la URL
$number_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($number_id > 0) {
    // Consulta para obtener los datos del estudiante
    $query = "SELECT 
                number_id,
                full_name,
                institutional_email,
                headquarters,
                department,
                mode,
                program
              FROM groups 
              WHERE number_id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $number_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $student = $result->fetch_assoc();
        ?>
        <div class="container mt-4">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card shadow-lg">
                        <div class="card-header text-center" style="background-color: #30336b; color: white;">
                            <h3><i class="bi bi-person-circle"></i> Información del Estudiante</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="info-item">
                                        <label class="info-label"><i class="bi bi-card-text"></i> Identificación:</label>
                                        <p class="info-value"><?php echo htmlspecialchars($student['number_id']); ?></p>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="info-item">
                                        <label class="info-label"><i class="bi bi-person"></i> Nombre Completo:</label>
                                        <p class="info-value"><?php echo htmlspecialchars(mb_strtoupper($student['full_name'], 'UTF-8')); ?></p>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="info-item">
                                        <label class="info-label"><i class="bi bi-envelope"></i> Email Institucional:</label>
                                        <p class="info-value"><?php echo htmlspecialchars($student['institutional_email']); ?></p>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="info-item">
                                        <label class="info-label"><i class="bi bi-building"></i> Sede:</label>
                                        <p class="info-value"><?php echo htmlspecialchars($student['headquarters']); ?></p>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="info-item">
                                        <label class="info-label"><i class="bi bi-geo-alt"></i> Departamento:</label>
                                        <p class="info-value"><?php echo htmlspecialchars($student['department']); ?></p>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="info-item">
                                        <label class="info-label"><i class="bi bi-laptop"></i> Modalidad:</label>
                                        <p class="info-value"><?php echo htmlspecialchars($student['mode']); ?></p>
                                    </div>
                                </div>
                                <div class="col-12 mb-3">
                                    <div class="info-item">
                                        <label class="info-label"><i class="bi bi-book"></i> Programa:</label>
                                        <p class="info-value program-highlight"><?php echo htmlspecialchars($student['program']); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-center" style="background-color: #f8f9fa;">
                            <small class="text-muted">
                                <i class="bi bi-info-circle"></i> 
                                Esta información corresponde al estudiante con identificación <?php echo htmlspecialchars($student['number_id']); ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <style>
            .info-item {
                background-color: #f8f9fa;
                padding: 15px;
                border-radius: 8px;
                border-left: 4px solid #30336b;
                height: 100%;
            }
            
            .info-label {
                font-weight: bold;
                color: #30336b;
                margin-bottom: 5px;
                display: block;
                font-size: 14px;
            }
            
            .info-value {
                margin: 0;
                color: #333;
                font-size: 16px;
                word-wrap: break-word;
            }
            
            .program-highlight {
                background: linear-gradient(135deg, #30336b, #30336b);
                color: white;
                padding: 10px;
                border-radius: 5px;
                text-align: center;
                font-weight: bold;
                font-size: 18px;
            }
            
            .card {
                border: none;
                border-radius: 15px;
            }
            
            .card-header {
                border-top-left-radius: 15px;
                border-top-right-radius: 15px;
                border-bottom: none;
            }
            
            .card-footer {
                border-bottom-left-radius: 15px;
                border-bottom-right-radius: 15px;
                border-top: 1px solid #dee2e6;
            }
            
            @media (max-width: 768px) {
                .info-value {
                    font-size: 14px;
                }
                
                .program-highlight {
                    font-size: 16px;
                }
                
                .card-header h3 {
                    font-size: 1.5rem;
                }
            }
        </style>
        <?php
    } else {
        ?>
        <div class="container mt-4">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="alert alert-warning text-center">
                        <i class="bi bi-exclamation-triangle" style="font-size: 2rem;"></i>
                        <h4 class="mt-3">Estudiante no encontrado</h4>
                        <p>No se encontró ningún estudiante con la identificación <strong><?php echo htmlspecialchars($number_id); ?></strong></p>
                        <hr>
                        <small class="text-muted">Verifique que el número de identificación sea correcto.</small>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    
    $stmt->close();
} else {
    ?>
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="alert alert-info text-center">
                    <i class="bi bi-info-circle" style="font-size: 2rem;"></i>
                    <h4 class="mt-3">Consulta de Información</h4>
                    <p>Para consultar la información de un estudiante, agregue el parámetro <code>id</code> a la URL.</p>
                    <hr>
                    <small class="text-muted">
                        Ejemplo: <code>studentInfo.php?id=1234567890</code>
                    </small>
                </div>
            </div>
        </div>
    </div>
    <?php
}
?>