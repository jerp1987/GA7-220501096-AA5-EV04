<?php
// login.php
// Script para autenticar usuario por correo electrónico, iniciar una sesión y devolver una respuesta JSON.

// Inicia la sesión PHP al principio del script
session_start();

// Incluye el archivo de conexión a la base de datos
include 'conexion.php';

// Establece el encabezado de respuesta para indicar que se enviará JSON
header('Content-Type: application/json');

// Verifica si la solicitud es de tipo POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Obtiene el correo electrónico y la contraseña del cuerpo de la solicitud POST
    $correo_electronico = trim($_POST['correo']);    // Ahora el correo es el identificador para login
    $clave_ingresada = $_POST['clave'];

    // Validación básica: Asegura que los campos no estén vacíos
    if (empty($correo_electronico) || empty($clave_ingresada)) {
        echo json_encode(["success" => false, "message" => "Error: El correo electrónico y la contraseña no pueden estar vacíos."]);
        $conexion->close();
        exit();
    }

    // Prepara la consulta SQL para seleccionar el ID, nombre y contraseña del usuario
    // Buscamos por la columna 'correo' ahora
    $sql = "SELECT id, nombre, contraseña FROM usuarios WHERE correo = ?";
    $stmt = $conexion->prepare($sql);

    // Verifica si la preparación de la consulta fue exitosa
    if ($stmt === false) {
        echo json_encode(["success" => false, "message" => "Error al preparar la consulta: " . $conexion->error]);
        $conexion->close();
        exit();
    }

    // Vincula el parámetro y ejecuta la consulta
    $stmt->bind_param("s", $correo_electronico); // s para string (correo electrónico)
    $stmt->execute();
    $stmt->store_result();

    // Verifica si se encontró exactamente un usuario con ese correo
    if ($stmt->num_rows === 1) {
        // Vincula el resultado de la consulta a las variables
        // Asegúrate de que las variables coincidan con las columnas seleccionadas en el SELECT
        $stmt->bind_result($user_id, $user_name_from_db, $clave_hash_almacenada);
        // Obtiene los valores del resultado
        $stmt->fetch();

        // Verifica la contraseña ingresada con la contraseña hasheada almacenada
        if (password_verify($clave_ingresada, $clave_hash_almacenada)) {
            // --- Autenticación satisfactoria ---
            // Almacena información del usuario en la sesión
            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_name'] = $user_name_from_db; // Guardamos el nombre completo del usuario
            $_SESSION['user_email'] = $correo_electronico; // También guardamos el correo en sesión

            // Devuelve un mensaje de éxito y una URL de redirección en JSON
            echo json_encode(["success" => true, "message" => "✅ Autenticación satisfactoria. Redirigiendo...", "redirect" => "dashboard.php"]);
        } else {
            // Si la contraseña es incorrecta, devuelve un mensaje de error en JSON
            echo json_encode(["success" => false, "message" => "❌ Error: Contraseña incorrecta."]);
        }
    } else {
        // Si el usuario (por correo) no fue encontrado, devuelve un mensaje de error en JSON
        echo json_encode(["success" => false, "message" => "❌ Error: Correo electrónico no registrado."]);
    }

    // Cierra la sentencia preparada
    $stmt->close();
    // Cierra la conexión a la base de datos
    $conexion->close();
} else {
    // Si el método de solicitud no es POST, devuelve un error en JSON
    echo json_encode(["success" => false, "message" => "❌ Método no permitido. Solo se aceptan solicitudes POST."]);
}
?>