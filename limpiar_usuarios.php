<?php
// limpiar_usuarios.php
// limpia los parametros anteriores que se an agregado para no sobre encimar textos
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/includes/config.php';

echo "<h2>Limpieza de Usuarios Duplicados</h2>";

// Eliminar usuarios duplicados (quedarse solo con el primero)
$conexion->exec("DELETE FROM usuarios WHERE username = 'admin' AND id != 1");

$filas_eliminadas = $conexion->query("SELECT ROW_COUNT()")->fetchColumn();

echo "<p style='color: green;'>✓ Usuarios duplicados eliminados: $filas_eliminadas</p>";

// Mostrar usuarios restantes
$usuarios = $conexion->query("SELECT * FROM usuarios")->fetchAll(PDO::FETCH_ASSOC);
echo "<h3>Usuarios en el sistema:</h3>";
echo "<pre>";
print_r($usuarios);
echo "</pre>";

echo "<p><a href='index.php'>Ir al login</a></p>";
?>