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
    <link rel="stylesheet" href="assets/css/estilos.css">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background-color: #f1f5f9;
        }
        .login-card {
            width: 100%;
            max-width: 400px;
            padding: 32px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            text-align: center;
            border: 1px solid #cbd5e1;
        }
        .login-title {
            font-size: 24px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 16px;
            text-align: left;
        }
        .alert-error {
            background-color: #fee2e2;
            color: #dc2626;
            padding: 10px;
            border-radius: 6px;
            font-size: 14px;
            margin-bottom: 16px;
            border: 1px solid #fca5a5;
        }
        .btn-block {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            margin-top: 10px;
        }
    </style>
</head>
<body>

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