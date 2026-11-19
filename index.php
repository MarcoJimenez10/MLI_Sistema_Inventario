<?php
require_once 'config/database.php';
require_once 'includes/header.php';

// Consultar totales
$total_activos = $pdo->query("SELECT COUNT(*) FROM activos")->fetchColumn();
$en_resguardo  = $pdo->query("SELECT COUNT(*) FROM activos WHERE estado = 'En Resguardo'")->fetchColumn();
$funcionales   = $pdo->query("SELECT COUNT(*) FROM activos WHERE estado = 'Funcional'")->fetchColumn();
$scrap_count   = $pdo->query("SELECT COUNT(*) FROM activos WHERE estado = 'Dañado / Scrap'")->fetchColumn();

// Consultar los activos registrados
$query = "SELECT * FROM activos ORDER BY fecha_registro DESC";
$stmt  = $pdo->prepare($query);
$stmt->execute();
$activos = $stmt->fetchAll();
?>

<div class="kpi-grid">
    <div class="card kpi-card">
        <span class="kpi-label">Total Activos</span>
        <h2 class="kpi-number"><?= $total_activos ?></h2>
    </div>
    <div class="card kpi-card">
        <span class="kpi-label">En Resguardo</span>
        <h2 class="kpi-number text-resguardo"><?= $en_resguardo ?></h2>
    </div>
    <div class="card kpi-card">
        <span class="kpi-label">Funcionales</span>
        <h2 class="kpi-number text-funcional"><?= $funcionales ?></h2>
    </div>
    <div class="card kpi-card">
        <span class="kpi-label">Dañado</span>
        <h2 class="kpi-number text-scrap"><?= $scrap_count ?></h2>
    </div>
</div>

<div class="card toolbar-card">
    <h2 class="toolbar-title">Inventario General de Activos</h2>
    <a href="modules/inventory/nuevo.php" class="btn btn-primary">+ Registrar Nuevo Activo</a>
</div>

<div class="card">
    <div class="tabla-container">
        <table>
            <thead>
                <tr>
                    <th>Folio ID</th>
                    <th>Tipo</th>
                    <th>Marca y Modelo</th>
                    <th>Núm. Serie</th>
                    <th>Ubicación</th>
                    <th>Usuario Asignado</th>
                    <th>IP</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($activos) > 0): ?>
                    <?php foreach ($activos as $act): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($act['id_activo']) ?></strong></td>
                            <td><?= htmlspecialchars($act['tipo_equipo']) ?></td>
                            <td><?= htmlspecialchars($act['marca']) ?> <?= htmlspecialchars($act['modelo']) ?></td>
                            <td><?= htmlspecialchars($act['numero_serie']) ?></td>
                            <td><?= htmlspecialchars($act['ubicacion']) ?></td>
                            <td><?= htmlspecialchars($act['usuario_asignado']) ?></td>
                            <td><?= htmlspecialchars($act['direccion_ip']) ?></td>
                            <td>
                                <?php
                                $class_badge = 'badge-funcional';
                                if ($act['estado'] === 'En Resguardo') $class_badge = 'badge-resguardo';
                                if ($act['estado'] === 'En Mantenimiento') $class_badge = 'badge-mantenimiento';
                                if ($act['estado'] === 'Dañado') $class_badge = 'badge-scrap';
                                ?>
                                <span class="badge <?= $class_badge ?>"><?= htmlspecialchars($act['estado']) ?></span>
                            </td>
                            <td>
                                <a href="modules/inventory/editar.php?id=<?= urlencode($act['id_activo']) ?>" class="btn btn-secondary btn-sm">Editar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="empty-table-msg">
                            No hay activos registrados en el sistema.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>