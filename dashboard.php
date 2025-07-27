<?php
session_start();
require_once 'conexion.php';
header('Content-Type: text/html; charset=UTF-8');

$isJsonRequest = isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false;

if (!isset($_SESSION['user_id']) && !$isJsonRequest) {
    header("Location: index.html");
    exit();
}

$sql = "SELECT id, nombre, correo FROM usuarios";
$resultado = $conexion->query($sql);

if ($isJsonRequest) {
    $usuarios = [];

    if ($resultado && $resultado->num_rows > 0) {
        while ($fila = $resultado->fetch_assoc()) {
            $usuarios[] = $fila;
        }
        echo json_encode(["success" => true, "usuarios" => $usuarios], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(["success" => false, "message" => "No hay usuarios registrados."], JSON_UNESCAPED_UNICODE);
    }
    $conexion->close();
    exit();
}

$userName = htmlspecialchars($_SESSION['user_name'], ENT_QUOTES, 'UTF-8');
$resultado = $conexion->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Usuarios</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6 font-sans">
    <div class="max-w-5xl mx-auto bg-white p-8 rounded-lg shadow-md">
        <h1 class="text-3xl font-bold text-green-700 mb-6 text-center">Panel de Control</h1>
        <p class="mb-4 text-gray-700">Bienvenido, <strong class="text-green-800"><?= $userName ?></strong></p>

        <h2 class="text-xl font-semibold text-gray-800 mb-4">Lista de Usuarios Registrados</h2>

        <div class="overflow-x-auto">
            <table class="min-w-full border text-sm text-left text-gray-600">
                <thead class="bg-green-600 text-white">
                    <tr>
                        <th class="px-4 py-2 border">ID</th>
                        <th class="px-4 py-2 border">Nombre</th>
                        <th class="px-4 py-2 border">Correo</th>
                        <th class="px-4 py-2 border text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    <?php if ($resultado && $resultado->num_rows > 0): ?>
                        <?php while ($fila = $resultado->fetch_assoc()): ?>
                            <tr class="hover:bg-gray-100">
                                <td class="px-4 py-2 border"><?= htmlspecialchars($fila['id']) ?></td>
                                <td class="px-4 py-2 border" id="nombre-<?= $fila['id'] ?>"><?= htmlspecialchars($fila['nombre']) ?></td>
                                <td class="px-4 py-2 border" id="correo-<?= $fila['id'] ?>"><?= htmlspecialchars($fila['correo']) ?></td>
                                <td class="px-4 py-2 border text-center space-x-2">
                                    <button onclick="mostrarFormularioEditar(<?= $fila['id'] ?>)" class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600 text-sm">Editar</button>
                                    <button onclick="eliminarUsuario(<?= $fila['id'] ?>)" class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700 text-sm">Eliminar</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="px-4 py-4 text-center text-gray-500">No hay usuarios registrados.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Formulario para editar usuario -->
        <div id="formularioEditar" class="mt-6 hidden">
            <h3 class="text-lg font-bold text-gray-800 mb-2">Editar Usuario</h3>
            <form id="editarForm" class="space-y-4">
                <input type="hidden" id="editarId">
                <input type="text" id="editarNombre" placeholder="Nombre" class="w-full border rounded px-4 py-2">
                <input type="email" id="editarCorreo" placeholder="Correo" class="w-full border rounded px-4 py-2">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Guardar Cambios</button>
                <button type="button" onclick="cancelarEdicion()" class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500">Cancelar</button>
            </form>
        </div>

        <div class="mt-6 text-center">
            <a href="logout.php" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition">Cerrar Sesión</a>
        </div>
    </div>

    <script>
    function eliminarUsuario(id) {
        if (!confirm(`¿Estás seguro de eliminar el usuario con ID ${id}?`)) return;

        fetch('usuarios.php', {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id })
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            if (data.success) location.reload();
        })
        .catch(() => alert('Error al conectar con el servidor.'));
    }

    function mostrarFormularioEditar(id) {
        const nombre = document.getElementById(`nombre-${id}`).innerText;
        const correo = document.getElementById(`correo-${id}`).innerText;

        document.getElementById('editarId').value = id;
        document.getElementById('editarNombre').value = nombre;
        document.getElementById('editarCorreo').value = correo;

        document.getElementById('formularioEditar').classList.remove('hidden');
        window.scrollTo(0, document.body.scrollHeight);
    }

    function cancelarEdicion() {
        document.getElementById('formularioEditar').classList.add('hidden');
    }

    document.getElementById('editarForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const id = document.getElementById('editarId').value;
        const nombre = document.getElementById('editarNombre').value;
        const correo = document.getElementById('editarCorreo').value;

        fetch('usuarios.php', {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id, nombre, correo })
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            if (data.success) location.reload();
        })
        .catch(() => alert('Error al conectar con el servidor.'));
    });
    </script>
</body>
</html>
<?php $conexion->close(); ?>
