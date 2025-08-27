<?php
// reset_password.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/includes/config.php';

echo "<h2>Reseteo de Contraseña</h2>";

// Resetear contraseña de admin correctamente
$nueva_password = '1234';
$passwordHash = password_hash($nueva_password, PASSWORD_DEFAULT);

// Actualizar en la base de datos
$stmt = $conexion->prepare("UPDATE usuarios SET password = ? WHERE username = 'admin' AND id = 1");
$stmt->execute([$passwordHash]);

$filas_afectadas = $stmt->rowCount();

if ($filas_afectadas > 0) {
    echo "<p style='color: green;'>✓ Contraseña de admin reseteada correctamente</p>";
    echo "<p><strong>Usuario:</strong> admin</p>";
    echo "<p><strong>Contraseña:</strong> 1234</p>";
    echo "<p><strong>Hash generado:</strong> " . substr($passwordHash, 0, 50) . "...</p>";
} else {
    echo "<p style='color: red;'>✗ Error al resetear contraseña</p>";
}

// Verificar que quedó correctamente
$usuario = $conexion->query("SELECT username, password FROM usuarios WHERE username = 'admin' AND id = 1")->fetch();
if ($usuario && password_verify('1234', $usuario['password'])) {
    echo "<p style='color: green;'>✓ Verificación exitosa - La contraseña ahora funciona</p>";
} else {
    echo "<p style='color: red;'>✗ La verificación falló</p>";
}

echo "<p><a href='index.php' style='background: #4caf50; color: white; padding: 10px 15px; text-decoration: none; border-radius: 4px;'>Ir al login</a></p>";
?>