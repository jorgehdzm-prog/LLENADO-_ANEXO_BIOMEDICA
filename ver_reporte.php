<?php
// ver_reporte.php 
// este es el voton de ver que se encuentra en la tabla de reportes con este 
// permite visualizar todos los datos guardados anteriror mente

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/config.php';

if (!is_logged_in()) {
    header('Location: index.php?error=no_auth');
    exit;
}

// Verificar que se proporcionó un ID válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: reportes.php?error=invalid_id');
    exit;
}

$reporte_id = intval($_GET['id']);

// Obtener el reporte de la base de datos
try {
    $sql = "SELECT r.*, u.username as tecnico_nombre 
            FROM reportes r 
            LEFT JOIN usuarios u ON r.usuario_id = u.id 
            WHERE r.id = ?";
    
    $stmt = $conexion->prepare($sql);
    $stmt->execute([$reporte_id]);
    $reporte = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$reporte) {
        header('Location: reportes.php?error=report_not_found');
        exit;
    }
    
    // Verificar permisos (solo admin o el usuario que creó el reporte puede verlo)
    if (!tiene_acceso('admin') && $reporte['usuario_id'] != $_SESSION['user_id']) {
        header('Location: reportes.php?error=no_permission');
        exit;
    }
    
} catch (Exception $e) {
    header('Location: reportes.php?error=db_error');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Reporte - Sistema de Mantenimiento</title>
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .reporte-container {
            background-color: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }
        .reporte-section {
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        .reporte-section h3 {
            color: #6a1b9a;
            margin-top: 0;
        }
        .reporte-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        .reporte-item {
            margin-bottom: 15px;
        }
        .reporte-item strong {
            display: block;
            color: #555;
            margin-bottom: 5px;
        }
        .reporte-item span {
            display: block;
            padding: 8px;
            background-color: #f9f9f9;
            border-radius: 4px;
            border-left: 4px solid #6a1b9a;
        }
        .acciones {
            margin-top: 20px;
            display: flex;
            gap: 10px;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s;
            gap: 8px;
            text-decoration: none;
            color: white;
        }
        .btn-primary {
            background-color: #6a1b9a;
        }
        .btn-secondary {
            background-color: #757575;
        }
        .btn-danger {
            background-color: #e74c3c;
        }
    </style>
</head>
<body>
    <!-- Barra de navegación -->
    <nav class="navbar">
        <div class="brand">Sistema de Mantenimiento Médico</div>
        <div class="user-info">
            <span><?= htmlspecialchars($_SESSION['username']) ?></span>
            <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['username']) ?>&background=3498db&color=fff" alt="Avatar">
        </div>
    </nav>

    <!-- Contenedor principal -->
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <ul class="menu">
                <li><a href="dashboard.php"><i class="fas fa-home"></i> Inicio</a></li>
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
                <h1>Detalles del Reporte</h1>
                <p>Información completa del reporte de mantenimiento.</p>
            </section>

            <div class="reporte-container">
                <!-- Datos del Equipo -->
                <div class="reporte-section">
                    <h3><i class="fas fa-desktop"></i> Datos del Equipo</h3>
                    <div class="reporte-grid">
                        <div class="reporte-item">
                            <strong>Número de Contrato</strong>
                            <span><?= htmlspecialchars($reporte['contrato']) ?></span>
                        </div>
                        <div class="reporte-item">
                            <strong>Unidad Médica</strong>
                            <span><?= htmlspecialchars($reporte['unidad_medica']) ?></span>
                        </div>
                        <div class="reporte-item">
                            <strong>Equipo Médico</strong>
                            <span><?= htmlspecialchars($reporte['equipo']) ?></span>
                        </div>
                        <div class="reporte-item">
                            <strong>Modelo</strong>
                            <span><?= htmlspecialchars($reporte['modelo']) ?></span>
                        </div>
                        <div class="reporte-item">
                            <strong>Número de Serie</strong>
                            <span><?= htmlspecialchars($reporte['serie']) ?></span>
                        </div>
                        <div class="reporte-item">
                            <strong>Número de Inventario</strong>
                            <span><?= htmlspecialchars($reporte['inventario']) ?></span>
                        </div>
                    </div>
                </div>

                <!-- Datos Técnicos -->
                <div class="reporte-section">
                    <h3><i class="fas fa-tools"></i> Datos Técnicos</h3>
                    <div class="reporte-grid">
                        <div class="reporte-item">
                            <strong>Tipo de Mantenimiento</strong>
                            <span><?= ucfirst($reporte['tipo_mantenimiento']) ?></span>
                        </div>
                        <div class="reporte-item">
                            <strong>Estado del Equipo</strong>
                            <span><?= ucfirst($reporte['estado']) ?></span>
                        </div>
                    </div>
                </div>

                <!-- Observaciones -->
                <div class="reporte-section">
                    <h3><i class="fas fa-clipboard-list"></i> Observaciones</h3>
                    <div class="reporte-item">
                        <span style="white-space: pre-wrap;"><?= htmlspecialchars($reporte['observaciones']) ?></span>
                    </div>
                </div>

                <!-- Información Adicional -->
                <div class="reporte-section">
                    <h3><i class="fas fa-info-circle"></i> Información Adicional</h3>
                    <div class="reporte-grid">
                        <div class="reporte-item">
                            <strong>Técnico Responsable</strong>
                            <span><?= htmlspecialchars($reporte['tecnico']) ?></span>
                        </div>
                        
                        <div class="reporte-item">
                            <strong>Fecha de Creación</strong>
                            <span><?= date('d/m/Y H:i', strtotime($reporte['fecha_creacion'])) ?></span>
                        </div>
                    </div>
                </div>

                <!-- Botones de Acción -->
                <div class="acciones">
                    <a href="reportes.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver a Reportes
                    </a>
                    <?php if (tiene_acceso('admin') || $reporte['usuario_id'] == $_SESSION['user_id']): ?>
                    <a href="eliminar_reporte.php?id=<?= $reporte['id'] ?>" class="btn btn-danger" onclick="return confirm('¿Está seguro de eliminar este reporte? Esta acción no se puede deshacer.')">
                        <i class="fas fa-trash"></i> Eliminar Reporte
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</body>
</html>