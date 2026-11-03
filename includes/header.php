<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario de Activos TI - ML Industries</title>
    <link rel="stylesheet" href="assets/css/estilos.css">
</head>
<body>

<header style="background-color: var(--primary); color: #fff; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h2 style="margin: 0; font-size: 20px; color: #fff;">ML Industries - Gestión de Activos TI</h2>
    </div>
    <div style="display: flex; align-items: center; gap: 20px;">
        <span style="font-size: 14px; font-weight: 500;">
            <?= htmlspecialchars($_SESSION['nombre_completo']) ?> (<?= htmlspecialchars($_SESSION['rol']) ?>)
        </span>
        <a href="logout.php" class="btn btn-secondary" style="padding: 6px 12px; font-size: 13px;">Cerrar Sesión</a>
    </div>
</header>

<div style="padding: 30px; max-width: 1300px; margin: 0 auto;">