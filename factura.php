<?php
session_start();
require_once 'conexion.php';
header('Content-Type: text/html; charset=UTF-8');

// Verificar sesión válida
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_rol'], ['cliente', 'empleado', 'administrador'])) {
    echo "<p style='color:red; text-align:center;'>🚫 Acceso denegado. Debes iniciar sesión.</p>";
    exit();
}

$rol = $_SESSION['user_rol'];
$volver_url = match ($rol) {
    'cliente'       => 'modulo_cliente.php',
    'empleado'      => 'modulo_empleado.php',
    'administrador' => 'listar_citas.php',
    default         => 'index.html'
};

// Validar ID recibido
$id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int) $_GET['id'] : null;
if (!$id) {
    echo "<p style='color:red; text-align:center;'>ID de cita no proporcionado o no válido.</p>";
    exit();
}

// Consultar la cita
$stmt = $conexion->prepare("SELECT * FROM citas WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    echo "<p style='color:red; text-align:center;'>No se encontró la cita.</p>";
    exit();
}

$cita = $resultado->fetch_assoc();
$stmt->close();

// Calcular valores
$precio_base = 50000;
$servicios = array_map('trim', explode(",", $cita['servicios']));
$subtotal = $precio_base * count($servicios);
$iva = $subtotal * 0.19;
$total = $subtotal + $iva;
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Factura de Servicio</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    @media print {
      .no-print { display: none; }
    }
  </style>
</head>
<body class="bg-gray-100 p-6 font-sans">
  <div class="max-w-2xl mx-auto bg-white p-8 rounded shadow">
    <h1 class="text-2xl font-bold text-center text-gray-800 mb-6">Factura de Servicio</h1>

    <div class="mb-6">
      <p><strong>Cliente:</strong> <?= htmlspecialchars($cita['nombres'] . ' ' . $cita['apellidos']) ?></p>
      <p><strong>Cédula:</strong> <?= htmlspecialchars($cita['cedula']) ?></p>
      <p><strong>Correo:</strong> <?= htmlspecialchars($cita['correo']) ?></p>
      <p><strong>Fecha:</strong> <?= date('d/m/Y H:i', strtotime($cita['fecha'])) ?></p>
    </div>

    <h2 class="text-lg font-semibold text-gray-700 mb-2">Servicios Realizados:</h2>
    <ul class="list-disc pl-5 text-gray-800 mb-6">
      <?php foreach ($servicios as $s): ?>
        <li><?= htmlspecialchars($s) ?></li>
      <?php endforeach; ?>
    </ul>

    <div class="text-right text-gray-800">
      <p><strong>Subtotal:</strong> $<?= number_format($subtotal, 0, ',', '.') ?></p>
      <p><strong>IVA (19%):</strong> $<?= number_format($iva, 0, ',', '.') ?></p>
      <p class="text-xl font-bold mt-2">Total: $<?= number_format($total, 0, ',', '.') ?></p>
    </div>

    <div class="text-center mt-8 no-print">
      <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
        Imprimir / Guardar PDF
      </button>
      <a href="<?= $volver_url ?>" class="ml-4 text-blue-600 hover:underline">Volver</a>
    </div>

    <div class="text-center mt-4 no-print">
      <button onclick="window.close()" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
        Cerrar ventana
      </button>
    </div>
  </div>
</body>
</html>