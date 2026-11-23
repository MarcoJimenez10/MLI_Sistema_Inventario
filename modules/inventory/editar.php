<?php
$ruta_base = '../../';
require_once $ruta_base . 'config/database.php';
require_once $ruta_base . 'includes/header.php';

if (!isset($_GET['id']) || empty(trim($_GET['id']))) {
    header("Location: " . $ruta_base . "index.php");
    exit();
}

$id_activo = trim($_GET['id']);
$error = '';

$stmt = $pdo->prepare("SELECT * FROM activos WHERE id_activo = :id LIMIT 1");
$stmt->execute(['id' => $id_activo]);
$activo = $stmt->fetch();

if (!$activo) {
    $_SESSION['alerta'] = [
        'icono'  => 'error',
        'titulo' => 'Activo no encontrado',
        'texto'  => "No existe ningún equipo registrado con el folio $id_activo."
    ];
    header("Location: " . $ruta_base . "index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo_equipo      = trim($_POST['tipo_equipo']);
    $marca            = trim($_POST['marca']);
    $modelo           = trim($_POST['modelo']);
    $numero_serie     = trim($_POST['numero_serie']);
    $ubicacion        = trim($_POST['ubicacion']);
    $usuario_asignado = trim($_POST['usuario_asignado']);
    $direccion_ip     = trim($_POST['direccion_ip']);
    $estado           = trim($_POST['estado']);
    $comentarios      = trim($_POST['comentarios']);

    if (!empty($tipo_equipo) && !empty($marca) && !empty($modelo) && !empty($estado)) {
        try {
            $sql = "UPDATE activos SET 
                        tipo_equipo      = :tipo_equipo,
                        marca            = :marca,
                        modelo           = :modelo,
                        numero_serie     = :numero_serie,
                        ubicacion        = :ubicacion,
                        usuario_asignado = :usuario_asignado,
                        direccion_ip     = :direccion_ip,
                        estado           = :estado,
                        comentarios      = :comentarios
                    WHERE id_activo = :id_activo";

            $update = $pdo->prepare($sql);
            $update->execute([
                'tipo_equipo'      => $tipo_equipo,
                'marca'            => $marca,
                'modelo'           => $modelo,
                'numero_serie'     => $numero_serie ?: null,
                'ubicacion'        => $ubicacion ?: null,
                'usuario_asignado' => $usuario_asignado ?: null,
                'direccion_ip'     => $direccion_ip ?: null,
                'estado'           => $estado,
                'comentarios'      => $comentarios ?: null,
                'id_activo'        => $id_activo
            ]);

            $_SESSION['alerta'] = [
                'icono'  => 'success',
                'titulo' => '¡Cambios Guardados!',
                'texto'  => "La información del activo $id_activo se actualizó correctamente."
            ];

            header("Location: " . $ruta_base . "index.php");
            exit();
        } catch (\PDOException $e) {
            $error = "Error al actualizar el activo: " . $e->getMessage();
        }
    } else {
        $error = "Por favor, completa todos los campos obligatorios (*).";
    }
}
?>

<div class="card toolbar-card">
    <h2 class="toolbar-title">Editar Activo: <?= htmlspecialchars($activo['id_activo']) ?></h2>
    <a href="<?= $ruta_base ?>index.php" class="btn btn-secondary">← Volver al Inventario</a>
</div>

<div class="card">
    <?php if (!empty($error)): ?>
        <div class="alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form action="editar.php?id=<?= urlencode($id_activo) ?>" method="POST">
        <div class="form-grid">
            <div class="form-group">
                <label for="id_activo">ID (No editable)</label>
                <input type="text" id="id_activo" class="form-control" value="<?= htmlspecialchars($activo['id_activo']) ?>" disabled>
            </div>

            <div class="form-group">
                <label for="tipo_equipo">Tipo de Equipo *</label>
                <select name="tipo_equipo" id="tipo_equipo" class="form-control" required>
                    <?php
                    $tipos = ['Laptop', 'Desktop', 'Monitor', 'Impresora', 'Escáner', 'Switch / Red', 'Otro'];
                    foreach ($tipos as $t) {
                        $selected = ($activo['tipo_equipo'] === $t) ? 'selected' : '';
                        echo "<option value=\"$t\" $selected>$t</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="marca">Marca *</label>
                <select name="marca" id="marca" class="form-control" required>
                    <option value="">Selecciona una opción...</option>
                    <option value="HP">HP</option>
                    <option value="Dell">Dell</option>
                    <option value="Lenovo">Lenovo</option>
                    <option value="Zebra">Zebra</option>
                    <option value="Acer">Acer</option>
                    <option value="Unifi">Unifi</option>
                    <option value="Epson">Epson</option>
                    <option value="Asus">Asus</option>
                    <option value="Otro">Otro</option>
                </select>
            </div>

            <div class="form-group">
                <label for="modelo">Modelo *</label>
                <input type="text" name="modelo" id="modelo" class="form-control" value="<?= htmlspecialchars($activo['modelo']) ?>" required>
            </div>

            <div class="form-group">
                <label for="numero_serie">Número de Serie</label>
                <input type="text" name="numero_serie" id="numero_serie" class="form-control" value="<?= htmlspecialchars($activo['numero_serie'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="estado">Estado Operativo *</label>
                <select name="estado" id="estado" class="form-control" required>
                    <?php
                    $estados = ['Funcional', 'En Resguardo', 'En Mantenimiento', 'Dañado / Scrap'];
                    foreach ($estados as $est) {
                        $selected = ($activo['estado'] === $est) ? 'selected' : '';
                        echo "<option value=\"$est\" $selected>$est</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="ubicacion">Ubicación / Área</label>
                <select name="ubicacion" id="ubicacion" class="form-control" required>
                    <option value="">Selecciona una opción...</option>
                    <option value="Sistemas">Sistemas</option>
                    <option value="Logistica">Logistica</option>
                    <option value="MPS">MPS</option>
                    <option value="RH">RH</option>
                    <option value="Safety">Safety</option>
                    <option value="Entrenamiento">Entrenamiento</option>
                    <option value="Almacén">Almacén Rollos</option>
                    <option value="Corte">Corte</option>
                    <option value="Producción">Producción</option>
                    <option value="Mantenimiento">Mantenimiento</option>
                    <option value="Ingenieria">Ingenieria</option>
                    <option value="Mecanicos">Mecanicos</option>
                    <option value="Nominas">Nominas</option>
                </select>
            </div>

            <div class="form-group">
                <label for="usuario_asignado">Usuario Asignado</label>
                <input type="text" name="usuario_asignado" id="usuario_asignado" class="form-control" value="<?= htmlspecialchars($activo['usuario_asignado'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="direccion_ip">Dirección IP</label>
                <input type="text" name="direccion_ip" id="direccion_ip" class="form-control" value="<?= htmlspecialchars($activo['direccion_ip'] ?? '') ?>">
            </div>

            <div class="form-group full-width">
                <label for="comentarios">Observaciones / Detalles Físicos</label>
                <textarea name="comentarios" id="comentarios" rows="3" class="form-control"><?= htmlspecialchars($activo['comentarios'] ?? '') ?></textarea>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            <a href="<?= $ruta_base ?>index.php" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

<?php require_once $ruta_base . 'includes/footer.php'; ?>