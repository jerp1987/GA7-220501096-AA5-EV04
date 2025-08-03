<?php
// conexión.php — Configuración de conexión a la base de datos MySQL

$host = 'localhost';
$usuario = 'root';
$contrasena = '';
$base_de_datos = 'crudusuario';

// Crear conexión usando mysqli orientado a objetos
$conexion = new mysqli($host, $usuario, $contrasena, $base_de_datos);

// Verificar conexión
if ($conexion->connect_error) {
    error_log("❌ Error de conexión MySQL: " . $conexion->connect_error);

    // Mostrar mensaje JSON si es invocado desde web/API
    if (php_sapi_name() !== 'cli') {
        header('Content-Type: application/json');
        echo json_encode([
            "success" => false,
            "message" => "❌ No se pudo conectar a la base de datos."
        ]);
        exit();
    } else {
        // Mostrar error en consola si se ejecuta por CLI
        die("❌ Error de conexión en consola.");
    }
}

// Establecer conjunto de caracteres (recomendado para tildes y emojis)
$conexion->set_charset("utf8mb4");
?>