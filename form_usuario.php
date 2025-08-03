<!-- form_usuario.php -->
<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] !== 'administrador') {
    header("Location: index.html");
    exit();
}
$userName = htmlspecialchars($_SESSION['user_name'], ENT_QUOTES, 'UTF-8');
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registrar Usuario</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6 font-sans">
  <div class="max-w-md mx-auto bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold text-center text-green-700 mb-4">Registrar Nuevo Usuario</h2>
    <p class="text-sm text-gray-500 mb-4 text-center">Administrador: <strong><?= $userName ?></strong></p>

    <?php if (isset($_GET['msg'])): ?>
      <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-4">
        <?= htmlspecialchars($_GET['msg']) ?>
      </div>
    <?php endif; ?>

    <form action="registrar.php" method="POST" class="space-y-4">
      <input type="text" name="nombre" placeholder="Nombre completo" required class="w-full px-4 py-2 border rounded" />
      <input type="email" name="correo" placeholder="Correo electrónico" required class="w-full px-4 py-2 border rounded" />
      <input type="password" name="clave" placeholder="Contraseña" required class="w-full px-4 py-2 border rounded" />

      <select name="rol" class="w-full px-4 py-2 border rounded" required>
        <option value="cliente">Cliente</option>
        <option value="empleado">Empleado</option>
        <option value="administrador">Administrador</option>
      </select>

      <div class="flex justify-between">
        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
          Registrar
        </button>
        <a href="dashboard.php" class="text-blue-600 hover:underline mt-2 text-sm">← Volver al Panel</a>
      </div>
    </form>
  </div>
</body>
</html>