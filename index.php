<?php
// index.php
require_once 'config/database.php';
require_once 'includes/header.php';

// Consultar totales para las tarjetas KPI
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

<!-- Tarjetas de Métricas / KPI -->
<div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 25px;">
    <div class="card" style="margin: 0; text-align: center;">
        <span style="color: var(--text-muted); font-size: 13px; font-weight: 600;">TOTAL ACTIVOS</span>
        <h2 style="font-size: 28px; margin: 5px 0 0 0;"><?= $total_activos ?></h2>
    </div>
    <div class="card" style="margin: 0; text-align: center;">
        <span style="color: var(--text-muted); font-size: 13px; font-weight: 600;">EN RESGUARDO</span>
        <h2 style="font-size: 28px; margin: 5px 0 0 0; color: var(--badge-resguardo);"><?= $en_resguardo ?></h2>
    </div>
    <div class="card" style="margin: 0; text-align: center;">
        <span style="color: var(--text-muted); font-size: 13px; font-weight: 600;">FUNCIONALES</span>
        <h2 style="font-size: 28px; margin: 5px 0 0 0; color: var(--badge-funcional);"><?= $funcionales ?></h2>
    </div>
    <div class="card" style="margin: 0; text-align: center;">
        <span style="color: var(--text-muted); font-size: 13px; font-weight: 600;">DAÑADO / SCRAP</span>
        <h2 style="font-size: 28px; margin: 5px 0 0 0; color: var(--badge-scrap);"><?= $scrap_count ?></h2>
    </div>
</div>

<!-- Barra de Botones y Filtros -->
<div class="card" style="display: flex; justify-content: space-between; align-items: center;">
    <h2>Inventario General de Activos</h2>
    <a href="modules/inventory/nuevo.php" class="btn btn-primary">+ Registrar Nuevo Activo</a>
</div>

<!-- Tabla General de Activos -->
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
                                if ($act['estado'] === 'Dañado / Scrap') $class_badge = 'badge-scrap';
                                ?>
                                <span class="badge <?= $class_badge ?>"><?= htmlspecialchars($act['estado']) ?></span>
                            </td>
                            <td>
                                <a href="modules/inventory/editar.php?id=<?= urlencode($act['id_activo']) ?>" class="btn btn-secondary" style="padding: 4px 8px; font-size: 12px;">Editar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" style="text-align: center; color: var(--text-muted); padding: 20px;">
                            No hay activos registrados en el sistema.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>