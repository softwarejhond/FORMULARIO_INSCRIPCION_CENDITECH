<?php
// Obtener el username desde la URL
$username = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($username > 0) {
    // Consulta para obtener los datos del ejecutor con cruce a executor_headquarters
    $query = "SELECT 
                u.username,
                u.nombre as full_name,
                u.email,
                u.rol,
                u.telefono,
                u.genero,
                u.direccion,
                eh.headquarter
              FROM users u
              LEFT JOIN executor_headquarters eh ON u.username = eh.username
              WHERE u.username = ? AND u.rol IN (5, 7, 8)";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $executor = $result->fetch_assoc();
        
        // Determinar el tipo de rol
        $rol_names = [
            5 => 'Docente',
            7 => 'Monitor',
            8 => 'Mentor'
        ];
        
        $rol_name = $rol_names[$executor['rol']] ?? 'Ejecutor';
        $rol_icon = [
            5 => 'bi-person-workspace', // Docente
            7 => 'bi-person-check',     // Monitor
            8 => 'bi-person-heart'      // Mentor
        ];
        
        $icon = $rol_icon[$executor['rol']] ?? 'bi-person-circle';
        ?>
        <div class="container mt-4">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card shadow-lg">
                        <div class="card-header text-center" style="background: linear-gradient(135deg, #e74c3c, #c0392b); color: white;">
                            <h3><i class="bi <?php echo $icon; ?>"></i> Información del <?php echo $rol_name; ?></h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="info-item">
                                        <label class="info-label"><i class="bi bi-card-text"></i> Identificación:</label>
                                        <p class="info-value"><?php echo htmlspecialchars($executor['username']); ?></p>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="info-item">
                                        <label class="info-label"><i class="bi bi-person"></i> Nombre Completo:</label>
                                        <p class="info-value"><?php echo htmlspecialchars(mb_strtoupper($executor['full_name'], 'UTF-8')); ?></p>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="info-item">
                                        <label class="info-label"><i class="bi bi-envelope"></i> Email:</label>
                                        <p class="info-value"><?php echo htmlspecialchars($executor['email']); ?></p>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="info-item">
                                        <label class="info-label"><i class="bi bi-telephone"></i> Teléfono:</label>
                                        <p class="info-value"><?php echo htmlspecialchars($executor['telefono'] ?: 'No registrado'); ?></p>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="info-item">
                                        <label class="info-label"><i class="bi bi-gender-ambiguous"></i> Género:</label>
                                        <p class="info-value"><?php echo htmlspecialchars($executor['genero'] ?: 'No especificado'); ?></p>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="info-item">
                                        <label class="info-label"><i class="bi bi-building"></i> Sede:</label>
                                        <p class="info-value"><?php echo htmlspecialchars($executor['headquarter'] ?: 'No asignada'); ?></p>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="info-item">
                                        <label class="info-label"><i class="bi bi-geo-alt"></i> Dirección:</label>
                                        <p class="info-value"><?php echo htmlspecialchars($executor['direccion'] ?: 'No registrada'); ?></p>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="info-item role-highlight">
                                        <label class="info-label"><i class="bi bi-award"></i> Rol:</label>
                                        <p class="info-value role-value"><?php echo htmlspecialchars($rol_name); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-center" style="background-color: #f8f9fa;">
                            <small class="text-muted">
                                <i class="bi bi-info-circle"></i> 
                                Esta información corresponde al <?php echo strtolower($rol_name); ?> con identificación <?php echo htmlspecialchars($executor['username']); ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <style>
            .info-item {
                background-color: #fff5f5;
                padding: 15px;
                border-radius: 8px;
                border-left: 4px solid #e74c3c;
                height: 100%;
                transition: all 0.3s ease;
            }
            
            .info-item:hover {
                background-color: #ffebee;
                transform: translateY(-2px);
                box-shadow: 0 4px 8px rgba(231, 76, 60, 0.1);
            }
            
            .info-label {
                font-weight: bold;
                color: #c0392b;
                margin-bottom: 5px;
                display: block;
                font-size: 14px;
            }
            
            .info-value {
                margin: 0;
                color: #2c3e50;
                font-size: 16px;
                word-wrap: break-word;
            }
            
            .role-highlight {
                background: linear-gradient(135deg, #e74c3c, #c0392b) !important;
                color: white;
                border: none !important;
            }
            
            .role-highlight .info-label {
                color: #fff !important;
            }
            
            .role-value {
                color: white !important;
                font-weight: bold;
                font-size: 18px;
                text-align: center;
            }
            
            .card {
                border: none;
                border-radius: 15px;
                border: 2px solid #fadbd8;
            }
            
            .card-header {
                border-top-left-radius: 15px;
                border-top-right-radius: 15px;
                border-bottom: none;
            }
            
            .card-footer {
                border-bottom-left-radius: 15px;
                border-bottom-right-radius: 15px;
                border-top: 1px solid #fadbd8;
            }
            
            @media (max-width: 768px) {
                .info-value {
                    font-size: 14px;
                }
                
                .role-value {
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
                        <i class="bi bi-exclamation-triangle" style="font-size: 2rem; color: #e67e22;"></i>
                        <h4 class="mt-3">Ejecutor no encontrado</h4>
                        <p>No se encontró ningún ejecutor (Docente, Monitor o Mentor) con la identificación <strong><?php echo htmlspecialchars($username); ?></strong></p>
                        <hr>
                        <small class="text-muted">Verifique que el número de identificación sea correcto y que corresponda a un ejecutor activo.</small>
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
                <div class="alert alert-info text-center" style="background-color: #fdf2e9; border-color: #e67e22; color: #d35400;">
                    <i class="bi bi-info-circle" style="font-size: 2rem; color: #e67e22;"></i>
                    <h4 class="mt-3">Consulta de Información de Ejecutores</h4>
                    <p>Para consultar la información de un ejecutor (Docente, Monitor o Mentor), agregue el parámetro <code>id</code> a la URL.</p>
                    <hr style="border-color: #e67e22;">
                    <small class="text-muted">
                        Ejemplo: <code>executorInfo.php?id=1234567890</code>
                    </small>
                </div>
            </div>
        </div>
    </div>
    <?php
}
?>