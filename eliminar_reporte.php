<?php
// eliminar_reporte.php
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

// Primero obtener información del reporte para verificar permisos
try {
    $sql = "SELECT usuario_id FROM reportes WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([$reporte_id]);
    $reporte = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$reporte) {
        header('Location: reportes.php?error=report_not_found');
        exit;
    }
    
    // Verificar permisos (solo admin o el usuario que creó el reporte puede eliminarlo)
    if (!tiene_acceso('admin') && $reporte['usuario_id'] != $_SESSION['user_id']) {
        header('Location: reportes.php?error=no_permission');
        exit;
    }
    
} catch (Exception $e) {
    header('Location: reportes.php?error=db_error');
    exit;
}

// Si llegamos aquí, tiene permisos para eliminar
try {
    $sql = "DELETE FROM reportes WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([$reporte_id]);
    
    $filas_afectadas = $stmt->rowCount();
    
    if ($filas_afectadas > 0) {
        header('Location: reportes.php?success=deleted');
    } else {
        header('Location: reportes.php?error=delete_failed');
    }
    exit;
    
} catch (Exception $e) {
    header('Location: reportes.php?error=db_error');
    exit;
}
?>