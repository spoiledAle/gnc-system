<?php

require_once __DIR__ . '/../config/session.php';

header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

$activeSession =
    isset(
        $_SESSION['user'],
        $_SESSION['dbCredentials'],
        $_SESSION['timeout']
    )
    && time() - (int) $_SESSION['timeout'] < 300;

if ($activeSession) {
    header('Location: home.php');
    exit;
}

unset(
    $_SESSION['user'],
    $_SESSION['dbCredentials'],
    $_SESSION['timeout'],
    $_SESSION['cart'],
    $_SESSION['checkoutToken'],
    $_SESSION['edit_index']
);

if (!isset($_SESSION['csrfToken'])) {
    $_SESSION['csrfToken'] = bin2hex(random_bytes(32));
}

$error = $_GET['error'] ?? '';

$registeredUsername = $_SESSION['registeredUsername'] ?? null;
unset($_SESSION['registeredUsername']);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GNC Login</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body class="auth-body">
    <main class="login-box">
        <img
            src="../assets/images/GNC_Logo.svg.png"
            alt="GNC Logo"
        >

        <h1>Iniciar sesión</h1>

        <?php if (is_string($registeredUsername)): ?>
            <div class="alert alert-success" role="status">
                Cuenta creada. Inicia sesión con:
                <strong>
                    <?= htmlspecialchars(
                        $registeredUsername,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </strong>
            </div>
        <?php endif; ?>

        <?php if ($error === '1'): ?>
            <div class="alert alert-error" role="alert">
                Usuario o contraseña incorrectos,
                o cuenta sin acceso.
            </div>
        <?php elseif ($error === '2'): ?>
            <div class="alert alert-warning" role="alert">
                Completa correctamente todos los campos.
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['expired'])): ?>
            <div class="alert alert-warning" role="status">
                Tu sesión expiró. Inicia sesión nuevamente.
            </div>
        <?php endif; ?>

        <form
            action="../controllers/Logincontroller.php"
            method="POST"
        >
            <input
                type="hidden"
                name="csrfToken"
                value="<?= htmlspecialchars(
                    $_SESSION['csrfToken'],
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
            >

            <div class="input-group">
                <label for="username">Usuario</label>
                <input
                    type="text"
                    id="username"
                    name="username"
                    maxlength="128"
                    autocomplete="username"
                    required
                >
            </div>

            <div class="input-group">
                <label for="password">Contraseña</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    autocomplete="current-password"
                    required
                >
            </div>

            <button
                type="submit"
                class="login-btn btn add-btn"
            >
                Entrar al sistema
            </button>
        </form>

        <div class="register-link">
            <a href="register.php">Crear cuenta</a>
        </div>
    </main>
</body>
</html>