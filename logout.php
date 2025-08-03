<?php
// logout.php — Finaliza la sesión y responde según el contexto

// Iniciar sesión si no está activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Detectar si la solicitud espera JSON (API)
$isApiRequest = isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json');

// Permitir métodos GET y POST únicamente
$method = $_SERVER["REQUEST_METHOD"];
if (!in_array($method, ['GET', 'POST'])) {
    http_response_code(405);
    $response = ["success" => false, "message" => "❌ Método no permitido."];
    echo $isApiRequest ? json_encode($response) : "❌ Método no permitido.";
    exit();
}

// Destruir sesión
$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();

// Responder según el tipo de solicitud
if ($isApiRequest) {
    echo json_encode(["success" => true, "message" => "✅ Sesión cerrada correctamente."]);
} else {
    // Redirección en caso de navegador
    header("Location: index.html");
    exit();
}
?>