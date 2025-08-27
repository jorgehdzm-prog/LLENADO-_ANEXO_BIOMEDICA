<?php
// verificar_tabla.php no expplicar
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/includes/config.php';

echo "<h2>Estructura Real de la Tabla Usuarios</h2>";

// Mostrar estructura de la tabla usuarios
$estructura = $conexion->query("DESCRIBE usuarios")->fetchAll(PDO::FETCH_ASSOC);
echo "<h3>Columnas de la tabla 'usuarios':</h3>";
echo "<table border='1' cellpadding='8'>";
echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
foreach ($estructura as $columna) {
    echo "<tr>";
    echo "<td>" . $columna['Field'] . "</td>";
    echo "<td>" . $columna['Type'] . "</td>";
    echo "<td>" . $columna['Null'] . "</td>";
    echo "<td>" . $columna['Key'] . "</td>";
    echo "<td>" . $columna['Default'] . "</td>";
    echo "<td>" . $columna['Extra'] . "</td>";
    echo "</tr>";
}
echo "</table>";

// Mostrar datos de usuarios
echo "<h3>Datos en la tabla 'usuarios':</h3>";
$usuarios = $conexion->query("SELECT * FROM usuarios")->fetchAll(PDO::FETCH_ASSOC);
echo "<pre>";
print_r($usuarios);
echo "</pre>";

// Verificar la contraseña manualmente
echo "<h3>Verificación de Contraseña Manual:</h3>";
if (count($usuarios) > 0) {
    $admin = $usuarios[0];
    $password_test = '1234';
    
    echo "Hash almacenado: " . $admin['password'] . "<br>";
    echo "Contraseña a verificar: '1234'<br>";
    
    if (password_verify($password_test, $admin['password'])) {
        echo "<span style='color: green;'>✓ Contraseña VERIFICADA</span>";
    } else {
        echo "<span style='color: red;'>✗ Contraseña NO verifica</span><br>";
        
        // Intentar con password sin hash
        if ($admin['password'] === '1234') {
            echo "<span style='color: green;'>✓ Contraseña coincide directamente (sin hash)</span>";
        }
    }
}
?>