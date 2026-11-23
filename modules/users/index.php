<?php
$ruta_base = '../../';
require_once $ruta_base . 'config/database.php';
require_once $ruta_base . 'includes/header.php';

if ($_SESSION['rol'] !== 'Administrador') {
    $_SESSION['alerta'] = [
        'icono'  => 'error',
        'titulo' => 'Acceso Denegado',
        'texto'  => 'No tienes permisos para administrar usuarios.'
    ];
    header("Location: " . $ruta_base . "index.php");
    exit();
}

$stmt = $pdo->query("SELECT id_usuario, nombre_completo, usuario, rol FROM usuarios ORDER BY id_usuario ASC");
$usuarios = $stmt->fetchAll();
?>

<div class="card toolbar-card">
    <h2 class="toolbar-title">Administración de Usuarios</h2>
    <a href="nuevo.php" class="btn btn-primary">+ Nuevo Usuario</a>
</div>

<div class="card">
    <div class="tabla-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre Completo</th>
                    <th>Usuario</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $u): ?>
                    <tr>
                        <td><strong><?= $u['id_usuario'] ?></strong></td>
                        <td><?= htmlspecialchars($u['nombre_completo']) ?></td>
                        <td><?= htmlspecialchars($u['usuario']) ?></td>
                        <td>
                            <span class="badge <?= $u['rol'] === 'Administrador' ? 'badge-resguardo' : 'badge-funcional' ?>">
                                <?= htmlspecialchars($u['rol']) ?>
                            </span>
                        </td>
                        <td>
                            <div class="action-group">
                                <a href="editar.php?id=<?= $u['id_usuario'] ?>" class="btn btn-secondary btn-sm">Editar</a>
                                
                                <?php if ($u['id_usuario'] != $_SESSION['usuario_id']): ?>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="confirmarEliminacion(<?= $u['id_usuario'] ?>, '<?= htmlspecialchars($u['usuario'], ENT_QUOTES) ?>')">
                                        Eliminar
                                    </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function confirmarEliminacion(id, nombreUsuario) {
    Swal.fire({
        title: '¿Eliminar usuario?',
        text: `Se borrará la cuenta "${nombreUsuario}". Esta acción no se puede deshacer.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `eliminar.php?id=${id}`;
        }
    });
}
</script>

<?php require_once $ruta_base . 'includes/footer.php'; ?>