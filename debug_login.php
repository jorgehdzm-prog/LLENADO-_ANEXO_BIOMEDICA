<?php
// debug_login.php - Script para depurar el login // no afecta al sistema dasboard
//principal solo es para ver que el log in esta en correcto funcionamiento y ver los posibes errores en el log
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Depuración del Sistema de Login</h2>";

// 1. Verificar sesión
echo "<h3>1. Estado de la Sesión</h3>";
echo "Session status: " . session_status() . " (2 = PHP_SESSION_ACTIVE)<br>";

// 2. Verificar includes
echo "<h3>2. Archivos Incluidos</h3>";
try {
    require_once __DIR__ . '/includes/auth.php';
    echo "✓ auth.php incluido correctamente<br>";
    
    require_once __DIR__ . '/includes/config.php';
    echo "✓ config.php incluido correctamente<br>";
    
    // 3. Verificar conexión a BD
    echo "<h3>3. Conexión a Base de Datos</h3>";
    $test = $conexion->query("SELECT COUNT(*) FROM usuarios");
    $count = $test->fetchColumn();
    echo "✓ Conexión BD exitosa. Usuarios encontrados: $count<br>";
    
    // 4. Verificar usuario admin
    echo "<h3>4. Usuario Admin</h3>";
    $admin = $conexion->query("SELECT username, password FROM usuarios WHERE username = 'admin'")->fetch();
    if ($admin) {
        echo "✓ Usuario admin encontrado: " . $admin['username'] . "<br>";
        echo "Hash de contraseña: " . substr($admin['password'], 0, 20) . "...<br>";
        
        // Verificar contraseña
        $password_test = '1234';
        if (password_verify($password_test, $admin['password'])) {
            echo "✓ Contraseña '1234' verifica correctamente<br>";
        } else {
            echo "✗ Contraseña '1234' NO verifica<br>";
        }
    } else {
        echo "✗ Usuario admin no encontrado<br>";
    }
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "<br>";
}

// 5. Probar función login
echo "<h3>5. Prueba de Función Login</h3>";
try {
    // Probamos con credenciales correctas
    $result = login('admin', '1234');
    echo "Resultado de login('admin', '1234'): " . ($result ? 'TRUE' : 'FALSE') . "<br>";
    
    // Probamos con credenciales incorrectas
    $result2 = login('admin', 'wrongpassword');
    echo "Resultado de login('admin', 'wrongpassword'): " . ($result2 ? 'TRUE' : 'FALSE') . "<br>";
    
} catch (Exception $e) {
    echo "Error en función login: " . $e->getMessage() . "<br>";
}

echo "<h3>6. Prueba de Redirección</h3>";
echo "<form action='login.php' method='POST' style='border: 1px solid #ccc; padding: 20px;'>";
echo "<h4>Probar Login Directamente</h4>";
echo "<input type='text' name='username' value='admin' placeholder='Usuario'><br>";
echo "<input type='password' name='password' value='1234' placeholder='Contraseña'><br>";
echo "<button type='submit'>Probar Login</button>";
echo "</form>";

echo "<hr>";
echo "<p><a href='index.php'>Volver al Login</a></p>";
?>