<?php
$ruta_base = '../../';
require_once $ruta_base . 'config/database.php';
require_once $ruta_base . 'includes/header.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_activo        = trim($_POST['id_activo']);
    $tipo_equipo      = trim($_POST['tipo_equipo']);
    $marca            = trim($_POST['marca']);
    $modelo           = trim($_POST['modelo']);
    $numero_serie     = trim($_POST['numero_serie']);
    $ubicacion        = trim($_POST['ubicacion']);
    $usuario_asignado = trim($_POST['usuario_asignado']);
    $direccion_ip     = trim($_POST['direccion_ip']);
    $estado           = trim($_POST['estado']);
    $comentarios      = trim($_POST['comentarios']);

    if (!empty($id_activo) && !empty($tipo_equipo) && !empty($marca) && !empty($modelo) && !empty($estado)) {
        // Verificar que el ID manual no exista ya en la base de datos
        $check = $pdo->prepare("SELECT COUNT(*) FROM activos WHERE id_activo = :id");
        $check->execute(['id' => $id_activo]);

        if ($check->fetchColumn() > 0) {
            $error = "El Folio / ID '$id_activo' ya está registrado en el sistema. Verifica el número.";
        } else {
            try {
                $sql = "INSERT INTO activos 
                        (id_activo, tipo_equipo, marca, modelo, numero_serie, ubicacion, usuario_asignado, direccion_ip, estado, comentarios) 
                        VALUES 
                        (:id_activo, :tipo_equipo, :marca, :modelo, :numero_serie, :ubicacion, :usuario_asignado, :direccion_ip, :estado, :comentarios)";
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    'id_activo'        => $id_activo,
                    'tipo_equipo'      => $tipo_equipo,
                    'marca'            => $marca,
                    'modelo'           => $modelo,
                    'numero_serie'     => $numero_serie ?: null,
                    'ubicacion'        => $ubicacion ?: null,
                    'usuario_asignado' => $usuario_asignado ?: null,
                    'direccion_ip'     => $direccion_ip ?: null,
                    'estado'           => $estado,
                    'comentarios'      => $comentarios ?: null
                ]);

                $_SESSION['alerta'] = [
                    'icono'  => 'success',
                    'titulo' => '¡Activo Registrado!',
                    'texto'  => "El equipo con folio $id_activo se guardó correctamente en el inventario."
                ];

                header("Location: " . $ruta_base . "index.php");
                exit();
            } catch (\PDOException $e) {
                $error = "Error al guardar el activo: " . $e->getMessage();
            }
        }
    } else {
        $error = "Por favor, completa todos los campos obligatorios (*).";
    }
}
?>

<div class="card toolbar-card">
    <h2 class="toolbar-title">Registrar Nuevo Activo</h2>
    <a href="<?= $ruta_base ?>index.php" class="btn btn-secondary">← Volver al Inventario</a>
</div>

<div class="card">
    <?php if (!empty($error)): ?>
        <div class="alert-error" style="margin-bottom: 16px;"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form action="nuevo.php" method="POST">
        <div class="form-grid">
        
            <div class="form-group">
                <label for="id_activo">ID *</label>
                <input type="text" name="id_activo" id="id_activo" class="form-control" placeholder="Ej. 2928 o MLI-LAP-01" required autofocus>
            </div>

            <div class="form-group">
                <label for="tipo_equipo">Tipo de Equipo *</label>
                <select name="tipo_equipo" id="tipo_equipo" class="form-control" required>
                    <option value="">Selecciona una opción...</option>
                    <option value="Laptop">Laptop</option>
                    <option value="Desktop">Desktop</option>
                    <option value="Monitor">Monitor</option>
                    <option value="Impresora">Impresora</option>
                    <option value="Escáner">Escáner</option>
                    <option value="Switch / Red">Switch / Equipo de Red</option>
                    <option value="Otro">Otro</option>
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
                <input type="text" name="modelo" id="modelo" class="form-control" placeholder="Ej. Victus 15, Latitude 5420" required>
            </div>

            <div class="form-group">
                <label for="numero_serie">Número de Serie</label>
                <input type="text" name="numero_serie" id="numero_serie" class="form-control" placeholder="Ej. 5CD1234XYZ">
            </div>


            <div class="form-group">
                <label for="estado">Estado Operativo *</label>
                <select name="estado" id="estado" class="form-control" required>
                    <option value="Funcional">Funcional</option>
                    <option value="En Resguardo" selected>En Resguardo</option>
                    <option value="Dañado / Scrap">Dañado</option>
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
                <input type="text" name="usuario_asignado" id="usuario_asignado" class="form-control" placeholder="Nombre del responsable o 'Sin asignar'">
            </div>

            <div class="form-group">
                <label for="direccion_ip">Dirección IP</label>
                <input type="text" name="direccion_ip" id="direccion_ip" class="form-control" placeholder="Ej. 192.168.1.87">
            </div>

    
            <div class="form-group full-width">
                <label for="comentarios">Observaciones / Detalles Físicos</label>
                <textarea name="comentarios" id="comentarios" rows="3" class="form-control" placeholder="Ej. Incluye cargador original, detalle estético en carcasa..."></textarea>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Guardar Activo</button>
            <button type="reset" class="btn btn-secondary">Limpiar</button>
            <a href="<?= $ruta_base ?>index.php" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

<?php require_once $ruta_base . 'includes/footer.php'; ?>