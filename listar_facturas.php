<?php
session_start();
require_once 'conexion.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_rol'], ['empleado', 'administrador'])) {
    header("Location: index.html");
    exit();
}

$userName = htmlspecialchars($_SESSION['user_name'], ENT_QUOTES, 'UTF-8');
$rol = $_SESSION['user_rol'];
$volver_url = $rol === 'administrador' ? 'modulo_administrador.php' : 'modulo_empleado.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Listado de Facturas</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    @media print {
      .no-print { display: none; }
    }
  </style>
</head>
<body class="bg-gray-100 p-6 font-sans">
  <div class="max-w-6xl mx-auto bg-white p-8 rounded-lg shadow">
    <h1 class="text-2xl font-bold text-center text-indigo-700 mb-2">Facturas Generadas</h1>
    <p class="text-center text-gray-600 mb-6">Bienvenido, <strong><?= $userName ?></strong></p>

    <div class="overflow-x-auto">
      <table class="min-w-full text-sm border">
        <thead class="bg-indigo-600 text-white">
          <tr>
            <th class="px-4 py-2 border">ID</th>
            <th class="px-4 py-2 border">Cliente</th>
            <th class="px-4 py-2 border">Servicios</th>
            <th class="px-4 py-2 border">Subtotal</th>
            <th class="px-4 py-2 border">IVA</th>
            <th class="px-4 py-2 border">Total</th>
            <th class="px-4 py-2 border">Acción</th>
          </tr>
        </thead>
        <tbody class="bg-white text-gray-700">
          <?php
          $sql = "SELECT * FROM facturas ORDER BY id DESC";
          $result = $conexion->query($sql);
          if ($result && $result->num_rows > 0):
              while ($row = $result->fetch_assoc()):
                  $id        = $row['id'] ?? '-';
                  $cliente   = htmlspecialchars($row['cliente'] ?? '-');
                  $servicios = htmlspecialchars($row['servicios'] ?? '-');
                  $subtotal  = number_format($row['subtotal'] ?? 0, 0, ',', '.');
                  $iva       = number_format($row['iva'] ?? 0, 0, ',', '.');
                  $total     = number_format($row['total'] ?? 0, 0, ',', '.');
          ?>
            <tr>
              <td class="border px-4 py-2"><?= $id ?></td>
              <td class="border px-4 py-2"><?= $cliente ?></td>
              <td class="border px-4 py-2"><?= $servicios ?></td>
              <td class="border px-4 py-2">$<?= $subtotal ?></td>
              <td class="border px-4 py-2">$<?= $iva ?></td>
              <td class="border px-4 py-2 font-semibold">$<?= $total ?></td>
              <td class="border px-4 py-2 text-center">
                <a href="factura.php?id=<?= $id ?>" target="_blank"
                   class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm">
                  Ver / Imprimir
                </a>
              </td>
            </tr>
          <?php endwhile; else: ?>
            <tr>
              <td colspan="7" class="px-4 py-4 text-center text-gray-500">No hay facturas registradas.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <div class="mt-6 text-center no-print">
      <a href="<?= $volver_url ?>" class="text-blue-600 hover:underline">Volver al panel</a>
    </div>
  </div>
</body>
</html>
<?php $conexion->close(); ?>