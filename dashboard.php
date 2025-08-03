<?php
session_start();
require_once 'conexion.php';
header('Content-Type: text/html; charset=UTF-8');

if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] !== 'administrador') {
    header("Location: index.html");
    exit();
}

$sql = "SELECT id, nombre, correo, rol FROM usuarios";
$resultado = $conexion->query($sql);

$userName = htmlspecialchars($_SESSION['user_name'], ENT_QUOTES, 'UTF-8');
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Panel de Administración</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6 font-sans">
  <div class="max-w-6xl mx-auto bg-white p-8 rounded-lg shadow-md">
    <h1 class="text-3xl font-bold text-green-700 mb-4 text-center">Panel de Administración</h1>
    <p class="mb-6 text-gray-700 text-center">Bienvenido, <strong class="text-green-800"><?= $userName ?></strong></p>

    <!-- Navegación -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10 text-center">
      <a href="asignar_cita.php" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Asignar Cita</a>
      <a href="cancelar_cita.php" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Cancelar Cita</a>
      <a href="listar_citas.php" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Listado de Citas</a>
      <a href="listar_facturas.php" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">Ver Facturas</a>
    </div>

    <!-- Sección de usuarios -->
    <h2 class="text-2xl font-semibold text-gray-800 mb-4">Gestión de Usuarios</h2>
    <div class="overflow-x-auto">
      <table class="min-w-full bg-white border text-sm">
        <thead class="bg-gray-200 text-gray-700">
          <tr>
            <th class="border px-4 py-2">ID</th>
            <th class="border px-4 py-2">Nombre</th>
            <th class="border px-4 py-2">Correo</th>
            <th class="border px-4 py-2">Rol</th>
            <th class="border px-4 py-2">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php while($usuario = $resultado->fetch_assoc()): ?>
          <tr class="text-gray-800">
            <td class="border px-4 py-2"><?= $usuario['id'] ?></td>
            <td class="border px-4 py-2"><?= htmlspecialchars($usuario['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
            <td class="border px-4 py-2"><?= htmlspecialchars($usuario['correo'], ENT_QUOTES, 'UTF-8') ?></td>
            <td class="border px-4 py-2 capitalize"><?= $usuario['rol'] ?></td>
            <td class="border px-4 py-2 space-x-2">
              <a href="editar_usuario.php?id=<?= $usuario['id'] ?>" class="bg-yellow-500 hover:bg-yellow-600 text-white py-1 px-2 rounded">Editar</a>
              <a href="eliminar_usuario.php?id=<?= $usuario['id'] ?>" class="bg-red-600 hover:bg-red-700 text-white py-1 px-2 rounded" onclick="return confirm('¿Estás seguro de eliminar este usuario?')">Eliminar</a>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>

    <div class="text-center mt-6">
      <a href="logout.php" class="text-sm text-gray-500 hover:underline">Cerrar sesión</a>
    </div>
  </div>
</body>
</html>
<?php $conexion->close(); ?>