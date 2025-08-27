<?php
//salir de dasboard y mandar a index para reiniciar el proceso de inicio de secion
require_once __DIR__ . '/includes/auth.php';

// Destruir sesión completamente
$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();

header('Location: index.php?msg=logged_out');
exit;
?>