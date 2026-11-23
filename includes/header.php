<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$ruta_base = $ruta_base ?? '';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: " . $ruta_base . "login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario de Activos TI - ML Industries</title>
    <link rel="stylesheet" href="<?= $ruta_base ?>assets/css/styles.css">
</head>
<body>

<header class="topbar">
    <div class="topbar-left">
        <img src="<?= $ruta_base ?>assets/img/logo_mli.svg" alt="ML Industries Logo" class="topbar-logo">
        <h2 class="topbar-title">Gestión de Activos TI</h2>
        <nav class="topbar-nav">
            <a href="<?= $ruta_base ?>index.php" class="nav-link">Inventario</a>
            <a href="<?= $ruta_base ?>modules/maintenance/index.php" class="nav-link">Bitácora</a>
            <?php if ($_SESSION['rol'] === 'Administrador'): ?>
                <a href="<?= $ruta_base ?>modules/users/index.php" class="nav-link">Usuarios</a>
            <?php endif; ?>
        </nav>
    </div>

    <div class="topbar-user">
        <span><?= htmlspecialchars($_SESSION['nombre_completo']) ?> (<?= htmlspecialchars($_SESSION['rol']) ?>)</span>
        <a href="<?= $ruta_base ?>logout.php" class="btn btn-secondary btn-sm">Cerrar Sesión</a>
    </div>
</header>

<div class="main-container">