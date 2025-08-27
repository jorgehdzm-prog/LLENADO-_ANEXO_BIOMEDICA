<?php
// login.php

// Activar visualización de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Iniciar sesión antes de cualquier output
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Verificar si ya está logueado
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

// Incluir archivo de autenticación
require_once __DIR__ . '/includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Validación básica
    if (empty($username) || empty($password)) {
        header('Location: index.php?error=empty_fields');
        exit;
    }

    if (login($username, $password)) {
        header('Location: dashboard.php');
        exit;
    } else {
        header('Location: index.php?error=invalid_credentials');
        exit;
    }
}

// Si llega aquí, redirigir al index
header('Location: index.php');
exit;