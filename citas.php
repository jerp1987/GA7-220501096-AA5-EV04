<?php
require_once 'conexion.php';
header('Content-Type: application/json');

// CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// OPTIONS (Preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$data = json_decode(file_get_contents("php://input"));

// === MÉTODO POST: Agendar cita ===
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombres = $apellidos = $cedula = $correo = $celular = $servicios = $descripcion = '';

    if (!empty($_POST)) {
        $nombres     = trim($_POST['nombres'] ?? '');
        $apellidos   = trim($_POST['apellidos'] ?? '');
        $cedula      = trim($_POST['cedula'] ?? '');
        $correo      = trim($_POST['correo'] ?? '');
        $celular     = trim($_POST['celular'] ?? '');
        $servicios   = trim($_POST['servicios'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
    } elseif (!empty($data)) {
        $nombres     = trim($data->nombres ?? '');
        $apellidos   = trim($data->apellidos ?? '');
        $cedula      = trim($data->cedula ?? '');
        $correo      = trim($data->correo ?? '');
        $celular     = trim($data->celular ?? '');
        $servicios   = trim($data->servicios ?? '');
        $descripcion = trim($data->descripcion ?? '');
    }

    if (empty($nombres) || empty($apellidos) || empty($cedula) || empty($correo) || empty($celular)) {
        echo json_encode(["success" => false, "message" => "⚠️ Todos los campos son obligatorios."]);
        exit();
    }

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["success" => false, "message" => "⚠️ Correo inválido."]);
        exit();
    }

    $stmt = $conexion->prepare("INSERT INTO citas (nombres, apellidos, cedula, correo, celular, servicios, descripcion) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        echo json_encode(["success" => false, "message" => "❌ Error preparando consulta."]);
        exit();
    }

    $stmt->bind_param("sssssss", $nombres, $apellidos, $cedula, $correo, $celular, $servicios, $descripcion);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "✅ Cita agendada correctamente."]);
    } else {
        echo json_encode(["success" => false, "message" => "❌ Error al guardar la cita."]);
    }

    $stmt->close();
    $conexion->close();
    exit();
}

// === MÉTODO DELETE: Cancelar cita ===
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $cedula = trim($data->cedula ?? '');
    $motivo = trim($data->motivo ?? 'Sin especificar');

    if (empty($cedula)) {
        echo json_encode(["success" => false, "message" => "⚠️ La cédula es obligatoria."]);
        exit();
    }

    // Buscar ID de cita por cédula
    $consulta = $conexion->prepare("SELECT id FROM citas WHERE cedula = ?");
    $consulta->bind_param("s", $cedula);
    $consulta->execute();
    $resultado = $consulta->get_result();

    if ($resultado && $resultado->num_rows > 0) {
        $cita = $resultado->fetch_assoc();
        $cita_id = $cita['id'];

        // 1. Insertar cancelación
        $insert = $conexion->prepare("INSERT INTO cancelaciones (cita_id, motivo, fecha_cancelacion) VALUES (?, ?, NOW())");
        $insert->bind_param("is", $cita_id, $motivo);
        $insert->execute();
        $insert->close();

        // 2. Eliminar cita original
        $delete = $conexion->prepare("DELETE FROM citas WHERE id = ?");
        $delete->bind_param("i", $cita_id);

        if ($delete->execute()) {
            echo json_encode(["success" => true, "message" => "✅ Cita cancelada correctamente."]);
        } else {
            echo json_encode(["success" => false, "message" => "❌ Error al eliminar la cita."]);
        }

        $delete->close();
    } else {
        echo json_encode(["success" => false, "message" => "❌ No se encontró una cita con esa cédula."]);
    }

    $consulta->close();
    $conexion->close();
    exit();
}

// === OTROS MÉTODOS ===
http_response_code(405);
echo json_encode(["success" => false, "message" => "🚫 Método no permitido."]);
?>