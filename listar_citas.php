<?php
session_start();
require_once 'conexion.php';

// ── Detección de modo JSON ─────────────────────────────────────────────────────
$accept   = $_SERVER['HTTP_ACCEPT'] ?? '';
$wantJson = (isset($_GET['format']) && $_GET['format'] === 'json')
         || isset($_GET['api'])
         || stripos($accept, 'application/json') !== false;

// ── Token API (bypass de sesión solo para modo API/JSON) ──────────────────────
$apiToken       = $_GET['api_token'] ?? '';
$apiTokenValido = ($apiToken === 'dev-token-123'); // cámbialo si quieres

// ── Autorización ──────────────────────────────────────────────────────────────
$sesionValida = (isset($_SESSION['user_id']) && in_array($_SESSION['user_rol'], ['cliente','empleado','administrador']));

if (!$sesionValida && !$apiTokenValido) {
    if ($wantJson) {
        header('Content-Type: application/json; charset=UTF-8');
        http_response_code(401);
        echo json_encode(["success" => false, "message" => "No autorizado. Inicia sesión o usa api_token."]);
        exit();
    } else {
        header("Location: index.html");
        exit();
    }
}

// Si hay sesión, usamos sus datos; con token asumimos acceso API genérico
$userRol   = $_SESSION['user_rol']   ?? 'api';
$userName  = htmlspecialchars($_SESSION['user_name'] ?? 'API', ENT_QUOTES, 'UTF-8');
$userEmail = $_SESSION['user_email'] ?? '';

// ── Consulta según rol (LEFT JOIN con facturas) ───────────────────────────────
if ($sesionValida && $userRol === 'cliente') {
    $stmt = $conexion->prepare("
        SELECT c.*, f.id AS factura_id
        FROM citas c
        LEFT JOIN facturas f ON f.cita_id = c.id
        WHERE c.correo = ?
        ORDER BY c.fecha DESC
    ");
    $stmt->bind_param("s", $userEmail);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conexion->query("
        SELECT c.*, f.id AS factura_id
        FROM citas c
        LEFT JOIN facturas f ON f.cita_id = c.id
        ORDER BY c.fecha DESC
    ");
}

// ── Normalización de datos (sirve para JSON y HTML) ───────────────────────────
$citas = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $citas[] = [
            'id'          => (int)$row['id'],
            'nombres'     => $row['nombres'],
            'apellidos'   => $row['apellidos'],
            'cedula'      => $row['cedula'],
            'correo'      => $row['correo'],
            'celular'     => $row['celular'],
            'servicios'   => $row['servicios'],
            'descripcion' => $row['descripcion'],
            'fecha'       => $row['fecha'],
            'factura_id'  => isset($row['factura_id']) ? (int)$row['factura_id'] : null,
        ];
    }
}

// ── Respuesta API (JSON) ──────────────────────────────────────────────────────
if ($wantJson) {
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode([
        "success" => true,
        "count"   => count($citas),
        "data"    => $citas
    ], JSON_UNESCAPED_UNICODE);
    $conexion->close();
    exit();
}

// ── Vista HTML (modo por defecto con sesión) ──────────────────────────────────
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
      <?= ($sesionValida && $userRol === 'cliente') ? 'Mis Citas Agendadas' : 'Citas Registradas' ?>
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
          <?php if (count($citas) > 0): ?>
            <?php foreach ($citas as $cita): ?>
              <tr>
                <td class="border px-4 py-2"><?= $cita['id'] ?></td>
                <td class="border px-4 py-2"><?= htmlspecialchars($cita['nombres'].' '.$cita['apellidos']) ?></td>
                <td class="border px-4 py-2"><?= htmlspecialchars($cita['cedula']) ?></td>
                <td class="border px-4 py-2"><?= htmlspecialchars($cita['servicios']) ?></td>
                <td class="border px-4 py-2"><?= date('d/m/Y H:i', strtotime($cita['fecha'])) ?></td>
                <td class="border px-4 py-2 text-center">
                  <?php if (!empty($cita['factura_id'])): ?>
                    <a href="factura.php?id=<?= $cita['factura_id'] ?>" target="_blank"
                       class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm">
                      Ver Factura
                    </a>
                  <?php else: ?>
                    <span class="text-gray-500">Sin factura</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="6" class="px-4 py-4 text-center text-gray-500">No hay citas registradas.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <?php if ($sesionValida): ?>
      <div class="mt-6 text-center">
        <a href="<?=
          $userRol === 'cliente' ? 'modulo_cliente.php' :
          ($userRol === 'empleado' ? 'modulo_empleado.php' : 'modulo_administrador.php');
        ?>" class="text-blue-600 hover:underline">
          Volver al panel
        </a>
      </div>
    <?php endif; ?>
  </div>
</body>
</html>
<?php $conexion->close(); ?>