<?php
// diagnostico.php
//no afecta directamente 
// prueba de conccion etc...
echo "<h2>Diagnóstico de Conexión MySQL</h2>";

// Probamos la conexión directamente aquí
$host = 'localhost';
$port = 3307;
$username = 'root';
$password = '';
$dbname = 'reportes_mantenimiento';

echo "<p>Intentando conectar a: <strong>$host:$port</strong></p>";

try {
    // Intentar conexión sin base de datos primero
    $conn = new PDO("mysql:host=$host;port=$port", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color: green;'>✓ Conexión al servidor MySQL EXITOSA</p>";
    
    // Verificar si la base de datos existe
    $databases = $conn->query("SHOW DATABASES")->fetchAll(PDO::FETCH_COLUMN);
    if (in_array($dbname, $databases)) {
        echo "<p style='color: green;'>✓ Base de datos '$dbname' EXISTE</p>";
        
        // Conectar a la base de datos específica
        $conn_db = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $username, $password);
        $conn_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "<p style='color: green;'>✓ Conexión a la base de datos EXITOSA</p>";
        
        // Mostrar tablas existentes
        $tables = $conn_db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        echo "<p>Tablas existentes: " . (count($tables) ? implode(', ', $tables) : 'NINGUNA') . "</p>";
        
    } else {
        echo "<p style='color: orange;'>⚠ Base de datos '$dbname' NO EXISTE, se creará automáticamente</p>";
    }
    
} catch(PDOException $e) {
    echo "<p style='color: red;'>✗ ERROR: " . $e->getMessage() . "</p>";
    echo "<p><strong>Soluciones:</strong></p>";
    echo "<ul>";
    echo "<li>Verifica que MySQL esté ejecutándose en XAMPP</li>";
    echo "<li>Revisa que el puerto 3307 sea el correcto en el panel de XAMPP</li>";
    echo "<li>Prueba reiniciar XAMPP</li>";
    echo "</ul>";
}

// Verificar si el archivo config.php existe
echo "<h3>Verificación de archivos</h3>";
$config_path = __DIR__ . '/includes/config.php';
if (file_exists($config_path)) {
    echo "<p>✓ Archivo config.php EXISTE</p>";
    
    // Mostrar contenido del archivo config (sin contraseñas)
    $config_content = file_get_contents($config_path);
    echo "<p><strong>Contenido de config.php:</strong></p>";
    echo "<pre style='background: #f4f4f4; padding: 10px; border: 1px solid #ddd;'>";
    echo htmlspecialchars($config_content);
    echo "</pre>";
} else {
    echo "<p style='color: red;'>✗ Archivo config.php NO EXISTE en: $config_path</p>";
}
?>