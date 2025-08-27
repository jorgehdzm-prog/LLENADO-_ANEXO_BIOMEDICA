<?php
// reportes.php - 
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/includes/auth.php';

if (!is_logged_in()) {
    header('Location: index.php?error=no_auth');
    exit;
}


// Inicializar variables
$filtro_usuario = "";
$parametros = [];

// Solo admin puede ver todos los reportes, técnicos solo los suyos
if (!tiene_acceso('admin')) {
    $filtro_usuario = " WHERE r.usuario_id = :user_id";
    $parametros[':user_id'] = $_SESSION['user_id'];
}

// Búsqueda y filtros
$filtros = [];
if (isset($_GET['busqueda']) && !empty(trim($_GET['busqueda']))) {
    $filtros[] = "(r.contrato LIKE :busqueda OR r.equipo LIKE :busqueda OR r.unidad_medica LIKE :busqueda)";
    $parametros[':busqueda'] = '%' . trim($_GET['busqueda']) . '%';
}

if (isset($_GET['tipo_mantenimiento']) && !empty($_GET['tipo_mantenimiento'])) {
    $filtros[] = "r.tipo_mantenimiento = :tipo_mantenimiento";
    $parametros[':tipo_mantenimiento'] = $_GET['tipo_mantenimiento'];
}

// Aplicar filtros adicionales
if (count($filtros) > 0) {
    $filtro_adicional = $filtro_usuario ? " AND " : " WHERE ";
    $filtro_adicional .= implode(" AND ", $filtros);
    $filtro_usuario .= $filtro_adicional;
}

// Consulta para obtener reportes
try {
    require_once __DIR__ . '/includes/config.php';
    
    $sql = "SELECT r.*, u.username as tecnico_nombre 
            FROM reportes r 
            LEFT JOIN usuarios u ON r.usuario_id = u.id" . $filtro_usuario . " 
            ORDER BY r.fecha_creacion DESC";
            
    $stmt = $conexion->prepare($sql);
    $stmt->execute($parametros);
    $reportes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    // Manejar error sin mostrar detalles al usuario
    error_log("Error en reportes.php: " . $e->getMessage());
    $reportes = [];
}
?>
<!-- Mostrar mensajes de éxito/error -->
<?php if (isset($_GET['success'])): ?>
<div class="alert-success" style="background: #d4edda; color: #155724; padding: 15px; border: 1px solid #c3e6cb; border-radius: 4px; margin-bottom: 20px;">
    <i class="fas fa-check-circle"></i> 
    <?php
    switch($_GET['success']) {
        case 'deleted':
            echo 'Reporte eliminado correctamente';
            break;
        default:
            echo 'Operación realizada con éxito';
    }
    ?>
</div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
<div class="alert-error" style="background: #f8d7da; color: #721c24; padding: 15px; border: 1px solid #f5c6cb; border-radius: 4px; margin-bottom: 20px;">
    <i class="fas fa-exclamation-circle"></i> 
    <?php
    switch($_GET['error']) {
        case 'invalid_id':
            echo 'ID de reporte inválido';
            break;
        case 'report_not_found':
            echo 'Reporte no encontrado';
            break;
        case 'no_permission':
            echo 'No tiene permisos para realizar esta acción';
            break;
        case 'db_error':
            echo 'Error de base de datos';
            break;
        case 'delete_failed':
            echo 'Error al eliminar el reporte';
            break;
        default:
            echo 'Error al realizar la operación';
    }
    ?>
