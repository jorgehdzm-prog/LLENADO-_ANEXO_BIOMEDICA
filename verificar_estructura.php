<?php
// verificar_estructura.php // no explicar
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/includes/config.php';

echo "<h2>Verificación de Estructura de Base de Datos</h2>";

// 1. Verificar tabla reportes
echo "<h3>1. Estructura de tabla 'reportes':</h3>";
try {
    $estructura = $conexion->query("DESCRIBE reportes")->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($estructura) === 0) {
        echo "<p style='color: red;'>❌ La tabla 'reportes' NO EXISTE</p>";
        
        // Crear tabla si no existe
        $sql = "CREATE TABLE reportes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            contrato VARCHAR(100) NOT NULL,
            unidad_medica VARCHAR(255) NOT NULL,
            equipo VARCHAR(255) NOT NULL,
            modelo VARCHAR(100) NOT NULL,
            serie VARCHAR(100) NOT NULL,
            inventario VARCHAR(100) NOT NULL,
            tipo_mantenimiento ENUM('preventivo', 'correctivo', 'predictivo') NOT NULL,
            estado ENUM('funcionando', 'parcial', 'no_funciona') NOT NULL,
            clasificacion ENUM('DX', 'REHA') NOT NULL,
            ubicacion VARCHAR(255) NOT NULL,
            nivel_prioridad ENUM('bajo', 'medio', 'alto') NOT NULL,
            observaciones TEXT NOT NULL,
            tecnico VARCHAR(255) NOT NULL,
            usuario_id INT NOT NULL,
            ruta_pdf VARCHAR(255),
            fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        $conexion->exec($sql);
        echo "<p style='color: green;'>✅ Tabla 'reportes' creada correctamente</p>";
        
    } else {
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
        echo "<p style='color: green;'>✅ Tabla 'reportes' existe</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

// 2. Verificar datos de ejemplo
echo "<h3>2. Datos en tabla 'reportes':</h3>";
try {
    $count = $conexion->query("SELECT COUNT(*) as total FROM reportes")->fetch()['total'];
    echo "<p>Total de reportes: <strong>$count</strong></p>";
    
    if ($count > 0) {
        $ejemplos = $conexion->query("SELECT id, contrato, equipo, fecha_creacion FROM reportes ORDER BY fecha_creacion DESC LIMIT 5")->fetchAll();
        echo "<p><strong>Últimos 5 reportes:</strong></p>";
        echo "<pre>";
        print_r($ejemplos);
        echo "</pre>";
    } else {
        echo "<p style='color: orange;'>⚠ No hay reportes en la base de datos</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

// 3. Probar inserción de datos
echo "<h3>3. Prueba de inserción de datos:</h3>";
try {
    $test_data = [
        'contrato' => 'TEST-' . time(),
        'unidad_medica' => 'Hospital de Prueba',
        'equipo' => 'Equipo de Prueba',
        'modelo' => 'Modelo Test',
        'serie' => 'SN-TEST',
        'inventario' => 'INV-TEST',
        'tipo_mantenimiento' => 'preventivo',
        'estado' => 'funcionando',
        'clasificacion' => 'DX',
        'ubicacion' => 'Área de Pruebas',
        'nivel_prioridad' => 'medio',
        'observaciones' => 'Este es un reporte de prueba',
        'tecnico' => 'Técnico Test',
        'usuario_id' => 1
    ];
    
    $sql = "INSERT INTO reportes (contrato, unidad_medica, equipo, modelo, serie, inventario, 
              tipo_mantenimiento, estado, clasificacion, ubicacion, nivel_prioridad, 
              observaciones, tecnico, usuario_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conexion->prepare($sql);
    $result = $stmt->execute(array_values($test_data));
    
    if ($result) {
        $id = $conexion->lastInsertId();
        echo "<p style='color: green;'>✅ Inserción exitosa. ID del reporte: $id</p>";
        
        // Eliminar el registro de prueba
        $conexion->exec("DELETE FROM reportes WHERE id = $id");
        echo "<p>Registro de prueba eliminado</p>";
    } else {
        echo "<p style='color: red;'>❌ Error en la inserción</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error en inserción: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='dashboard.php'>Ir al Dashboard</a> | <a href='reportes.php'>Ver Reportes</a></p>";
?>