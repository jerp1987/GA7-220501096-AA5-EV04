<?php
require_once 'conexion.php';

if (!isset($_GET['id'])) {
    echo "ID de usuario no proporcionado.";
    exit();
}

$id = intval($_GET['id']);
$sql = "SELECT * FROM usuarios WHERE id = $id";
$result = $conexion->query($sql);

if (!$result || $result->num_rows === 0) {
    echo "Usuario no encontrado.";
    exit();
}

$usuario = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar Usuario</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
  <div class="max-w-xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl mb-4 text-center text-blue-600 font-bold">Editar Usuario</h1>
    <form action="guardar_usuario.php" method="POST" class="space-y-4">
      <input type="hidden" name="id" value="<?= $usuario['id'] ?>">
      <div>
        <label>Nombre:</label>
        <input type="text" name="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>" required class="w-full border p-2 rounded" />
      </div>
      <div>
        <label>Correo:</label>
        <input type="email" name="correo" value="<?= htmlspecialchars($usuario['correo']) ?>" required class="w-full border p-2 rounded" />
      </div>
      <div>
        <label>Rol:</label>
        <select name="rol" class="w-full border p-2 rounded">
          <option value="administrador" <?= $usuario['rol'] == 'administrador' ? 'selected' : '' ?>>Administrador</option>
          <option value="empleado" <?= $usuario['rol'] == 'empleado' ? 'selected' : '' ?>>Empleado</option>
          <option value="cliente" <?= $usuario['rol'] == 'cliente' ? 'selected' : '' ?>>Cliente</option>
        </select>
      </div>
      <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Guardar Cambios</button>
    </form>
    <div class="text-center mt-4">
      <a href="dashboard.php" class="text-sm text-gray-600 hover:underline">← Volver al panel</a>
    </div>
  </div>
</body>
</html>