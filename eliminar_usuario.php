<?php
require_once 'conexion.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "DELETE FROM usuarios WHERE id = $id";

    if ($conexion->query($sql)) {
        header("Location: dashboard.php?msg=Usuario eliminado correctamente");
        exit();
    } else {
        header("Location: dashboard.php?error=No se pudo eliminar el usuario");
        exit();
    }
} else {
    header("Location: dashboard.php?error=ID no proporcionado");
    exit();
}
?>