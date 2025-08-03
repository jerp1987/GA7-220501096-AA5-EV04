<?php
session_start();
require_once 'conexion.php';
header('Content-Type: text/html; charset=UTF-8');

// Validar sesión
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_rol'], ['cliente', 'empleado', 'administrador'])) {
    header("Location: index.html");
    exit();
}

$userRol = $_SESSION['user_rol'];
$userName = htmlspecialchars($_SESSION['user_name'], ENT_QUOTES, 'UTF-8');
$userEmail = $_SESSION['user_email'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Listado de Citas</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans p-6">
  <div class="max-w-6xl mx-auto bg-white p-8 rounded-lg shadow">
    <h1 class="text-2xl font-bold text-center text-indigo-700 mb-2">
      <?= $userRol === 'cliente' ? 'Mis Citas Agendadas' : 'Citas Registradas' ?>
    </h1>
    <p class="text-center text-gray-600 mb-6">Bienvenido, <strong><?= $userName ?></strong></p>

    <div class="overflow-x-auto">
      <table class="min-w-full text-sm border">
        <thead class="bg-indigo-600 text-white">
          <tr>
            <th class="px-4 py-2 border">ID</th>
            <th class="px-4 py-2 border">Cliente</th>
            <th class="px-4 py-2 border">Cédula</th>
            <th class="px-4 py-2 border">Servicios</th>
            <th class="px-4 py-2 border">Fecha</th>
            <th class="px-4 py-2 border">Acción</th>
          </tr>
        </thead>
        <tbody class="bg-white text-gray-700">
          <?php
          if ($userRol === 'cliente') {
              $stmt = $conexion->prepare("SELECT * FROM citas WHERE correo = ? ORDER BY fecha DESC");
              $stmt->bind_param("s", $userEmail);
              $stmt->execute();
              $result = $stmt->get_result();
          } else {
              $result = $conexion->query("SELECT * FROM citas ORDER BY fecha DESC");
          }

          if ($result && $result->num_rows > 0):
              while ($row = $result->fetch_assoc()):
          ?>
            <tr>
              <td class="border px-4 py-2"><?= $row['id'] ?></td>
              <td class="border px-4 py-2"><?= htmlspecialchars($row['nombres'] . ' ' . $row['apellidos']) ?></td>
              <td class="border px-4 py-2"><?= htmlspecialchars($row['cedula']) ?></td>
              <td class="border px-4 py-2"><?= htmlspecialchars($row['servicios']) ?></td>
              <td class="border px-4 py-2"><?= date('d/m/Y H:i', strtotime($row['fecha'])) ?></td>
              <td class="border px-4 py-2 text-center">
                <a href="factura.php?id=<?= $row['id'] ?>" target="_blank"
                   class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm">
                  Ver Factura
                </a>
              </td>
            </tr>
          <?php endwhile; else: ?>
            <tr>
              <td colspan="6" class="px-4 py-4 text-center text-gray-500">No hay citas registradas.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <div class="mt-6 text-center">
      <a href="<?=
        $userRol === 'cliente' ? 'modulo_cliente.php' :
        ($userRol === 'empleado' ? 'modulo_empleado.php' : 'modulo_administrador.php');
      ?>" class="text-blue-600 hover:underline">
        Volver al panel
      </a>
    </div>
  </div>
</body>
</html>
<?php $conexion->close(); ?>