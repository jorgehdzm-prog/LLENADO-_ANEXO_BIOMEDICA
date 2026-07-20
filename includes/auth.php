<?php
// includes/auth.php - Sistema de Autenticación

// Verificar si la sesión ya está activa antes de iniciarla
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Base de datos de usuarios
$users = [
    'admin' => [
        'password' => '1234', 
        'id' => 1, 
        'role' => 'admin',
        'name' => 'Administrador'
    ],
    'jorge' => [
        'password' => 'jorgemin123', 
        'id' => 2, 
        'role' => 'tecnico',
        'name' => 'Jorge Martínez'
    ],
    'alfredo' => [
        'password' => 'alfredo123', 
        'id' => 3, 
        'role' => 'tecnico',
        'name' => 'Alfredo González'
    ],
    'evelin' => [
        'password' => 'evelin123', 
        'id' => 4, 
        'role' => 'tecnico',
        'name' => 'Evelin Ramírez'
    ]
];

/**
 * Función de login
 * Verifica credenciales y establece sesión
 */
function login($username, $password) {
    global $users;
    
    if (isset($users[$username]) && $users[$username]['password'] === $password) {
        // Asegurarnos de que la sesión esté activa
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $_SESSION['user_id'] = $users[$username]['id'];
        $_SESSION['username'] = $username;
        $_SESSION['user_role'] = $users[$username]['role'];
        $_SESSION['user_name'] = $users[$username]['name'];
        
        return true;
    }
    
    return false;
}

/**
 * Verifica si el usuario está autenticado
 */
function is_logged_in() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['user_id']);
}

/**
 * Verifica si el usuario tiene un rol específico
 */
function tiene_acceso($rol_requerido = null) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!is_logged_in()) {
        return false;
    }
    
    if (!isset($_SESSION['user_role'])) {
        error_log("Error: user_role no está definido en la sesión");
        return false;
    }
    
    if ($rol_requerido === null) {
        return true;
    }
    
    return $_SESSION['user_role'] === $rol_requerido;
}