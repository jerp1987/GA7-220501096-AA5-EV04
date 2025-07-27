<?php
// logout.php - Cerrar sesión y redirigir

session_start();

// Limpiar variables de sesión
$_SESSION = [];

// Borrar cookie de sesión si aplica
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"] ?? false,
        $params["httponly"] ?? false
    );
}

// Destruir sesión
session_destroy();

// Redirigir al inicio
header("Location: index.html");
exit();
?>