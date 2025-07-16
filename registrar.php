<?php
// registrar.php
// Script para registrar un nuevo usuario en la base de datos

require_once 'conexion.php';
header('Content-Type: application/json');

// Asegurarse que la solicitud sea POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "message" => "❌ Método no permitido. Solo se aceptan solicitudes POST."]);
    exit();
}

// Capturar y validar datos
$nombre = trim($_POST['nombre_completo'] ?? '');
$correo = trim($_POST['correo'] ?? '');
$clave = $_POST['clave'] ?? '';

if (empty($nombre) || empty($correo) || empty($clave)) {
    echo json_encode(["success" => false, "message" => "⚠️ Todos los campos son obligatorios."]);
    exit();
}

// Verificar si el correo ya existe
$sql_check = "SELECT id FROM usuarios WHERE correo = ?";
$stmt_check = $conexion->prepare($sql_check);

if (!$stmt_check) {
    error_log("Error al preparar la verificación: " . $conexion->error);
    echo json_encode(["success" => false, "message" => "❌ Error interno al verificar el correo."]);
    exit();
}

$stmt_check->bind_param("s", $correo);
$stmt_check->execute();
$stmt_check->store_result();

if ($stmt_check->num_rows > 0) {
    echo json_encode([
        "success" => false,
        "message" => "⚠️ El correo '" . htmlspecialchars($correo) . "' ya está registrado."
    ]);
    $stmt_check->close();
    exit();
}
$stmt_check->close();

// Hashear la contraseña
$clave_hash = password_hash($clave, PASSWORD_DEFAULT);

// Insertar nuevo usuario
$sql_insert = "INSERT INTO usuarios (nombre, correo, contrasena) VALUES (?, ?, ?)";
$stmt_insert = $conexion->prepare($sql_insert);

if (!$stmt_insert) {
    error_log("Error al preparar inserción: " . $conexion->error);
    echo json_encode(["success" => false, "message" => "❌ Error interno al registrar usuario."]);
    exit();
}

$stmt_insert->bind_param("sss", $nombre, $correo, $clave_hash);

if ($stmt_insert->execute()) {
    echo json_encode(["success" => true, "message" => "✅ Usuario registrado correctamente."]);
} else {
    echo json_encode(["success" => false, "message" => "❌ Error al registrar usuario: " . $stmt_insert->error]);
}

$stmt_insert->close();
$conexion->close();
?>
