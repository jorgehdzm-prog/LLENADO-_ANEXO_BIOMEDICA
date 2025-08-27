<?php
// procesar-servicio.php 

// Activar logging de errores pero NO mostrar en pantalla
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Iniciar buffering de output
ob_start();

require_once __DIR__ . '/includes/auth.php';

if (!is_logged_in()) {
    ob_end_clean();
    header('Location: index.php?error=no_auth');
    exit;
}

$accion = $_POST['accion'] ?? '';

// SI ES GENERAR PDF, procesar y guardar en BD
if ($accion === 'generar_pdf') {
    try {
        // PRIMERO: Guardar en base de datos CON TU ESTRUCTURA
        require_once __DIR__ . '/includes/config.php';
        
        // Insertar reporte en base de datos - USANDO TUS CAMPOS
        $stmt = $conexion->prepare("INSERT INTO reportes 
            (contrato, unidad_medica, equipo, modelo, inventario, serie, 
             tipo_mantenimiento, estado, observaciones, tecnico, usuario_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->execute([
            $_POST['contrato'],
            $_POST['unidad_medica'],
            $_POST['equipo'],
            $_POST['modelo'],
            $_POST['inventario'] ?? '', // ESTE ES TU CAMPO "dav"
            $_POST['serie'],
            $_POST['tipo_mantenimiento'],
            $_POST['estado'], // ESTE ES TU CAMPO "estado_bien"
            $_POST['observaciones'],
            $_POST['tecnico'],
            $_SESSION['user_id']
        ]);
        
        $reporte_id = $conexion->lastInsertId();
        error_log("Reporte guardado en BD con ID: " . $reporte_id);
        
        // LUEGO: Generar el PDF
        ob_end_clean();
        require __DIR__ . '/generar_pdf.php';
        exit;
        
    } catch (Exception $e) {
        error_log("Error al guardar reporte: " . $e->getMessage());
        ob_end_clean();
        header('Location: dashboard.php?error=db_error');
        exit;
    }
}

// SI ES SOLO GUARDAR (sin generar PDF)
else if ($accion === 'guardar') {
    try {
        require_once __DIR__ . '/includes/config.php';
        
        // Insertar reporte en base de datos - USANDO TUS CAMPOS
        $stmt = $conexion->prepare("INSERT INTO reportes 
            (contrato, unidad_medica, equipo, modelo, inventario, serie, 
             tipo_mantenimiento, estado, observaciones, tecnico, usuario_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->execute([
            $_POST['contrato'],
            $_POST['unidad_medica'],
            $_POST['equipo'],
            $_POST['modelo'],
            $_POST['inventario'] ?? '', // ESTE ES TU CAMPO "dav"
            $_POST['serie'],
            $_POST['tipo_mantenimiento'],
            $_POST['estado'], // ESTE ES TU CAMPO "estado_bien"
            $_POST['observaciones'],
            $_POST['tecnico'],
            $_SESSION['user_id']
        ]);
        
        $reporte_id = $conexion->lastInsertId();
        error_log("Reporte guardado en BD con ID: " . $reporte_id);
        
        // Redireccionar con éxito
        ob_end_clean();
        header('Location: dashboard.php?success=1&id=' . $reporte_id);
        exit;
        
    } catch (Exception $e) {
        error_log("Error al guardar reporte: " . $e->getMessage());
        ob_end_clean();
        header('Location: dashboard.php?error=db_error');
        exit;
    }
}

// Si no es ninguna acción válida, redireccionar
ob_end_clean();
header('Location: dashboard.php');
exit;
// Después de guardar el reporte, guardar el PDF en el servidor
$pdf_filename = 'reporte_' . $reporte_id . '_' . date('Ymd_His') . '.pdf';
$pdf_path = __DIR__ . '/assets/pdf/' . $pdf_filename;

// Crear directorio si no existe
if (!file_exists(__DIR__ . '/assets/pdf')) {
    mkdir(__DIR__ . '/assets/pdf', 0777, true);
}

// Guardar el PDF (necesitarías modificar generar_pdf.php para que guarde el archivo)
// Actualizar la ruta del PDF en la base de datos
$conexion->prepare("UPDATE reportes SET ruta_pdf = ? WHERE id = ?")
         ->execute(['assets/pdf/' . $pdf_filename, $reporte_id]);
?>