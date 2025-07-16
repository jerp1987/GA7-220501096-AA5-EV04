<?php
// login.php
// Script para autenticar usuario por correo electrónico, iniciar sesión y devolver una respuesta JSON

session_start();
require_once 'conexion.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "message" => "❌ Método no permitido. Solo se aceptan solicitudes POST."]);
    exit();
}

$correo_electronico = trim($_POST['correo'] ?? '');
$clave_ingresada = $_POST['clave'] ?? '';

// Validar campos vacíos
if (empty($correo_electronico) || empty($clave_ingresada)) {
    echo json_encode(["success" => false, "message" => "⚠️ El correo y la contraseña son obligatorios."]);
    exit();
}

// Consulta segura con prepared statement
$sql = "SELECT id, nombre, contrasena FROM usuarios WHERE correo = ?";
$stmt = $conexion->prepare($sql);

if (!$stmt) {
    error_log("Error al preparar la consulta: " . $conexion->error);
    echo json_encode(["success" => false, "message" => "❌ Error interno del servidor."]);
    exit();
}

$stmt->bind_param("s", $correo_electronico);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 1) {
    $stmt->bind_result($user_id, $user_name, $clave_hash);
    $stmt->fetch();

    if (password_verify($clave_ingresada, $clave_hash)) {
        // Autenticación exitosa
        $_SESSION['user_id'] = $user_id;
        $_SESSION['user_name'] = $user_name;
        $_SESSION['user_email'] = $correo_electronico;

        echo json_encode([
            "success" => true,
            "message" => "✅ Autenticación exitosa. Redirigiendo...",
            "redirect" => "dashboard.php"
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "❌ Contraseña incorrecta."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "❌ Correo electrónico no registrado."]);
}

$stmt->close();
$conexion->close();
?>
