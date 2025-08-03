<?php
session_start();
require_once 'conexion.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] !== 'administrador') {
    echo json_encode(["success" => false, "message" => "Acceso no autorizado."]);
    exit();
}

// Verificar que se envió el formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $nombre = trim($_POST['nombre'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $contrasena = $_POST['contrasena'] ?? '';
    $rol = trim($_POST['rol'] ?? '');

    if (empty($nombre) || empty($correo) || empty($rol)) {
        echo json_encode(["success" => false, "message" => "Todos los campos excepto contraseña son obligatorios."]);
        exit();
    }

    if ($id > 0) {
        // Actualizar usuario
        if (!empty($contrasena)) {
            $contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);
            $sql = "UPDATE usuarios SET nombre=?, correo=?, contrasena=?, rol=? WHERE id=?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("ssssi", $nombre, $correo, $contrasena_hash, $rol, $id);
        } else {
            $sql = "UPDATE usuarios SET nombre=?, correo=?, rol=? WHERE id=?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("sssi", $nombre, $correo, $rol, $id);
        }
        $accion = "actualizado";
    } else {
        // Insertar nuevo usuario
        if (empty($contrasena)) {
            echo json_encode(["success" => false, "message" => "La contraseña es obligatoria al registrar un nuevo usuario."]);
            exit();
        }
        $contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);
        $sql = "INSERT INTO usuarios (nombre, correo, contrasena, rol) VALUES (?, ?, ?, ?)";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ssss", $nombre, $correo, $contrasena_hash, $rol);
        $accion = "registrado";
    }

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Usuario $accion correctamente."]);
    } else {
        echo json_encode(["success" => false, "message" => "Error: " . $stmt->error]);
    }

    $stmt->close();
    $conexion->close();
} else {
    echo json_encode(["success" => false, "message" => "Método no permitido."]);
}
?>