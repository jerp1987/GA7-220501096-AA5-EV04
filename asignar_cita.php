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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Asignar Cita - Taller de Motocicletas</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">
  <div class="max-w-3xl mx-auto bg-white mt-10 p-8 rounded-lg shadow-lg">
    <h1 class="text-2xl font-bold text-center text-green-700 mb-6">Asignación de Cita</h1>

    <form id="formAsignarCita" class="space-y-4">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <input type="text" name="nombres" placeholder="Nombres" required class="border px-4 py-2 rounded" />
        <input type="text" name="apellidos" placeholder="Apellidos" required class="border px-4 py-2 rounded" />
        <input type="text" name="cedula" placeholder="Cédula" required class="border px-4 py-2 rounded" />
        <input type="email" name="correo" placeholder="Correo electrónico" required class="border px-4 py-2 rounded" />
        <input type="tel" name="celular" placeholder="Celular" required class="border px-4 py-2 rounded" />
      </div>

      <label class="block text-sm font-semibold text-gray-700 mt-4">Selecciona los servicios requeridos:</label>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
        <label><input type="checkbox" name="servicios[]" value="Mecánica general" /> Mecánica general</label>
        <label><input type="checkbox" name="servicios[]" value="Electricidad y electrónica" /> Electricidad y electrónica</label>
        <label><input type="checkbox" name="servicios[]" value="Mantenimiento preventivo" /> Mantenimiento preventivo</label>
        <label><input type="checkbox" name="servicios[]" value="Diagnóstico especializado" /> Diagnóstico especializado</label>
        <label><input type="checkbox" name="servicios[]" value="Reparación de carrocería y pintura" /> Reparación de carrocería y pintura</label>
        <label><input type="checkbox" name="servicios[]" value="Restauración de motos antiguas" /> Restauración de motos antiguas</label>
        <label><input type="checkbox" name="servicios[]" value="Personalización" /> Personalización</label>
        <label><input type="checkbox" name="servicios[]" value="Venta de repuestos y accesorios" /> Venta de repuestos y accesorios</label>
        <label><input type="checkbox" name="servicios[]" value="Asesoramiento técnico" /> Asesoramiento técnico</label>
        <label><input type="checkbox" name="servicios[]" value="Servicios adicionales" /> Servicios adicionales</label>
      </div>

      <textarea name="descripcion" placeholder="Descripción adicional del servicio" class="w-full border px-4 py-2 rounded" rows="3"></textarea>

      <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded shadow">
        Agendar Cita
      </button>
    </form>

    <div id="mensajeCita" class="mt-4 text-center text-sm font-medium"></div>
    <div class="text-center mt-6">
      <a href="<?= $volver_url ?>" class="text-blue-600 hover:underline">Volver al panel</a>
    </div>
  </div>

  <script>
    const form = document.getElementById('formAsignarCita');
    const mensaje = document.getElementById('mensajeCita');

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const formData = new FormData(form);
      const serviciosSeleccionados = formData.getAll('servicios[]');

      if (serviciosSeleccionados.length === 0) {
        mensaje.textContent = '⚠️ Debes seleccionar al menos un servicio.';
        mensaje.className = 'text-red-600 mt-4 text-center text-sm font-medium';
        return;
      }

      formData.set('servicios', serviciosSeleccionados.join(', '));
      formData.delete('servicios[]');

      try {
        const res = await fetch('citas.php', {
          method: 'POST',
          body: formData
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