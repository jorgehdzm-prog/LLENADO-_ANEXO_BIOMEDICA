<?php
// generar_pdf.php 
//genera el pdf

// NO permitir que nada se output antes del PDF
ob_start(); // Start buffering

require_once __DIR__ . '/includes/auth.php';

if (!is_logged_in()) {
    ob_end_clean(); // Clean buffer
    header('Location: index.php?error=no_auth');
    exit;
}

// Limpiar buffer completamente antes de cualquier operación PDF
ob_end_clean();

// Ahora incluir TCPDF
require_once __DIR__ . '/vendor/autoload.php';

// Recoger todos los datos del formulario - USANDO TU ESTRUCTURA
$datos = [
    'contrato' => $_POST['contrato'] ?? 'No especificado',
    'unidad_medica' => $_POST['unidad_medica'] ?? 'No especificada',
    'equipo' => $_POST['equipo'] ?? 'No especificado',
    'modelo' => $_POST['modelo'] ?? 'No especificado',
    'serie' => $_POST['serie'] ?? 'No especificado',
    'inventario' => $_POST['inventario'] ?? 'No especificado', // ESTE ES TU CAMPO "dav"
    'tipo_mantenimiento' => $_POST['tipo_mantenimiento'] ?? 'preventivo',
    'estado' => $_POST['estado'] ?? 'funcionando', // ESTE ES TU CAMPO "estado_bien"
    'observaciones' => $_POST['observaciones'] ?? 'Ninguna',
    'tecnico' => $_POST['tecnico'] ?? 'Anónimo'
];

