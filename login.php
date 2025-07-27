<?php
// login.php - Autenticación desde formulario o Postman

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'conexion.php';
header('Content-Type: application/json');

// Aceptar solo solicitudes POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "❌ Método no permitido. Solo POST."]);
    exit();
}

// Inicializar variables
$correo_electronico = '';
$clave_ingresada = '';

// Leer datos desde FormData (HTML) o JSON (Postman)
if (!empty($_POST['correo']) && !empty($_POST['clave'])) {
    $correo_electronico = trim($_POST['correo']);
    $clave_ingresada = $_POST['clave'];
} else {
    $input = json_decode(file_get_contents("php://input"));
    if (!empty($input->correo) && !empty($input->clave)) {
        $correo_electronico = trim($input->correo);
        $clave_ingresada = $input->clave;
    }
}

// Validación básica
if (empty($correo_electronico) || empty($clave_ingresada)) {
    echo json_encode(["success" => false, "message" => "⚠️ El correo y la contraseña son obligatorios."]);
    exit();
}

// Consulta segura
$sql = "SELECT id, nombre, contrasena FROM usuarios WHERE correo = ?";
$stmt = $conexion->prepare($sql);

if (!$stmt) {
    error_log("❌ Error en prepare: " . $conexion->error);
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
        // Autenticación correcta
        $_SESSION['user_id'] = $user_id;
        $_SESSION['user_name'] = $user_name;
        $_SESSION['user_email'] = $correo_electronico;

        echo json_encode([
            "success" => true,
            "message" => "✅ Autenticación exitosa.",
            "redirect" => "dashboard.php"
        ]);
    } else {
        http_response_code(401);
        echo json_encode(["success" => false, "message" => "❌ Contraseña incorrecta."]);
    }
} else {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "❌ Correo electrónico no registrado."]);
}

$stmt->close();
$conexion->close();
?>