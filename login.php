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
            $_SESSION['login_exitoso']   = true;

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

<div class="login-glow-card">
  <div class="login-glow-inner">
    <form class="login-form" action="login.php" method="POST">
        <img src="assets/img/logo_mli.svg" alt="ML Industries Logo" class="login-logo" />
      <p id="heading">Activos TI</p>

      <?php if (!empty($error)): ?>
          <div class="alert-error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <div class="field">
        <svg viewBox="0 0 16 16" fill="currentColor" height="16" width="16" xmlns="http://www.w3.org/2000/svg" class="input-icon">
          <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"/>
        </svg>
        <input type="text" name="usuario" class="input-field" placeholder="Usuario" autocomplete="off" required autofocus />
      </div>

      <div class="field">
        <svg viewBox="0 0 16 16" fill="currentColor" height="16" width="16" xmlns="http://www.w3.org/2000/svg" class="input-icon">
          <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/>
        </svg>
        <input type="password" name="clave" class="input-field" placeholder="Contraseña" required />
      </div>

      <div class="login-btn-container">
        <button type="submit" class="login-button">Iniciar Sesión</button>
      </div>
    </form>
  </div>
</div>

</body>
</html>