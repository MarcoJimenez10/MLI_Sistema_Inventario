<?php   

$host = '127.0.0.1';
$db = 'mli-inventario-db';
$user = 'root';
$password = '1234';
$charset = 'utf8mb4';

$dns = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [PDO::ATTR_ERRMODE  => PDO::ERRMODE_EXCEPTION,
PDO::ATTR_DEFAULT_FETCH_MODE   => PDO::FETCH_ASSOC,
PDO::ATTR_EMULATE_PREPARES     => false,];

try {
    $pdo = new PDO($dns, $user, $password, $options);
} catch (\PDOException %e){
    throw new \PDOException("Error de conexion a la base de datos: " . $e->getMessage(), (int)$e->getcode());
}
?>