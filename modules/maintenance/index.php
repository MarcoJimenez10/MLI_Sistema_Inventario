<?php
$ruta_base = '../../';
require_once $ruta_base . 'config/database.php';
require_once $ruta_base . 'includes/header.php';

$id_activo = isset($_GET['id']) ? trim($_GET['id']) : '';
$error = '';
$activo = null;

if (!empty($id_activo)) {
    $stmt = $pdo->prepare("SELECT * FROM activos WHERE id_activo = :id LIMIT 1");
    $stmt->execute(['id' => $id_activo]);
    $activo = $stmt->fetch();

    if (!$activo) {
        $_SESSION['alerta'] = [
            'icono'  => 'error',
            'titulo' => 'Activo no encontrado',
            'texto'  => "No existe el folio $id_activo."
        ];
        header("Location: " . $ruta_base . "index.php");
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($id_activo)) {
    $tipo_evento  = trim($_POST['tipo_evento']);
    $descripcion  = trim($_POST['descripcion']);
    $nuevo_estado = trim($_POST['nuevo_estado']);
    $id_usuario   = $_SESSION['usuario_id'];

    if (!empty($tipo_evento) && !empty($descripcion)) {
        try {
            $pdo->beginTransaction();

            $sql = "INSERT INTO bitacora_mantenimiento (id_activo, id_usuario, tipo_evento, descripcion) 
                    VALUES (:id_activo, :id_usuario, :tipo_evento, :descripcion)";
            $insert = $pdo->prepare($sql);
            $insert->execute([
                'id_activo'   => $id_activo,
                'id_usuario'  => $id_usuario,
                'tipo_evento' => $tipo_evento,
                'descripcion' => $descripcion
            ]);

            if (!empty($nuevo_estado) && $nuevo_estado !== $activo['estado']) {
                $upd = $pdo->prepare("UPDATE activos SET estado = :estado WHERE id_activo = :id");
                $upd->execute(['estado' => $nuevo_estado, 'id' => $id_activo]);
            }

            $pdo->commit();

            $_SESSION['alerta'] = [
                'icono'  => 'success',
                'titulo' => 'Bitácora Actualizada',
                'texto'  => 'El registro técnico se guardó en el historial del equipo.'
            ];

            header("Location: index.php?id=" . urlencode($id_activo));
            exit();
        } catch (\PDOException $e) {
            $pdo->rollBack();
            $error = "Error al guardar en bitácora: " . $e->getMessage();
        }
    } else {
        $error = "Por favor, selecciona el tipo de evento y describe el diagnóstico.";
    }
}

if (!empty($id_activo)) {
    $sqlHist = "SELECT b.*, u.nombre_completo 
                FROM bitacora_mantenimiento b 
                INNER JOIN usuarios u ON b.id_usuario = u.id_usuario 
                WHERE b.id_activo = :id 
                ORDER BY b.fecha_registro DESC";
    $stmtHist = $pdo->prepare($sqlHist);
    $stmtHist->execute(['id' => $id_activo]);
} else {
    $sqlHist = "SELECT b.*, u.nombre_completo, a.tipo_equipo, a.marca, a.modelo 
                FROM bitacora_mantenimiento b 
                INNER JOIN usuarios u ON b.id_usuario = u.id_usuario 
                INNER JOIN activos a ON b.id_activo = a.id_activo 
                ORDER BY b.fecha_registro DESC";
    $stmtHist = $pdo->query($sqlHist);
}
$historial = $stmtHist->fetchAll();
?>

<div class="card toolbar-card">
    <div>
        <?php if ($activo): ?>
            <h2 class="toolbar-title">Bitácora Técnica: <?= htmlspecialchars($activo['id_activo']) ?></h2>
            <span class="kpi-label">
                <?= htmlspecialchars($activo['tipo_equipo']) ?> — <?= htmlspecialchars($activo['marca']) ?> <?= htmlspecialchars($activo['modelo']) ?>
            </span>
        <?php else: ?>
            <h2 class="toolbar-title">Bitácora General de Mantenimientos y Bajas</h2>
            <span class="kpi-label">Historial completo de intervenciones técnicas</span>
        <?php endif; ?>
    </div>
    <a href="<?= $ruta_base ?>index.php" class="btn btn-secondary">← Volver al Inventario</a>
</div>

<?php if ($activo): ?>
<div class="card">
    <h3 class="toolbar-title" style="margin-bottom: 16px;">Registrar Evento / Dictamen Técnico</h3>

    <?php if (!empty($error)): ?>
        <div class="alert-error" style="margin-bottom: 16px;"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form action="index.php?id=<?= urlencode($id_activo) ?>" method="POST">
        <div class="form-grid">
            <div class="form-group">
                <label for="tipo_evento">Tipo de Intervención *</label>
                <select name="tipo_evento" id="tipo_evento" class="form-control" required>
                    <option value="">Selecciona una opción...</option>
                    <option value="Mantenimiento Preventivo">Mantenimiento Preventivo (Limpieza / Revisión)</option>
                    <option value="Mantenimiento Correctivo">Mantenimiento Correctivo (Reparación)</option>
                    <option value="Diagnóstico de Falla">Diagnóstico de Falla</option>
                    <option value="Dictamen de Baja / Scrap">Dictamen de Baja / Scrap</option>
                    <option value="Asignación / Entrega">Asignación / Entrega a Usuario</option>
                </select>
            </div>

            <div class="form-group">
                <label for="nuevo_estado">Actualizar Estado del Equipo</label>
                <select name="nuevo_estado" id="nuevo_estado" class="form-control">
                    <?php
                    $estados = ['Funcional', 'En Resguardo', 'Dañado / Scrap'];
                    foreach ($estados as $est) {
                        $selected = ($activo['estado'] === $est) ? 'selected' : '';
                        echo "<option value=\"$est\" $selected>$est</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group full-width">
                <label for="descripcion">Descripción del Diagnóstico / Trabajo Realizado *</label>
                <textarea name="descripcion" id="descripcion" rows="3" class="form-control" placeholder="Ej. Se detecta daño en tarjeta madre; se dictamina para baja (Scrap)." required></textarea>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Guardar en Bitácora</button>
        </div>
    </form>
</div>
<?php endif; ?>

<div class="card">
    <h3 class="toolbar-title" style="margin-bottom: 16px;">
        <?= $activo ? 'Historial de este Equipo' : 'Todos los Registros Técnicos' ?>
    </h3>
    <div class="tabla-container">
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <?php if (!$activo): ?>
                        <th>Folio / Equipo</th>
                    <?php endif; ?>
                    <th>Tipo de Evento</th>
                    <th>Descripción / Dictamen</th>
                    <th>Registrado por</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($historial) > 0): ?>
                    <?php foreach ($historial as $reg): ?>
                        <tr>
                            <td><?= date('d/m/Y H:i', strtotime($reg['fecha_registro'])) ?></td>
                            <?php if (!$activo): ?>
                                <td>
                                    <strong><?= htmlspecialchars($reg['id_activo']) ?></strong><br>
                                    <small><?= htmlspecialchars($reg['tipo_equipo'] . ' ' . $reg['marca']) ?></small>
                                </td>
                            <?php endif; ?>
                            <td><strong><?= htmlspecialchars($reg['tipo_evento']) ?></strong></td>
                            <td><?= nl2br(htmlspecialchars($reg['descripcion'])) ?></td>
                            <td><?= htmlspecialchars($reg['nombre_completo']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="<?= $activo ? '4' : '5' ?>" class="empty-table-msg">
                            No hay registros en la bitácora técnica.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once $ruta_base . 'includes/footer.php'; ?>