<?php
// conexion.php
// Conexión segura a la base de datos MySQL usando MySQLi

$host = 'localhost';
$usuario = 'root';
$contrasena = '';
$base_de_datos = 'crudusuario';

// Crear la conexión
$conexion = new mysqli($host, $usuario, $contrasena, $base_de_datos);

// Verificar si hubo error de conexión
if ($conexion->connect_errno) {
    error_log("Error de conexión a la base de datos: " . $conexion->connect_error);
    die("❌ No se pudo conectar a la base de datos.");
}

// Opcional: establecer codificación de caracteres
$conexion->set_charset("utf8mb4");
?>

