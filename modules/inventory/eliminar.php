<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'Administrador') {$_SESSION['alerta'] = [
        'icono'  => 'error',
        'titulo' => 'Acceso Denegado',
        'texto'  => 'Solo un Administrador puede eliminar activos del inventario.'
    ];
    header("Location: ../../index.php");
    exit();
}

if (isset($_GET['id']) && !empty(trim($_GET['id']))) {
    $id_activo = trim($_GET['id']);

    try {
        $pdo->beginTransaction();

    
        $stmtBitacora =$pdo->prepare("DELETE FROM bitacora_mantenimiento WHERE id_activo = :id");
        $stmtBitacora->execute(['id' =>$id_activo]);

        $stmtActivo =$pdo->prepare("DELETE FROM activos WHERE id_activo = :id");
        $stmtActivo->execute(['id' =>$id_activo]);

        $pdo->commit();

        $_SESSION['alerta'] = [
            'icono'  => 'success',
            'titulo' => 'Activo Eliminado',
            'texto'  => "El equipo con folio $id_activo fue eliminado correctamente."
        ];
    } catch (\PDOException $e) {
        $pdo->rollBack();$_SESSION['alerta'] = [
            'icono'  => 'error',
            'titulo' => 'Error al eliminar',
            'texto'  => 'No se pudo eliminar el registro: ' . $e->getMessage()
        ];
    }
}

header("Location: ../../index.php");
exit();