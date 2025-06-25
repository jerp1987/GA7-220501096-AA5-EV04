<?php
// registrar.php
// Script para registrar un nuevo usuario en la base de datos, usando el correo como identificador único,
// y devolver una respuesta JSON.

// Incluye el archivo de conexión a la base de datos
include 'conexion.php';

// Establece el encabezado de respuesta para indicar que se enviará JSON
header('Content-Type: application/json');

// Verifica si la solicitud es de tipo POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Obtiene el nombre completo, correo electrónico y la contraseña del cuerpo de la solicitud POST
    $nombre_completo = trim($_POST['nombre_completo']); // Nuevo campo para el nombre completo
    $correo_electronico = trim($_POST['correo']);      // El correo será el identificador único
    $clave_usuario = $_POST['clave'];

    // Validación básica: Asegura que los campos no estén vacíos
    if (empty($nombre_completo) || empty($correo_electronico) || empty($clave_usuario)) {
        echo json_encode(["success" => false, "message" => "Error: Todos los campos son obligatorios."]);
        $conexion->close();
        exit();
    }

    // --- Paso 1: Verificar si el correo electrónico ya existe ---
    // Usamos 'correo' como el campo para la verificación de unicidad
    $sql_check = "SELECT id FROM usuarios WHERE correo = ?";
    $stmt_check = $conexion->prepare($sql_check);
    if ($stmt_check === false) {
        echo json_encode(["success" => false, "message" => "Error al preparar la verificación de correo: " . $conexion->error]);
        $conexion->close();
        exit();
    }
    $stmt_check->bind_param("s", $correo_electronico);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        // Si el correo ya existe, devuelve un error específico
        echo json_encode(["success" => false, "message" => "Error: El correo electrónico '" . htmlspecialchars($correo_electronico) . "' ya está registrado."]);
        $stmt_check->close();
        $conexion->close();
        exit();
    }
    $stmt_check->close();
    // --- Fin de verificación de correo existente ---

    // Encripta la contraseña usando PASSWORD_DEFAULT
    $clave_hash = password_hash($clave_usuario, PASSWORD_DEFAULT);

    // Prepara la consulta SQL para insertar un nuevo usuario
    // Ahora insertamos en las columnas 'nombre' y 'correo' y 'contraseña' de tu tabla
    $sql_insert = "INSERT INTO usuarios (nombre, correo, contraseña) VALUES (?, ?, ?)";
    $stmt_insert = $conexion->prepare($sql_insert);

    // Verifica si la preparación de la consulta fue exitosa
    if ($stmt_insert === false) {
        echo json_encode(["success" => false, "message" => "Error al preparar la inserción de usuario: " . $conexion->error]);
        $conexion->close();
        exit();
    }

    // Vincula los parámetros a la consulta preparada (sss indica tres strings)
    // El orden y tipo deben coincidir con la consulta SQL (nombre, correo, contraseña)
    $stmt_insert->bind_param("sss", $nombre_completo, $correo_electronico, $clave_hash);

    // Ejecuta la consulta de inserción
    if ($stmt_insert->execute()) {
        // Si la ejecución fue exitosa, devuelve un mensaje de éxito en JSON
        echo json_encode(["success" => true, "message" => "✅ Usuario registrado correctamente."]);
    } else {
        // Si hubo un error al ejecutar la consulta, devuelve un mensaje de error en JSON
        echo json_encode(["success" => false, "message" => "❌ Error al registrar usuario: " . $stmt_insert->error]);
    }

    // Cierra la sentencia preparada
    $stmt_insert->close();
    // Cierra la conexión a la base de datos
    $conexion->close();
} else {
    // Si el método de solicitud no es POST, devuelve un error en JSON
    echo json_encode(["success" => false, "message" => "❌ Método no permitido. Solo se aceptan solicitudes POST."]);
}
?>