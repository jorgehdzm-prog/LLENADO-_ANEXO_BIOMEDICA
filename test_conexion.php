<?php
// test_conexion.php
echo "<h2>Test de Conexión a MySQL</h2>";
echo "<p>Probando conexión con diferentes configuraciones...</p>";

// Configuraciones a probar
$configuraciones = [
    ['host' => 'localhost', 'puerto' => 3307, 'usuario' => 'root', 'password' => ''],
    ['host' => 'localhost', 'puerto' => 3306, 'usuario' => 'root', 'password' => ''],
    ['host' => '127.0.0.1', 'puerto' => 3307, 'usuario' => 'root', 'password' => ''],
    ['host' => '127.0.0.1', 'puerto' => 3306, 'usuario' => 'root', 'password' => '']
];

foreach ($configuraciones as $config) {
    $host = $config['host'];
    $puerto = $config['puerto'];
    $usuario = $config['usuario'];
    $password = $config['password'];
    
    echo "<h3>Probando: $host:$puerto</h3>";
    
    try {
        // Intentar conexión sin base de datos
        $conn = new PDO("mysql:host=$host;port=$puerto", $usuario, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        echo "<p style='color: green; padding: 10px; background: #e8f5e8; border: 1px solid #4caf50;'>";
        echo "✓ CONEXIÓN EXITOSA en puerto $puerto";
        echo "</p>";
        
        // Listar bases de datos
        $databases = $conn->query("SHOW DATABASES")->fetchAll(PDO::FETCH_COLUMN);
        echo "<p><strong>Bases de datos disponibles:</strong><br>" . implode(', ', $databases) . "</p>";
        
        // Verificar versión de MySQL
        $version = $conn->query("SELECT VERSION()")->fetchColumn();
        echo "<p><strong>Versión de MySQL:</strong> $version</p>";
        
        break; // Si una conexión funciona, salir del bucle
        
    } catch(PDOException $e) {
        echo "<p style='color: red; padding: 10px; background: #ffebee; border: 1px solid #f44336;'>";
        echo "✗ Error de conexión: " . $e->getMessage();
        echo "</p>";
    }
    
    echo "<hr>";
}

// Probar también con socket (para XAMPP en Windows)
echo "<h3>Probando con socket de MySQL</h3>";
try {
    $conn = new PDO("mysql:host=localhost;unix_socket=/mysql/mysql.sock", 'root', '');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color: green;'>✓ Conexión exitosa via socket</p>";
} catch(PDOException $e) {
    echo "<p style='color: #888;'>ℹ Conexión via socket no disponible: " . $e->getMessage() . "</p>";
}

// Información del servidor
echo "<h3>Información del servidor</h3>";
echo "<p><strong>PHP Version:</strong> " . PHP_VERSION . "</p>";
echo "<p><strong>Sistema Operativo:</strong> " . PHP_OS . "</p>";
echo "<p><strong>Directorio actual:</strong> " . __DIR__ . "</p>";

echo "<h3>Instrucciones</h3>";
echo "<ol>";
echo "<li>Si ves un mensaje verde, copia los valores de host y puerto</li>";
echo "<li>Actualiza el archivo includes/config.php con esos valores</li>";
echo "<li>Si todos fallan, verifica que MySQL esté ejecutándose en XAMPP</li>";
echo "</ol>";
?>