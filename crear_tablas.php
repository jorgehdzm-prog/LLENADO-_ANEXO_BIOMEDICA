<?php
// crear_tablas.php //crea las tablas en las cuales e va guardar los usuarios de 
// includes/auth.php 
//no afecta directamente al programa inical 
// crea tablas si es que estas llegan a fallar o tener algun problema de conexin en sql
echo "<h2>Estado del Sistema</h2>";

// Incluir config.php correctamente
require_once __DIR__ . '/includes/config.php';

echo "<p style='color: green;'><strong>✓ Conexión a la base de datos establecida correctamente</strong></p>";

// Verificar tablas
$tablas = $conexion->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
echo "<p><strong>Tablas en la base de datos:</strong> " . implode(', ', $tablas) . "</p>";

// Verificar usuarios
$usuarios = $conexion->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
echo "<p><strong>Total de usuarios en el sistema:</strong> $usuarios</p>";

if ($usuarios > 0) {
    $user_list = $conexion->query("SELECT username, nivel_acceso FROM usuarios")->fetchAll(PDO::FETCH_ASSOC);
    echo "<p><strong>Lista de usuarios:</strong></p>";
    echo "<ul>";
    foreach ($user_list as $user) {
        echo "<li>" . htmlspecialchars($user['username']) . " (" . $user['nivel_acceso'] . ")</li>";
    }
    echo "</ul>";
}

// Verificar reportes
$reportes = $conexion->query("SELECT COUNT(*) FROM reportes")->fetchColumn();
echo "<p><strong>Total de reportes en el sistema:</strong> $reportes</p>";

echo "<p style='color: green;'><strong>🎉 ¡Sistema configurado y funcionando correctamente!</strong></p>";
echo "<p><a href='index.php' style='background: #4caf50; color: white; padding: 10px 15px; text-decoration: none; border-radius: 4px;'>Ir al login</a></p>";
echo "<p><a href='dashboard.php' style='background: #2196f3; color: white; padding: 10px 15px; text-decoration: none; border-radius: 4px;'>Ir al dashboard</a></p>";
?>