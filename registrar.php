<?php
// registrar.php — Registro de usuario desde formulario HTML o Postman

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'conexion.php';
header('Content-Type: application/json');

// Solo permitir método POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "❌ Solo se aceptan solicitudes POST."]);
    exit();
}

// Detectar tipo de solicitud: Formulario HTML o JSON
$isForm = isset($_POST['nombre']) && isset($_POST['correo']) && isset($_POST['clave']);

// Obtener datos
if ($isForm) {
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $clave  = $_POST['clave'];
    $rol    = trim($_POST['rol'] ?? 'cliente');
} else {
    $data = json_decode(file_get_contents("php://input"));
    $nombre = trim($data->nombre ?? '');
    $correo = trim($data->correo ?? '');
    $clave  = $data->clave ?? '';
    $rol    = trim($data->rol ?? 'cliente');
}

// Validar datos
if (empty($nombre) || empty($correo) || empty($clave)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "⚠️ Todos los campos son obligatorios."]);
    exit();
}

$roles_validos = ['administrador', 'empleado', 'cliente'];
if (!in_array($rol, $roles_validos)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "❌ Rol inválido."]);
    exit();
}

// Verificar si ya existe el correo
$stmt_check = $conexion->prepare("SELECT id FROM usuarios WHERE correo = ?");
$stmt_check->bind_param("s", $correo);
$stmt_check->execute();
$stmt_check->store_result();

if ($stmt_check->num_rows > 0) {
    http_response_code(409);
    echo json_encode(["success" => false, "message" => "⚠️ El correo ya está registrado."]);
    $stmt_check->close();
    exit();
}
$stmt_check->close();

// Encriptar clave y registrar
$clave_hash = password_hash($clave, PASSWORD_DEFAULT);

$stmt = $conexion->prepare("INSERT INTO usuarios (nombre, correo, contrasena, rol) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $nombre, $correo, $clave_hash, $rol);

if ($stmt->execute()) {
    echo json_encode([
        "success" => true,
        "message" => "✅ Usuario registrado correctamente como $rol."
    ]);
} else {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "❌ Error al registrar el usuario."]);
}

$stmt->close();
$conexion->close();
?>