<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] !== 'cliente') {
    header("Location: index.html");
    exit();
}
$userName = htmlspecialchars($_SESSION['user_name'], ENT_QUOTES, 'UTF-8');
$userEmail = htmlspecialchars($_SESSION['user_email'], ENT_QUOTES, 'UTF-8');
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Módulo Cliente</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6 font-sans">
  <div class="max-w-xl mx-auto bg-white p-8 rounded-lg shadow-md">
    <h1 class="text-2xl font-bold text-center text-blue-700 mb-2">
      Bienvenido, <?= $userName ?> (Cliente)
    </h1>
    <p class="text-center text-gray-600 mb-6"><?= $userEmail ?></p>

    <div class="grid gap-4 text-center">
      <a href="asignar_cita.php" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded block">Agendar Cita</a>
      <a href="cancelar_cita.php" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded block">Cancelar Cita</a>
      <a href="logout.php" class="mt-6 text-sm text-gray-500 hover:underline">Cerrar sesión</a>
    </div>
  </div>
</body>
</html>