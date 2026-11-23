<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../../login.php");
    exit();
}

$busqueda      = trim($_GET['buscar'] ?? '');
$filtro_estado = trim($_GET['estado'] ?? '');

$sql = "SELECT id_activo, tipo_equipo, marca, modelo, numero_serie, ubicacion, usuario_asignado, direccion_ip, estado, comentarios, fecha_registro 
        FROM activos WHERE 1=1";
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

$nombre_archivo = "Inventario_MLI_" . date('Y-m-d_His') . ".csv";
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $nombre_archivo . '"');

$salida = fopen('php://output', 'w');


fprintf($salida, chr(0xEF).chr(0xBB).chr(0xBF));


fputcsv($salida, [
    'ID',
    'Tipo de Equipo',
    'Marca',
    'Modelo',
    'Numero de Serie',
    'Ubicacion',
    'Usuario Asignado',
    'Direccion IP',
    'Estado',
    'Comentarios',
    'Fecha de Registro'
]);


foreach ($activos as $row) {
    fputcsv($salida, [
        $row['id_activo'],
        $row['tipo_equipo'],
        $row['marca'],
        $row['modelo'],
        $row['numero_serie'] ?? '',
        $row['ubicacion'] ?? '',
        $row['usuario_asignado'] ?? '',
        $row['direccion_ip'] ?? '',
        $row['estado'],
        $row['comentarios'] ?? '',
        $row['fecha_registro']
    ]);
}

fclose($salida);
exit();