<?php
require_once "conexion.php";
header("Content-Type: application/json");

// Leer input JSON
$input = json_decode(file_get_contents("php://input"));

// ====================== MÉTODO GET ======================
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!empty($_GET['id'])) {
        $id = intval($_GET['id']);
        $stmt = $conexion->prepare("SELECT id, nombre, correo FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($usuario = $resultado->fetch_assoc()) {
            echo json_encode(["success" => true, "usuario" => $usuario]);
        } else {
            echo json_encode(["success" => false, "message" => "⚠️ Usuario no encontrado."]);
        }

        $stmt->close();
    } else {
        $result = $conexion->query("SELECT id, nombre, correo FROM usuarios");
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

    if (!empty($_POST)) {
        $nombre = trim($_POST['nombre'] ?? '');
        $correo = trim($_POST['correo'] ?? '');
        $clave = $_POST['clave'] ?? '';
    } elseif (!empty($input)) {
        $nombre = trim($input->nombre ?? '');
        $correo = trim($input->correo ?? '');
        $clave = $input->clave ?? '';
    }

    if (empty($nombre) || empty($correo) || empty($clave)) {
        echo json_encode(["success" => false, "message" => "⚠️ Todos los campos son obligatorios."]);
        exit();
    }

    $check = $conexion->prepare("SELECT id FROM usuarios WHERE correo = ?");
    $check->bind_param("s", $correo);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "⚠️ El correo ya está registrado."]);
        $check->close();
        exit();
    }
    $check->close();

    $clave_hash = password_hash($clave, PASSWORD_DEFAULT);
    $stmt = $conexion->prepare("INSERT INTO usuarios (nombre, correo, contrasena) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nombre, $correo, $clave_hash);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "✅ Usuario registrado correctamente."]);
    } else {
        echo json_encode(["success" => false, "message" => "❌ Error al registrar usuario."]);
    }

    $stmt->close();
    $conexion->close();
    exit();
}

// ====================== MÉTODO PUT ======================
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    if (!empty($input->id) && !empty($input->nombre) && !empty($input->correo)) {
        $id = intval($input->id);
        $nombre = trim($input->nombre);
        $correo = trim($input->correo);

        $stmt = $conexion->prepare("UPDATE usuarios SET nombre = ?, correo = ? WHERE id = ?");
        if (!$stmt) {
            echo json_encode(["success" => false, "message" => "❌ Error en la consulta."]);
            exit();
        }

        $stmt->bind_param("ssi", $nombre, $correo, $id);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "✅ Usuario actualizado correctamente."]);
        } else {
            echo json_encode(["success" => false, "message" => "❌ Error al actualizar usuario."]);
        }

        $stmt->close();
    } else {
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
        if (!$stmt) {
            echo json_encode(["success" => false, "message" => "❌ Error al preparar eliminación."]);
            exit();
        }

        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "✅ Usuario eliminado correctamente."]);
        } else {
            echo json_encode(["success" => false, "message" => "❌ Error al eliminar usuario."]);
        }

        $stmt->close();
    } else {
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