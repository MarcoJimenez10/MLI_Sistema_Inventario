<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'Administrador') {
    header("Location: ../../index.php");
    exit();
}

if (isset($_GET['id'])) {
    $id_eliminar = (int)$_GET['id'];

    // Evitar que el administrador borre su propia cuenta activa
    if ($id_eliminar !== (int)$_SESSION['usuario_id']) {
        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id_usuario = :id");
        $stmt->execute(['id' => $id_eliminar]);

        $_SESSION['alerta'] = [
            'icono'  => 'success',
            'titulo' => 'Usuario Eliminado',
            'texto'  => 'La cuenta fue eliminada del sistema.'
        ];
    }
}

header("Location: index.php");
exit();