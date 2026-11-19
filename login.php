<?php
session_start();

if (isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'config/database.php';

    $usuario_input = trim($_POST['usuario']);
    $clave_input   = trim($_POST['clave']);

    if (!empty($usuario_input) && !empty($clave_input)) {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE usuario = :usuario LIMIT 1");
        $stmt->execute(['usuario' => $usuario_input]);
        $user = $stmt->fetch();

        if ($user && ($clave_input === $user['clave'] || password_verify($clave_input, $user['clave']))) {
            $_SESSION['usuario_id']      = $user['id_usuario'];
            $_SESSION['nombre_completo'] = $user['nombre_completo'];
            $_SESSION['usuario']         = $user['usuario'];
            $_SESSION['rol']             = $user['rol'];

            header("Location: index.php");
            exit();
        } else {
            $error = 'Usuario o contraseña incorrectos.';
        }
    } else {
        $error = 'Por favor, llena todos los campos.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ML Industries Inventario</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/login.css">
    
</head>
<body class="login-page">

<div class="login-card">
    <h2 class="login-title">Iniciar Sesión</h2>
    
    <?php if (!empty($error)): ?>
        <div class="alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form action="login.php" method="POST">
        <div class="form-group">
            <label for="usuario">Usuario</label>
            <input type="text" name="usuario" id="usuario" class="form-control" placeholder="Ej. marco.jimenez" required autofocus>
        </div>

        <div class="form-group">
            <label for="clave">Contraseña</label>
            <input type="password" name="clave" id="clave" class="form-control" placeholder="••••••••" required>
        </div>

        <button type="submit" class="btn btn-primary btn-block">Ingresar al Sistema</button>
    </form>
</div>

</body>
</html>