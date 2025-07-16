<?php
// dashboard.php
// Página protegida que requiere que el usuario haya iniciado sesión.

session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
}

// Escapar el nombre del usuario para evitar inyección de HTML
$userName = htmlspecialchars($_SESSION['user_name'], ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido - Dashboard</title>
    <!-- Tailwind CSS desde CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen p-4">
    <div class="bg-white p-8 rounded-lg shadow-xl text-center max-w-md w-full">
        <h2 class="text-3xl font-bold text-green-700 mb-4">¡Bienvenido!</h2>
        <p class="text-xl text-gray-700 mb-6">
            Hola, <span class="font-semibold text-green-800"><?= $userName; ?></span>. Has iniciado sesión correctamente.
        </p>

        <!-- Botón de cerrar sesión -->
        <a href="logout.php"
           class="inline-block bg-red-600 text-white font-bold py-2 px-6 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition duration-200 ease-in-out shadow-md">
            Cerrar Sesión
        </a>
    </div>
</body>
</html>
