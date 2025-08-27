<?php
include 'includes/auth.php';

// Test de authentication
echo "<h2>Testing auth.php</h2>";

// Test 1: Login correcto
$_POST['username'] = 'admin';
$_POST['password'] = '1234';

if (login('admin', '1234')) {
    echo "<p style='color:green;'>✓ Login admin correcto</p>";
    echo "<pre>Session: "; print_r($_SESSION); echo "</pre>";
} else {
    echo "<p style='color:red;'>✗ Login admin fallido</p>";
}

// Test 2: Verificar funciones
echo "<p>is_logged_in(): " . (is_logged_in() ? 'TRUE' : 'FALSE') . "</p>";
echo "<p>tiene_acceso('admin'): " . (tiene_acceso('admin') ? 'TRUE' : 'FALSE') . "</p>";
echo "<p>tiene_acceso('tecnico'): " . (tiene_acceso('tecnico') ? 'TRUE' : 'FALSE') . "</p>";