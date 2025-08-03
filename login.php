<?php
// login.php — Inicio de sesión desde formulario o Postman (API híbrida)

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'conexion.php';
header('Content-Type: application/json');

// Solo permitir método POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "❌ Método no permitido."]);
    exit();
}

// Inicializar variables
$correo_electronico = '';
$clave_ingresada = '';

// Capturar datos desde formulario (x-www-form-urlencoded o multipart/form-data)
if (!empty($_POST['correo']) && !empty($_POST['clave'])) {
    $correo_electronico = trim($_POST['correo']);
    $clave_ingresada = $_POST['clave'];
} else {
    // Capturar desde JSON (Postman u otras APIs)
    $input = json_decode(file_get_contents("php://input"));
    if (!empty($input->correo) && !empty($input->clave)) {
        $correo_electronico = trim($input->correo);
        $clave_ingresada = $input->clave;
    }
}

// Validar campos obligatorios
if (empty($correo_electronico) || empty($clave_ingresada)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "⚠️ El correo y la contraseña son obligatorios."]);
    exit();
}

// Consulta del usuario en la base de datos
$sql = "SELECT id, nombre, contrasena, rol FROM usuarios WHERE correo = ?";
$stmt = $conexion->prepare($sql);

if (!$stmt) {
    error_log("❌ Error al preparar la consulta: " . $conexion->error);
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "❌ Error interno del servidor."]);
    exit();
}

$stmt->bind_param("s", $correo_electronico);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 1) {
    // Extraer los datos del usuario
    $stmt->bind_result($user_id, $user_name, $clave_hash, $rol);
    $stmt->fetch();

    if (password_verify($clave_ingresada, $clave_hash)) {
        // Autenticación exitosa: guardar sesión y redirigir por rol
        $_SESSION['user_id'] = $user_id;
        $_SESSION['user_name'] = $user_name;
        $_SESSION['user_email'] = $correo_electronico;
        $_SESSION['user_rol'] = $rol;

        // Ruta de redirección por rol
        $redirectUrl = match ($rol) {
            'administrador' => 'modulo_administrador.php',
            'empleado'      => 'modulo_empleado.php',
            'cliente'       => 'modulo_cliente.php',
            default         => 'index.html'
        };

        echo json_encode([
            "success" => true,
            "message" => "✅ Autenticación exitosa.",
            "redirect" => $redirectUrl
        ]);
    } else {
        http_response_code(401);
        echo json_encode(["success" => false, "message" => "❌ Contraseña incorrecta."]);
    }
} else {
    http_response_code(404);
    echo json_encode(["success" => false, "message" => "❌ Usuario no registrado."]);
}

$stmt->close();
$conexion->close();
?>