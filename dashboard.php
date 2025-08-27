<?php
require_once __DIR__ . '/includes/auth.php'; //requiere los datos de usuarios 

// Iniciar sesión si no está activa
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!is_logged_in()) {
    header('Location: index.php?error=no_auth');
    exit;
}



try {
    global $conexion;
    if (isset($conexion)) {
        $mostrar_estadisticas = true;
        
        // Obtener total de reportes
        $filtro = tiene_acceso('admin') ? "" : " WHERE usuario_id = " . $_SESSION['user_id'];
        $total_reportes = $conexion->query("SELECT COUNT(*) FROM reportes" . $filtro)->fetchColumn();
        
        // Obtener mantenimientos preventivos
        $sql_preventivos = "SELECT COUNT(*) FROM reportes WHERE tipo_mantenimiento = 'preventivo'";
        if (!tiene_acceso('admin')) {
            $sql_preventivos .= " AND usuario_id = " . $_SESSION['user_id'];
        }
        $preventivos = $conexion->query($sql_preventivos)->fetchColumn();
        
        // Obtener mantenimientos correctivos
        $sql_correctivos = "SELECT COUNT(*) FROM reportes WHERE tipo_mantenimiento = 'correctivo'";
        if (!tiene_acceso('admin')) {
            $sql_correctivos .= " AND usuario_id = " . $_SESSION['user_id'];
        }
        $correctivos = $conexion->query($sql_correctivos)->fetchColumn();
    }
} catch (Exception $e) {
    $mostrar_estadisticas = false;
    error_log("Error obteniendo estadísticas: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control - Sistema de Mantenimiento</title>
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Estilos adicionales */
        .btn-pdf {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s;
            margin: 0 5px;
        }
        .btn-pdf:hover {
            background-color: #c0392b;
        }
        .image-preview {
            border: 2px dashed #ddd;
            margin-top: 5px;
            min-height: 80px;
            position: relative;
        }
        .img-thumbnail {
            max-width: 100%;
            max-height: 100px;
        }
        .btn-remove-image {
            position: absolute;
            top: 5px;
            right: 5px;
            background: rgba(0,0,0,0.5);
            color: white;
            border: none;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            cursor: pointer;
        }
        
        /* Estilos para las tarjetas de resumen */
        .summary-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .summary-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .summary-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }
        
        .summary-card h3 {
            margin-top: 0;
            color: #2c3e50;
            font-size: 16px;
            margin-bottom: 10px;
        }
        
        .summary-card .value {
            font-size: 32px;
            font-weight: bold;
            display: block;
            margin: 15px 0;
            color: #2c3e50;
        }
        
        .summary-card.primary { 
            border-top: 4px solid #3498db;
            background: linear-gradient(135deg, #ffffff 0%, #eaf2f8 100%);
        }
        
        .summary-card.warning { 
            border-top: 4px solid #f39c12;
            background: linear-gradient(135deg, #ffffff 0%, #fef9e7 100%);
        }
        
        .summary-card.success { 
            border-top: 4px solid #2ecc71;
            background: linear-gradient(135deg, #ffffff 0%, #eafaf1 100%);
        }
        
        .summary-card.info { 
            border-top: 4px solid #9b59b6;
            background: linear-gradient(135deg, #ffffff 0%, #f4ecf7 100%);
        }
        
        .card-icon {
            font-size: 28px;
            margin-bottom: 15px;
            display: block;
        }
        
        .primary .card-icon { color: #3498db; }
        .warning .card-icon { color: #f39c12; }
        .success .card-icon { color: #2ecc71; }
        .info .card-icon { color: #9b59b6; }
        
        .summary-card p {
            margin: 0;
            color: #7f8c8d;
            font-size: 14px;
        }
        
        /* Mejoras para el formulario */
        .form-container {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        
        .form-section {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #ecf0f1;
        }
        
        .form-section:last-child {
            border-bottom: none;
        }
        
        .form-section h3 {
            color: #2c3e50;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #3498db;
            display: inline-block;
        }
        
        /* Alertas mejoradas */
        .alert-success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border: 1px solid #c3e6cb;
            border-radius: 4px;
            margin: 20px;
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
            max-width: 400px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
            margin: 20px;
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
            max-width: 400px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        /* Mejoras responsive */
        @media (max-width: 768px) {
            .summary-cards {
                grid-template-columns: 1fr;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Barra de navegación  -->
    <nav class="navbar">
        <div class="brand">Sistema de Mantenimiento Médico</div>
        <div class="user-info">
            <span><?= htmlspecialchars($_SESSION['user_name'] ?? $_SESSION['username']) ?></span>
            <span class="user-role">(<?= $_SESSION['user_role'] ?? 'usuario' ?>)</span>
            <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['user_name'] ?? $_SESSION['username']) ?>&background=3498db&color=fff" alt="Avatar">
        </div>
    </nav>

    <!-- Contenedor principal -->
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <ul class="menu">
                <li><a href="dashboard.php" class="active"><i class="fas fa-home"></i> Inicio</a></li>
                <li><a href="reportes.php"><i class="fas fa-file-alt"></i> Reportes Generados</a></li>
                <?php if (tiene_acceso('admin')): ?>
                <li><a href="usuarios.php"><i class="fas fa-users"></i> Usuarios</a></li>
                <?php endif; ?>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
            </ul>
        </aside>

        <!-- Contenido principal -->
        <main class="main-content">
            <section class="welcome-section">
                <h1>Bienvenido, <?= htmlspecialchars($_SESSION['user_name'] ?? $_SESSION['username']) ?></h1>
                <p>Sistema de registro fotográfico para mantenimiento de equipos médicos.</p>
            </section>

           

            <!-- Formulario de Servicio de Mantenimiento -->
            <div class="form-container">
                <h2><i class="fas fa-tools"></i> Nuevo Servicio de Mantenimiento</h2>
                
                <form id="servicio-form" action="procesar-servicio.php" method="POST" enctype="multipart/form-data">
                    <!-- Sección 1: Datos del Equipo -->
                    <div class="form-section">
                        <h3>Datos del Equipo</h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="contrato">Número de Contrato:</label>
                                <input type="text" id="contrato" name="contrato" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="unidad_medica">Unidad Médica:</label>
                                <input type="text" id="unidad_medica" name="unidad_medica" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="equipo">Equipo Médico:</label>
                                <select id="equipo" name="equipo" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="Monitor de Signos">Monitor de Signos Vitales</option>
                                    <option value="Ventilador">Ventilador Mecánico</option>
                                    <option value="Electrocardiógrafo">Electrocardiógrafo</option>
                                    <option value="Bomba de Infusión">Bomba de Infusión</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="modelo">Modelo:</label>
                                <input type="text" id="modelo" name="modelo" required>
                            </div>

                            <div class="form-group">
                                <label for="clasificacion">Clasificación EM:</label>
                                <select id="clasificacion" name="clasificacion" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="DX">DX (Diagnóstico)</option>
                                    <option value="REHA">REHA (Rehabilitación)</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="ubicacion">Ubicación:</label>
                                <input type="text" id="ubicacion" name="ubicacion" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="nivel">Nivel de Prioridad:</label>
                                <select id="nivel" name="nivel" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="bajo">Bajo</option>
                                    <option value="medio">Medio</option>
                                    <option value="alto">Alto</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Sección 2: Datos Técnicos -->
                    <div class="form-section">
                        <h3>Datos Técnicos</h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="serie">Número de Serie:</label>
                                <input type="text" id="serie" name="serie" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="inventario">Número de Inventario:</label>
                                <input type="text" id="inventario" name="inventario" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="tipo_mantenimiento">Tipo de Mantenimiento:</label>
                                <select id="tipo_mantenimiento" name="tipo_mantenimiento" required>
                                    <option value="preventivo">Preventivo</option>
                                    <option value="correctivo">Correctivo</option>
                                    <option value="predictivo">Predictivo</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="estado">Estado del Equipo:</label>
                                <select id="estado" name="estado" required>
                                    <option value="funcionando">Funcionando Correctamente</option>
                                    <option value="parcial">Funcionamiento Parcial</option>
                                    <option value="no_funciona">No Funciona</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Sección 3: Memoria Fotográfica -->
                    <div class="form-section">
                        <h3>Memoria Fotográfica (8 imágenes requeridas)</h3>
                        <div class="image-upload-grid">
                            <?php
                            $imageCategories = [
                                'etiqueta' => 'Etiqueta con datos del equipo',
                                'estado_inicial' => 'Estado inicial antes del mantenimiento',
                                'areas_criticas' => 'Áreas críticas para el funcionamiento',
                                'desmontaje' => 'Proceso de desmontaje y revisión interna',
                                'componentes' => 'Piezas dañadas/desgastadas/reemplazadas',
                                'reparaciones' => 'Aspecto después de reparaciones',
                                'funcionamiento' => 'Equipo reensamblado en funcionamiento',
                                'etiqueta_servicio' => 'Etiqueta de servicio'
                            ];
                            
                            foreach ($imageCategories as $key => $label): ?>
                            <div class="image-upload-group">
                                <label for="<?= $key ?>"><?= $label ?>:</label>
                                <input type="file" id="<?= $key ?>" name="<?= $key ?>" accept="image/*" required>
                                <div class="image-preview" id="preview-<?= $key ?>"></div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- Sección 4: Observaciones -->
                    <div class="form-section">
                        <h3>Observaciones y Comentarios</h3>
                        <div class="form-group">
                            <label for="observaciones">Detalles del servicio realizado:</label>
                            <textarea id="observaciones" name="observaciones" rows="4" required></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="tecnico">ING Responsable:</label>
                            <textarea id="tecnico" name="tecnico" rows="1" required></textarea>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" name="accion" value="guardar" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar Servicio
                        </button>
                        <button type="submit" name="accion" value="generar_pdf" class="btn btn-pdf">
                            <i class="fas fa-file-pdf"></i> Generar PDF
                        </button>
                        <button type="reset" class="btn btn-secondary">
                            <i class="fas fa-eraser"></i> Limpiar
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
  <!--alertas de reporte y guardado-->
    <?php if (isset($_GET['success'])): ?>
    <div class="alert-success">
        <i class="fas fa-check-circle"></i> 
        <?php if (isset($_GET['id'])): ?>
            Reporte guardado correctamente (ID: <?= $_GET['id'] ?>)
        <?php else: ?>
            Reporte guardado correctamente
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
    <div class="alert-error">
        <i class="fas fa-exclamation-circle"></i> 
        Error al guardar el reporte. Por favor, intente nuevamente.
    </div>
    <?php endif; ?>

    <!-- JavaScript para el formulario -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Previsualización de imágenes
        document.querySelectorAll('input[type="file"]').forEach(input => {
            input.addEventListener('change', function() {
                const previewId = 'preview-' + this.name;
                const preview = document.getElementById(previewId);
                preview.innerHTML = '';
                
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.classList.add('img-thumbnail');
                        preview.appendChild(img);
                        
                        const removeBtn = document.createElement('button');
                        removeBtn.innerHTML = '<i class="fas fa-times"></i>';
                        removeBtn.classList.add('btn-remove-image');
                        removeBtn.addEventListener('click', (e) => {
                            e.preventDefault();
                            preview.innerHTML = '';
                            input.value = '';
                        });
                        preview.appendChild(removeBtn);
                    }
                    reader.readAsDataURL(this.files[0]);
                }
            });
        });
        
        // Validación del formulario
        document.getElementById('servicio-form').addEventListener('submit', function(e) {
            let valid = true;
            const requiredFields = this.querySelectorAll('[required]');
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.style.borderColor = 'red';
                    valid = false;
                    setTimeout(() => field.style.borderColor = '', 2000);
                }
                
                if (field.type === 'file' && (!field.files || field.files.length === 0)) {
                    const preview = document.getElementById('preview-' + field.name);
                    if (preview) preview.style.border = '2px solid red';
                    valid = false;
                }
            });
            
            if (!valid) {
                e.preventDefault();
                alert('Por favor complete todos los campos requeridos y suba todas las imágenes');
                window.scrollTo({
                    top: document.querySelector('.form-section').offsetTop - 20,
                    behavior: 'smooth'
                });
            }
        });
        
        // Ocultar mensajes de alerta después de 5 segundos
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert-success, .alert-error');
            alerts.forEach(alert => {
                alert.style.display = 'none';
            });
        }, 5000);
    });
    </script>
</body>
</html>