try {
    // Crear nuevo PDF
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // Configuración del documento
    $pdf->SetCreator('Sistema de Mantenimiento Médico');
    $pdf->SetAuthor($datos['tecnico']);
    $pdf->SetTitle('Reporte de Mantenimiento - ' . $datos['contrato']);
    $pdf->SetHeaderData('', 0, 'Reporte de Mantenimiento', 'N° ' . $datos['contrato']);

    // Configurar márgenes
    $pdf->SetMargins(15, 25, 15);
    $pdf->SetHeaderMargin(10);
    $pdf->SetFooterMargin(10);

    // Añadir página principal
    $pdf->AddPage();

    // CONTENIDO PRINCIPAL - AJUSTADO A TUS CAMPOS
    $html = '
    <style>
        .titulo { color: #6a1b9a; font-size: 16px; font-weight: bold; }
        .subtitulo { color: #4a148c; font-size: 14px; margin-top: 10px; }
        table { border-collapse: collapse; margin: 10px 0; width: 100%; }
        th { background-color: #f2f2f2; text-align: left; padding: 8px; }
        td { padding: 8px; border: 1px solid #ddd; }
    </style>

    <h1 class="titulo" style="text-align:center;">Reporte de Mantenimiento Médico</h1>

    <h2 class="subtitulo">Datos del Equipo</h2>
    <table>
        <tr>
            <th width="30%">Número de Contrato</th>
            <td width="70%">'.htmlspecialchars($datos['contrato']).'</td>
        </tr>
        <tr>
            <th>Unidad Médica</th>
            <td>'.htmlspecialchars($datos['unidad_medica']).'</td>
        </tr>
        <tr>
            <th>Equipo Médico</th>
            <td>'.htmlspecialchars($datos['equipo']).'</td>
        </tr>
        <tr>
            <th>Modelo</th>
            <td>'.htmlspecialchars($datos['modelo']).'</td>
        </tr>
        <tr>
            <th>Número de Serie</th>
            <td>'.htmlspecialchars($datos['serie']).'</td>
        </tr>
        <tr>
            <th>Número de Inventario</th>
            <td>'.htmlspecialchars($datos['inventario']).'</td>
        </tr>
    </table>

    <h2 class="subtitulo">Datos Técnicos</h2>
    <table>
        <tr>
            <th width="30%">Tipo de Mantenimiento</th>
            <td width="70%">'.ucfirst(htmlspecialchars($datos['tipo_mantenimiento'])).'</td>
        </tr>
        <tr>
            <th>Estado del Equipo</th>
            <td>'.ucfirst(htmlspecialchars($datos['estado'])).'</td>
        </tr>
    </table>

    <h2 class="subtitulo">Observaciones</h2>
    <p>'.nl2br(htmlspecialchars($datos['observaciones'])).'</p>

    <h2 class="subtitulo">Responsable</h2>
    <p>Técnico: '.htmlspecialchars($datos['tecnico']).'</p>
    <p>Fecha: '.date('d/m/Y H:i:s').'</p>
    ';

    // Escribir contenido principal
    $pdf->writeHTML($html, true, false, true, false, '');

    // =============================================
    // PROCESAMIENTO DE IMÁGENES 
    // =============================================
    $imageCategories = [
        'etiqueta' => 'Etiqueta del equipo',
        'estado_inicial' => 'Estado inicial',
        'areas_criticas' => 'Áreas críticas',
        'desmontaje' => 'Proceso de desmontaje',
        'componentes' => 'Componentes',
        'reparaciones' => 'Después de reparaciones',
        'funcionamiento' => 'Equipo funcionando',
        'etiqueta_servicio' => 'Etiqueta de servicio'
    ];

    foreach ($imageCategories as $key => $label) {
        // Verificación MÁS ROBUSTA de la imagen
        $fileKey = $key;
        
        // Debug: Verificar qué hay en $_FILES
        error_log("Verificando archivo: $fileKey - Existe: " . (isset($_FILES[$fileKey]) ? 'si' : 'no'));
        
        // Verificación CORREGIDA
        if (isset($_FILES[$fileKey]) && 
            $_FILES[$fileKey]['error'] === UPLOAD_ERR_OK && 
            is_uploaded_file($_FILES[$fileKey]['tmp_name']) &&
            file_exists($_FILES[$fileKey]['tmp_name']) &&
            filesize($_FILES[$fileKey]['tmp_name']) > 0) {
            
            $imagePath = $_FILES[$fileKey]['tmp_name'];
            
            // Verificar que es una imagen válida
            $imageInfo = @getimagesize($imagePath);
            if ($imageInfo === false) {
                error_log("Archivo $fileKey no es una imagen válida");
                
                // Agregar página indicando que la imagen no es válida
                $pdf->AddPage();
                $pdf->SetFont('helvetica', 'B', 12);
                $pdf->Cell(0, 10, $label, 0, 1);
                $pdf->Ln(5);
                $pdf->SetFont('helvetica', 'I', 10);
                $pdf->Cell(0, 10, 'Error: Archivo no es una imagen válida', 0, 1, 'C');
                continue;
            }
            
            list($width, $height) = $imageInfo;
            
            // Agregar nueva página para cada imagen
            $pdf->AddPage();
            
            // Título de la sección
            $pdf->SetFont('helvetica', 'B', 12);
            $pdf->Cell(0, 10, $label, 0, 1);
            $pdf->Ln(5);
            
            // Calcular tamaño para mantener proporción (max width 180mm)
            $maxWidth = 180;
            $ratio = $width / $maxWidth;
            $newWidth = $maxWidth;
            $newHeight = $height / $ratio;
            
            // Si la altura es demasiado grande, ajustar
            if ($newHeight > 250) {
                $newHeight = 250;
                $newWidth = $width * ($newHeight / $height);
            }
            
            try {
                // Insertar imagen centrada con manejo de errores
                $pdf->Image(
                    $imagePath,
                    (210 - $newWidth) / 2, // Centrar horizontalmente (A4 width = 210mm)
                    null,
                    $newWidth,
                    $newHeight,
                    '',
                    '',
                    '',
                    false,
                    300,
                    '',
                    false,
                    false,
                    0,
                    false,
                    false,
                    true
                );
                
                // Pie de foto
                $pdf->SetFont('helvetica', 'I', 10);
                $pdf->Cell(0, 10, 'Figura: ' . $label, 0, 1, 'C');
                
            } catch (Exception $e) {
                error_log("Error al insertar imagen $fileKey: " . $e->getMessage());
                
                // Mensaje de error en el PDF
                $pdf->SetFont('helvetica', 'I', 10);
                $pdf->Cell(0, 10, 'Error al cargar la imagen: ' . $label, 0, 1, 'C');
            }
            
        } else {
            // Agregar página indicando que falta la imagen
            $pdf->AddPage();
            $pdf->SetFont('helvetica', 'B', 12);
            $pdf->Cell(0, 10, $label, 0, 1);
            $pdf->Ln(5);
            $pdf->SetFont('helvetica', 'I', 10);
            $pdf->Cell(0, 10, 'Imagen no disponible o error en la carga', 0, 1, 'C');
            
            error_log("Imagen $fileKey no disponible o con error. Error code: " . ($_FILES[$fileKey]['error'] ?? 'NO_FILE'));
        }
    }

    // Salida del PDF
    $pdf->Output('reporte_mantenimiento_'.date('Ymd_His').'.pdf', 'I');
    
} catch (Exception $e) {
    // En caso de error, redirigir con mensaje
    error_log("Error grave en generar_pdf: " . $e->getMessage());
    header('Location: dashboard.php?error=pdf_error');
    exit;
}
?>