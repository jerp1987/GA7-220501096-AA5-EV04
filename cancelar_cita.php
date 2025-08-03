<?php
session_start();
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_rol'], ['cliente', 'empleado', 'administrador'])) {
    header("Location: index.html");
    exit();
}
$rol = $_SESSION['user_rol'];

$volver_url = match ($rol) {
    'cliente'       => 'modulo_cliente.php',
    'empleado'      => 'modulo_empleado.php',
    'administrador' => 'modulo_administrador.php',
    default         => 'index.html'
};
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cancelar Cita - Taller de Motocicletas</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">
  <div class="max-w-xl mx-auto bg-white mt-10 p-8 rounded-lg shadow-lg">
    <h1 class="text-2xl font-bold text-center text-red-600 mb-6">Cancelar Cita</h1>

    <form id="formCancelarCita" class="space-y-4">
      <input
        type="text"
        name="cedula"
        placeholder="Cédula del cliente"
        required
        class="w-full border px-4 py-2 rounded"
      />

      <textarea
        name="motivo"
        placeholder="Motivo de la cancelación"
        required
        rows="3"
        class="w-full border px-4 py-2 rounded"
      ></textarea>

      <button
        type="submit"
        class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded shadow"
      >
        Cancelar Cita
      </button>
    </form>

    <div id="mensajeCancelar" class="mt-4 text-center text-sm font-medium"></div>

    <div class="text-center mt-6">
      <a href="<?= $volver_url ?>" class="text-blue-600 hover:underline">Volver al panel</a>
    </div>
  </div>

  <script>
    const form = document.getElementById('formCancelarCita');
    const mensaje = document.getElementById('mensajeCancelar');

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const cedula = form.cedula.value.trim();
      const motivo = form.motivo.value.trim();

      if (!cedula || !motivo) {
        mensaje.textContent = '⚠️ Debes completar todos los campos.';
        mensaje.className = 'text-red-600 mt-4 text-center text-sm font-medium';
        return;
      }

      try {
        const res = await fetch('citas.php', {
          method: 'DELETE',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ cedula, motivo })
        });

        const data = await res.json();
        mensaje.textContent = data.message;
        mensaje.className = `mt-4 text-center text-sm font-medium ${
          data.success ? 'text-green-600' : 'text-red-600'
        }`;

        if (data.success) form.reset();
      } catch (err) {
        mensaje.textContent = '❌ Error al conectar con el servidor.';
        mensaje.className = 'mt-4 text-center text-sm font-medium text-red-600';
      }
    });
  </script>
</body>
</html>