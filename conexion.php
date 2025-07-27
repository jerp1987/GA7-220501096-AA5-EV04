<?php
$host = 'localhost';
$usuario = 'root';
$contrasena = '';
$base_de_datos = 'crudusuario';

$conn = new mysqli($host, $usuario, $contrasena, $base_de_datos);

if ($conn->connect_errno) {
    error_log("Error de conexión a la base de datos: " . $conn->connect_error);
    die("❌ No se pudo conectar a la base de datos.");
}

$conn->set_charset("utf8mb4");
$conexion = $conn; // para mantener el nombre estándar en tu sistema
?>



