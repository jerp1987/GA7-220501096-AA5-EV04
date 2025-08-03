<?php
require_once "conexion.php";
header("Content-Type: application/json");

// Obtener datos JSON para PUT/DELETE o POSTMAN
$input = json_decode(file_get_contents("php://input"), false);

// ====================== MÉTODO GET ======================
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!empty($_GET['id'])) {
        $id = intval($_GET['id']);
        $stmt = $conexion->prepare("SELECT id, nombre, correo, rol FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($usuario = $resultado->fetch_assoc()) {
            echo json_encode(["success" => true, "usuario" => $usuario]);
        } else {
            http_response_code(404);
            echo json_encode(["success" => false, "message" => "⚠️ Usuario no encontrado."]);
        }

        $stmt->close();
    } else {
        $result = $conexion->query("SELECT id, nombre, correo, rol FROM usuarios");
        $usuarios = [];

        while ($row = $result->fetch_assoc()) {
            $usuarios[] = $row;
        }

        echo json_encode(["success" => true, "usuarios" => $usuarios]);
    }

    $conexion->close();
    exit();
}

// ====================== MÉTODO POST ======================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = '';
    $correo = '';
    $clave = '';
    $rol = 'cliente';

    if (!empty($_POST)) {
        $nombre = trim($_POST['nombre'] ?? '');
        $correo = trim($_POST['correo'] ?? '');
        $clave  = $_POST['clave'] ?? '';
        $rol    = trim($_POST['rol'] ?? 'cliente');
    } elseif (!empty($input)) {
        $nombre = trim($input->nombre ?? '');
        $correo = trim($input->correo ?? '');
        $clave  = $input->clave ?? '';
        $rol    = trim($input->rol ?? 'cliente');
    }

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

    // Verificar duplicado
    $check = $conexion->prepare("SELECT id FROM usuarios WHERE correo = ?");
    $check->bind_param("s", $correo);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        http_response_code(409);
        echo json_encode(["success" => false, "message" => "⚠️ El correo ya está registrado."]);
        $check->close();
        exit();
    }
    $check->close();

    // Insertar usuario
    $clave_hash = password_hash($clave, PASSWORD_DEFAULT);
    $stmt = $conexion->prepare("INSERT INTO usuarios (nombre, correo, contrasena, rol) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nombre, $correo, $clave_hash, $rol);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "✅ Usuario registrado correctamente."]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "❌ Error al registrar usuario."]);
    }

    $stmt->close();
    $conexion->close();
    exit();
}

// ====================== MÉTODO PUT ======================
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    if (!empty($input->id) && !empty($input->nombre) && !empty($input->correo) && !empty($input->rol)) {
        $id     = intval($input->id);
        $nombre = trim($input->nombre);
        $correo = trim($input->correo);
        $rol    = trim($input->rol);

        $roles_validos = ['administrador', 'empleado', 'cliente'];
        if (!in_array($rol, $roles_validos)) {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "❌ Rol inválido."]);
            exit();
        }

        $stmt = $conexion->prepare("UPDATE usuarios SET nombre = ?, correo = ?, rol = ? WHERE id = ?");
        $stmt->bind_param("sssi", $nombre, $correo, $rol, $id);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "✅ Usuario actualizado correctamente."]);
        } else {
            http_response_code(500);
            echo json_encode(["success" => false, "message" => "❌ Error al actualizar usuario."]);
        }

        $stmt->close();
    } else {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "⚠️ Datos incompletos para actualizar."]);
    }

    $conexion->close();
    exit();
}

// ====================== MÉTODO DELETE ======================
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    if (!empty($input->id)) {
        $id = intval($input->id);
        $stmt = $conexion->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "✅ Usuario eliminado correctamente."]);
        } else {
            http_response_code(500);
            echo json_encode(["success" => false, "message" => "❌ Error al eliminar usuario."]);
        }

        $stmt->close();
    } else {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "⚠️ ID no especificado para eliminar."]);
    }

    $conexion->close();
    exit();
}

// ====================== MÉTODO NO PERMITIDO ======================
http_response_code(405);
echo json_encode(["success" => false, "message" => "🚫 Método no permitido."]);
exit();
?>