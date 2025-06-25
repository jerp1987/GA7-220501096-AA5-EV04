<?php
// dashboard.php
// Página protegida que requiere que el usuario haya iniciado sesión.

// Inicia la sesión PHP
session_start();

// Verifica si el usuario NO ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    // Si no hay sesión, redirige al usuario a la página de inicio de sesión
    header("Location: index.html");
    exit(); // Detiene la ejecución del script
}

// Si la sesión existe, obtiene el nombre completo del usuario de la sesión
$userName = htmlspecialchars($_SESSION['user_name']); // El nombre completo se guarda aquí
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido - Dashboard</title>
    <!-- Incluye Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f0f4f8;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            box-sizing: border-box;
        }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen p-4">
    <div class="bg-white p-8 rounded-lg shadow-xl text-center max-w-md w-full">
        <h2 class="text-3xl font-bold text-green-700 mb-4">¡Bienvenido!</h2>
        <p class="text-xl text-gray-700 mb-6">Hola, <span class="font-semibold text-green-800"><?php echo $userName; ?></span>. Has iniciado sesión correctamente.</p>

        <!-- Botón para cerrar sesión -->
        <a href="logout.php"
           class="inline-block bg-red-600 text-white font-bold py-2 px-6 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition duration-200 ease-in-out shadow-md">
            Cerrar Sesión
        </a>
    </div>
</body>
</html>