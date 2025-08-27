<?php
// debug_reportes.php
//no afecta directamente al programa inicial 
//verifica y busca algun problema de coneccion en la conexion sql
 
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';

if (!is_logged_in()) {
    header('Location: index.php');
    exit;
}

echo "<h2>Debug de Reportes - Análisis Completo</h2>";

// 1. Verificar usuario y permisos
echo "<h3>1. Información del Usuario</h3>";
echo "User ID: " . $_SESSION['user_id'] . "<br>";
echo "Username: " . $_SESSION['username'] . "<br>";
echo "Nivel acceso: " . $_SESSION['nivel_acceso'] . "<br>";
echo "Es admin: " . (tiene_acceso('admin') ? 'Sí' : 'No') . "<br>";

// 2. Verificar la consulta exacta que se usa
echo "<h3>2. Consulta SQL que se ejecuta</h3>";

$filtro_usuario = "";
$parametros = [];

if (!tiene_acceso('admin')) {
    $filtro_usuario = " WHERE r.usuario_id = :user_id";
    $parametros[':user_id'] = $_SESSION['user_id'];
    echo "🔐 <strong>Filtro aplicado:</strong> Solo reportes del usuario actual<br>";
} else {
    echo "👑 <strong>Filtro aplicado:</strong> Todos los reportes (admin)<br>";
}

// Búsqueda y filtros adicionales
$filtros = [];
if (isset($_GET['busqueda']) && !empty($_GET['busqueda'])) {
    $filtros[] = "(r.contrato LIKE :busqueda OR r.equipo LIKE :busqueda OR r.unidad_medica LIKE :busqueda)";
    $parametros[':busqueda'] = '%' . $_GET['busqueda'] . '%';
}

if (isset($_GET['tipo_mantenimiento']) && !empty($_GET['tipo_mantenimiento'])) {
    $filtros[] = "r.tipo_mantenimiento = :tipo_mantenimiento";
    $parametros[':tipo_mantenimiento'] = $_GET['tipo_mantenimiento'];
}

if (count($filtros) > 0) {
    $filtro_adicional = $filtro_usuario ? " AND " : " WHERE ";
    $filtro_adicional .= implode(" AND ", $filtros);
    $filtro_usuario .= $filtro_adicional;
}

$sql = "SELECT r.*, u.username as tecnico_nombre 
        FROM reportes r 
        LEFT JOIN usuarios u ON r.usuario_id = u.id" . $filtro_usuario . " 
        ORDER BY r.fecha_creacion DESC";

echo "<p><strong>SQL completo:</strong><br> <code>" . htmlspecialchars($sql) . "</code></p>";

if (!empty($parametros)) {
    echo "<p><strong>Parámetros:</strong><br>";
    print_r($parametros);
    echo "</p>";
}

// 3. Ejecutar la consulta y mostrar resultados
echo "<h3>3. Resultados de la Consulta</h3>";

try {
    $stmt = $conexion->prepare($sql);
    $stmt->execute($parametros);
    $reportes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p>Número de reportes encontrados: <strong>" . count($reportes) . "</strong></p>";
    
    if (count($reportes) > 0) {
        echo "<table border='1' cellpadding='8' style='width:100%'>";
        echo "<tr>
                <th>ID</th>
                <th>Contrato</th>
                <th>Equipo</th>
                <th>Tipo</th>
                <th>Fecha</th>
                <th>Técnico</th>
                <th>Usuario ID</th>
              </tr>";
        
        foreach ($reportes as $reporte) {
            echo "<tr>";
            echo "<td>" . $reporte['id'] . "</td>";
            echo "<td>" . htmlspecialchars($reporte['contrato']) . "</td>";
            echo "<td>" . htmlspecialchars($reporte['equipo']) . "</td>";
            echo "<td>" . $reporte['tipo_mantenimiento'] . "</td>";
            echo "<td>" . $reporte['fecha_creacion'] . "</td>";
            echo "<td>" . htmlspecialchars($reporte['tecnico_nombre']) . "</td>";
            echo "<td>" . $reporte['usuario_id'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: orange;'>⚠ No se encontraron reportes con los criterios actuales.</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ <strong>Error en la consulta:</strong> " . $e->getMessage() . "</p>";
}

// 4. Verificar estructura de la tabla reportes
echo "<h3>4. Estructura de la Tabla Reportes</h3>";
try {
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
} catch (Exception $e) {
    echo "<p style='color: red;'>Error al verificar estructura: " . $e->getMessage() . "</p>";
}

// 5. Verificar todos los reportes sin filtros
echo "<h3>5. Todos los Reportes en la Base de Datos</h3>";
try {
    $todos_reportes = $conexion->query("SELECT COUNT(*) as total FROM reportes")->fetch();
    echo "<p>Total de reportes en BD: <strong>" . $todos_reportes['total'] . "</strong></p>";
    
    if ($todos_reportes['total'] > 0) {
        $ejemplos = $conexion->query("SELECT id, contrato, equipo, usuario_id, fecha_creacion FROM reportes ORDER BY fecha_creacion DESC LIMIT 5")->fetchAll();
        echo "<p><strong>Últimos 5 reportes:</strong></p>";
        echo "<pre>";
        print_r($ejemplos);
        echo "</pre>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Error al contar reportes: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='reportes.php' style='background: #2196f3; color: white; padding: 10px 15px; text-decoration: none; border-radius: 4px;'>Volver a Reportes</a></p>";
?>