<?php
// logout.php
// Script para cerrar sesión y redirigir al usuario al inicio

session_start();

// Vaciar variables de sesión
$_SESSION = [];

// Borrar la cookie de sesión si existe
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destruir la sesión
session_destroy();

// Redirigir al inicio
header("Location: index.html");
exit();
?>
