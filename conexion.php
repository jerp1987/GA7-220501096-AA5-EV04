<?php
// conexion.php
// Conexión a la base de datos MySQL

$host = "localhost";
$usuario = "root"; // Asegúrate de que este sea el usuario de tu base de datos
$contrasena = ""; // Asegúrate de que esta sea la contraseña de tu base de datos (vacía por defecto en XAMPP para 'root')
$base_de_datos = "crud_php"; // Asegúrate de que tu base de datos se llama 'crud_php'

$conexion = new mysqli($host, $usuario, $contrasena, $base_de_datos);

// Verifica si la conexión fue exitosa
if ($conexion->connect_error) {
    // Si hay un error de conexión, detiene la ejecución y muestra el error
    die("Error en la conexión: " . $conexion->connect_error);
}
?>