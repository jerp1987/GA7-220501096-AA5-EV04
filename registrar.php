<?php
// Activar errores en entorno de desarrollo
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'conexion.php';
header('Content-Type: application/json');

// Aceptar solo método POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "message" => "❌ Solo se aceptan solicitudes POST."]);
    exit();
}

// Inicializar variables
$nombre = '';
$correo = '';
$clave = '';

// Detectar si llegan datos como FormData (desde HTML)
if (!empty($_POST)) {
    $nombre = trim($_POST['nombre'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $clave = $_POST['clave'] ?? '';
} else {
    // O si vienen como JSON (desde Postman)
    $data = json_decode(file_get_contents("php://input"));
    $nombre = trim($data->nombre ?? '');
    $correo = trim($data->correo ?? '');
    $clave = $data->clave ?? '';
}

// Validar campos requeridos
if (empty($nombre) || empty($correo) || empty($clave)) {
    echo json_encode(["success" => false, "message" => "⚠️ Todos los campos son obligatorios."]);
    exit();
}

// Verificar si el correo ya existe
$stmt_check = $conexion->prepare("SELECT id FROM usuarios WHERE correo = ?");
if (!$stmt_check) {
    echo json_encode(["success" => false, "message" => "❌ Error en la consulta de verificación."]);
    exit();
}
$stmt_check->bind_param("s", $correo);
$stmt_check->execute();
$stmt_check->store_result();

if ($stmt_check->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "⚠️ El correo ya está registrado."]);
    $stmt_check->close();
    exit();
}
$stmt_check->close();

// Encriptar contraseña y registrar
$clave_hash = password_hash($clave, PASSWORD_DEFAULT);
$stmt = $conexion->prepare("INSERT INTO usuarios (nombre, correo, contrasena) VALUES (?, ?, ?)");

if (!$stmt) {
    echo json_encode(["success" => false, "message" => "❌ Error al preparar el registro."]);
    exit();
}

$stmt->bind_param("sss", $nombre, $correo, $clave_hash);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "✅ Usuario registrado correctamente."]);
} else {
    echo json_encode(["success" => false, "message" => "❌ Error al registrar usuario."]);
}

$stmt->close();
$conexion->close();
?>
