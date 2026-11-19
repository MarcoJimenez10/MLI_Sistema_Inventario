<?php
require_once 'config/database.php';
require_once 'includes/header.php';

// Consultar totales para las tarjetas KPI (siempre muestran el global)
$total_activos = $pdo->query("SELECT COUNT(*) FROM activos")->fetchColumn();
$en_resguardo  = $pdo->query("SELECT COUNT(*) FROM activos WHERE estado = 'En Resguardo'")->fetchColumn();
$funcionales   = $pdo->query("SELECT COUNT(*) FROM activos WHERE estado = 'Funcional'")->fetchColumn();
$scrap_count   = $pdo->query("SELECT COUNT(*) FROM activos WHERE estado = 'Dañado / Scrap'")->fetchColumn();


$busqueda      = trim($_GET['buscar'] ?? '');
$filtro_estado = trim($_GET['estado'] ?? '');


$sql = "SELECT * FROM activos WHERE 1=1";
$params = [];

if (!empty($busqueda)) {
    $sql .= " AND (
                id_activo LIKE :b1 OR 
                tipo_equipo LIKE :b2 OR 
                marca LIKE :b3 OR 
                modelo LIKE :b4 OR 
                numero_serie LIKE :b5 OR 
                ubicacion LIKE :b6 OR 
                usuario_asignado LIKE :b7 OR 
                direccion_ip LIKE :b8
              )";
    $termino = "%$busqueda%";
    for ($i = 1; $i <= 8; $i++) {
        $params["b$i"] = $termino;
    }
}

if (!empty($filtro_estado)) {
    $sql .= " AND estado = :estado";
    $params['estado'] = $filtro_estado;
}

$sql .= " ORDER BY fecha_registro DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
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
        <span class="kpi-label">Dañado / Scrap</span>
        <h2 class="kpi-number text-scrap"><?= $scrap_count ?></h2>
    </div>
</div>


<div class="card toolbar-card">
    <h2 class="toolbar-title">Inventario General de Activos</h2>
    <a href="modules/inventory/nuevo.php" class="btn btn-primary">+ Registrar Nuevo Activo</a>
</div>


<div class="card">
    <form action="index.php" method="GET" class="filter-bar">
        <div class="filter-input">
            <input type="text" name="buscar" class="form-control" placeholder="Buscar por ID, Marca, Modelo, Serie, IP o Usuario..." value="<?= htmlspecialchars($busqueda) ?>">
        </div>

        <div class="filter-select">
            <select name="estado" class="form-control">
                <option value="">Todos los estados</option>
                <?php
                $estados_lista = ['Funcional', 'En Resguardo', 'En Mantenimiento', 'Dañado / Scrap'];
                foreach ($estados_lista as $est) {
                    $selected = ($filtro_estado === $est) ? 'selected' : '';
                    echo "<option value=\"$est\" $selected>$est</option>";
                }
                ?>
            </select>
        </div>

        <div class="filter-actions">
            <button type="submit" class="btn btn-primary">Filtrar</button>
            <?php if (!empty($busqueda) || !empty($filtro_estado)): ?>
                <a href="index.php" class="btn btn-secondary">Limpiar</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="card">
    <div class="tabla-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
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
                            <td><?= htmlspecialchars($act['numero_serie'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($act['ubicacion'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($act['usuario_asignado'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($act['direccion_ip'] ?? '-') ?></td>
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
                                <a href="modules/inventory/editar.php?id=<?= urlencode($act['id_activo']) ?>" class="btn btn-secondary btn-sm">Editar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="empty-table-msg">
                            No se encontraron activos que coincidan con la búsqueda.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>