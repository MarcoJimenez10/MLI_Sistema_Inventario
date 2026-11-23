<?php
$ruta_base = '../../';
require_once $ruta_base . 'config/database.php';
require_once $ruta_base . 'includes/header.php';

if ($_SESSION['rol'] !== 'Administrador' || !isset($_GET['id'])) {
    header("Location: " . $ruta_base . "index.php");
    exit();
}

$id_usuario = (int)$_GET['id'];
$error = '';

$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id_usuario = :id LIMIT 1");
$stmt->execute(['id' => $id_usuario]);
$user = $stmt->fetch();

if (!$user) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_completo = trim($_POST['nombre_completo']);
    $usuario         = trim($_POST['usuario']);
    $rol             = trim($_POST['rol']);
    $nueva_clave     = trim($_POST['clave']);

    if (!empty($nombre_completo) && !empty($usuario) && !empty($rol)) {
        if (!empty($nueva_clave)) {
            $clave_hash = password_hash($nueva_clave, PASSWORD_DEFAULT);
            $sql = "UPDATE usuarios SET nombre_completo = :nombre, usuario = :usuario, clave = :clave, rol = :rol WHERE id_usuario = :id";
            $params = ['nombre' => $nombre_completo, 'usuario' => $usuario, 'clave' => $clave_hash, 'rol' => $rol, 'id' => $id_usuario];
        } else {
            $sql = "UPDATE usuarios SET nombre_completo = :nombre, usuario = :usuario, rol = :rol WHERE id_usuario = :id";
            $params = ['nombre' => $nombre_completo, 'usuario' => $usuario, 'rol' => $rol, 'id' => $id_usuario];
        }

        $update = $pdo->prepare($sql);
        $update->execute($params);

        $_SESSION['alerta'] = [
            'icono'  => 'success',
            'titulo' => 'Usuario Actualizado',
            'texto'  => 'Los datos de la cuenta se guardaron correctamente.'
        ];

        header("Location: index.php");
        exit();
    } else {
        $error = "Nombre, usuario y rol son obligatorios.";
    }
}
?>

<div class="card toolbar-card">
    <h2 class="toolbar-title">Editar Usuario: <?= htmlspecialchars($user['usuario']) ?></h2>
    <a href="index.php" class="btn btn-secondary">← Volver a Usuarios</a>
</div>

<div class="card">
    <?php if (!empty($error)): ?>
        <div class="alert-error" style="margin-bottom: 16px;"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form action="editar.php?id=<?= $id_usuario ?>" method="POST">
        <div class="form-grid">
            <div class="form-group">
                <label for="nombre_completo">Nombre Completo *</label>
                <input type="text" name="nombre_completo" id="nombre_completo" class="form-control" value="<?= htmlspecialchars($user['nombre_completo']) ?>" required>
            </div>

            <div class="form-group">
                <label for="usuario">Nombre de Usuario *</label>
                <input type="text" name="usuario" id="usuario" class="form-control" value="<?= htmlspecialchars($user['usuario']) ?>" required>
            </div>

            <div class="form-group">
                <label for="clave">Nueva Contraseña (dejar en blanco para no cambiar)</label>
                <input type="password" name="clave" id="clave" class="form-control" placeholder="••••••••">
            </div>

            <div class="form-group">
                <label for="rol">Rol en el Sistema *</label>
                <select name="rol" id="rol" class="form-control" required>
                    <option value="Operativo" <?= $user['rol'] === 'Operativo' ? 'selected' : '' ?>>Operativo</option>
                    <option value="Administrador" <?= $user['rol'] === 'Administrador' ? 'selected' : '' ?>>Administrador</option>
                </select>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            <a href="index.php" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

<?php require_once $ruta_base . 'includes/footer.php'; ?>