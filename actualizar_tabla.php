<?php
// actualizar_tabla.php //no afecta el dasboard
//sistema de verificacion de tablas con el xampp phpmysiqual
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/includes/config.php';

echo "<h2>Actualizando Estructura de Tabla Reportes</h2>";

// 1. Renombrar columnas existentes y añadir las faltantes
$alter_queries = [
    // Cambiar nombre de columnas existentes si es necesario
    "ALTER TABLE reportes CHANGE dav inventario VARCHAR(50)",
    "ALTER TABLE reportes CHANGE estado_bien estado VARCHAR(100)",
    
    // Añadir columnas faltantes
    "ALTER TABLE reportes ADD COLUMN clasificacion ENUM('DX', 'REHA') DEFAULT 'DX'",
    "ALTER TABLE reportes ADD COLUMN ubicacion VARCHAR(255) AFTER modelo",
    "ALTER TABLE reportes ADD COLUMN nivel_prioridad ENUM('bajo', 'medio', 'alto') DEFAULT 'medio'",
    "ALTER TABLE reportes ADD COLUMN usuario_id INT NOT NULL AFTER tecnico",
    "ALTER TABLE reportes ADD COLUMN ruta_pdf VARCHAR(255) AFTER usuario_id",
    
    // Modificar columnas para que coincidan con el formulario
    "ALTER TABLE reportes MODIFY contrato VARCHAR(100) NOT NULL",
    "ALTER TABLE reportes MODIFY unidad_medica VARCHAR(255) NOT NULL",
    "ALTER TABLE reportes MODIFY equipo VARCHAR(255) NOT NULL",
    "ALTER TABLE reportes MODIFY observaciones TEXT NOT NULL",
    "ALTER TABLE reportes MODIFY tecnico VARCHAR(255) NOT NULL"
];

foreach ($alter_queries as $query) {
    try {
        $conexion->exec($query);
        echo "<p style='color: green;'>✅ $query</p>";
    } catch (Exception $e) {
        echo "<p style='color: orange;'>⚠ $query - Error: " . $e->getMessage() . "</p>";
    }
}

// 2. Verificar estructura final
echo "<h3>Estructura final de la tabla:</h3>";
$estructura = $conexion->query("DESCRIBE reportes")->fetchAll(PDO::FETCH_ASSOC);
echo "<table border='1' cellpadding='8'>";
echo "<tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Llave</th><th>Default</th></tr>";
foreach ($estructura as $columna) {
    echo "<tr>";
    echo "<td>" . $columna['Field'] . "</td>";
    echo "<td>" . $columna['Type'] . "</td>";
    echo "<td>" . $columna['Null'] . "</td>";
    echo "<td>" . $columna['Key'] . "</td>";
    echo "<td>" . $columna['Default'] . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<hr>";
echo "<p><a href='dashboard.php'>Ir al Dashboard</a> | <a href='verificar_estructura.php'>Verificar Estructura</a></p>";
?>