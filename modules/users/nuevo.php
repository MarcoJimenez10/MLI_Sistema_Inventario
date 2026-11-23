<?php
$ruta_base = '../../';
require_once $ruta_base . 'config/database.php';
require_once $ruta_base . 'includes/header.php';

if ($_SESSION['rol'] !== 'Administrador') {
    header("Location: " . $ruta_base . "index.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_completo = trim($_POST['nombre_completo']);
    $usuario         = trim($_POST['usuario']);
    $clave           = trim($_POST['clave']);
    $rol             = trim($_POST['rol']);

    if (!empty($nombre_completo) && !empty($usuario) && !empty($clave) && !empty($rol)) {
        $check = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE usuario = :usuario");
        $check->execute(['usuario' => $usuario]);

        if ($check->fetchColumn() > 0) {
            $error = "El nombre de usuario '$usuario' ya está en uso.";
        } else {
            $clave_hash = password_hash($clave, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("INSERT INTO usuarios (nombre_completo, usuario, clave, rol) VALUES (:nombre, :usuario, :clave, :rol)");
            $stmt->execute([
                'nombre'  => $nombre_completo,
                'usuario' => $usuario,
                'clave'   => $clave_hash,
                'rol'     => $rol
            ]);

            $_SESSION['alerta'] = [
                'icono'  => 'success',
                'titulo' => 'Usuario Creado',
                'texto'  => "La cuenta para $nombre_completo se registró correctamente."
            ];

            header("Location: index.php");
            exit();
        }
    } else {
        $error = "Todos los campos son obligatorios.";
    }
}
?>

<div class="card toolbar-card">
    <h2 class="toolbar-title">Registrar Nuevo Usuario</h2>
    <a href="index.php" class="btn btn-secondary">← Volver a Usuarios</a>
</div>

<div class="card">
    <?php if (!empty($error)): ?>
        <div class="alert-error" style="margin-bottom: 16px;"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form action="nuevo.php" method="POST">
        <div class="form-grid">
            <div class="form-group">
                <label for="nombre_completo">Nombre Completo *</label>
                <input type="text" name="nombre_completo" id="nombre_completo" class="form-control" placeholder="Ej. Juan Pérez López" required autofocus>
            </div>

            <div class="form-group">
                <label for="usuario">Nombre de Usuario (Login) *</label>
                <input type="text" name="usuario" id="usuario" class="form-control" placeholder="Ej. juan.perez" required>
            </div>

            <div class="form-group">
                <label for="clave">Contraseña *</label>
                <input type="password" name="clave" id="clave" class="form-control" placeholder="••••••••" required>
            </div>

            <div class="form-group">
                <label for="rol">Rol en el Sistema *</label>
                <select name="rol" id="rol" class="form-control" required>
                    <option value="Operativo">Operativo</option>
                    <option value="Administrador">Administrador</option>
                </select>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Guardar Usuario</button>
            <a href="index.php" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

<?php require_once $ruta_base . 'includes/footer.php'; ?>