</div>
<?php endif; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes Generados - Sistema de Mantenimiento</title>
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .filtros {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .filtro-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
        }
        .tabla-reportes {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .tabla-reportes th, .tabla-reportes td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .tabla-reportes th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .tabla-reportes tr:hover {
            background-color: #f9f9f9;
        }
        .acciones {
            display: flex;
            gap: 5px;
        }
        .btn-accion {
            padding: 5px 10px;
            border-radius: 4px;
            text-decoration: none;
            color: white;
            font-size: 0.9rem;
        }
        .btn-ver {
            background-color: #3498db;
        }
        .btn-descargar {
            background-color: #27ae60;
        }
        .btn-eliminar {
            background-color: #e74c3c;
        }
        .sin-reportes {
            text-align: center;
            padding: 40px;
            background-color: white;
            border-radius: 8px;
            color: #777;
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
                <li><a href="reportes.php" class="active"><i class="fas fa-file-alt"></i> Reportes Generados</a></li>
                <?php if (tiene_acceso('admin')): ?>
                <li><a href="usuarios.php"><i class="fas fa-users"></i> Usuarios</a></li>
                <?php endif; ?>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
            </ul>
        </aside>

        <!-- Contenido principal -->
        <main class="main-content">
            <section class="welcome-section">
                <h1>Reportes Generados</h1>
                <p>Gestión y visualización de todos los reportes de mantenimiento.</p>
            </section>

            <!-- Filtros de búsqueda -->
            <div class="filtros">
                <h3><i class="fas fa-filter"></i> Filtros de Búsqueda</h3>
                <form method="GET" action="reportes.php">
                    <div class="filtro-grid">
                        <div class="form-group">
                            <label for="busqueda">Buscar:</label>
                            <input type="text" id="busqueda" name="busqueda" value="<?= htmlspecialchars($_GET['busqueda'] ?? '') ?>" placeholder="Contrato, equipo o unidad médica">
                        </div>
                        <div class="form-group">
                            <label for="tipo_mantenimiento">Tipo de Mantenimiento:</label>
                            <select id="tipo_mantenimiento" name="tipo_mantenimiento">
                                <option value="">Todos</option>
                                <option value="preventivo" <?= (isset($_GET['tipo_mantenimiento']) && $_GET['tipo_mantenimiento'] == 'preventivo') ? 'selected' : '' ?>>Preventivo</option>
                                <option value="correctivo" <?= (isset($_GET['tipo_mantenimiento']) && $_GET['tipo_mantenimiento'] == 'correctivo') ? 'selected' : '' ?>>Correctivo</option>
                                <option value="predictivo" <?= (isset($_GET['tipo_mantenimiento']) && $_GET['tipo_mantenimiento'] == 'predictivo') ? 'selected' : '' ?>>Predictivo</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                        <a href="reportes.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Limpiar
                        </a>
                    </div>
                </form>
            </div>

            <!-- Lista de reportes -->
          <?php if (count($reportes) > 0): ?>
<table class="tabla-reportes">
    <thead>
        <tr>
            <th>Contrato</th>
            <th>Unidad Médica</th>
            <th>Equipo</th>
            <th>Tipo Mant.</th>
            <th>Fecha</th>
           
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($reportes as $reporte): ?>
        <tr>
            <td><?= htmlspecialchars($reporte['contrato']) ?></td>
            <td><?= htmlspecialchars($reporte['unidad_medica']) ?></td>
            <td><?= htmlspecialchars($reporte['equipo']) ?></td>
            <td><?= ucfirst($reporte['tipo_mantenimiento']) ?></td>
            <td><?= date('d/m/Y H:i', strtotime($reporte['fecha_creacion'])) ?></td>
 
            <td class="acciones">
                <a href="ver_reporte.php?id=<?= $reporte['id'] ?>" class="btn-accion btn-ver">
                    <i class="fas fa-eye"></i>
                </a>
                <?php if (!empty($reporte['ruta_pdf'])): ?>
                <a href="<?= $reporte['ruta_pdf'] ?>" target="_blank" class="btn-accion btn-descargar">
                    <i class="fas fa-download"></i>
                </a>
                <?php endif; ?>
                <?php if (tiene_acceso('admin') || $reporte['usuario_id'] == $_SESSION['user_id']): ?>
                <a href="eliminar_reporte.php?id=<?= $reporte['id'] ?>" class="btn-accion btn-eliminar" onclick="return confirm('¿Está seguro de eliminar este reporte?')">
                    <i class="fas fa-trash"></i>
                </a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
<div class="sin-reportes">
    <i class="fas fa-folder-open" style="font-size: 3rem; margin-bottom: 15px;"></i>
    <h3>No se encontraron reportes</h3>
    <p>
        <?php if (isset($_GET['busqueda']) || isset($_GET['tipo_mantenimiento'])): ?>
        No hay reportes que coincidan con los criterios de búsqueda.
        <?php else: ?>
        No hay reportes en el sistema. <a href="dashboard.php">Crea el primer reporte</a>
        <?php endif; ?>
    </p>
</div>
<?php endif; ?>
        </main>
    </div>
</body>
</html>