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
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>GNC Login</title>

    <link rel="stylesheet" href="../assets/css/style.css">

    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: var(--gnc-bg-main);
            padding: 24px;
            margin: 0;
            box-sizing: border-box;
        }

        .login-box {
            width: 100%;
            max-width: 420px;
            background-color: var(--gnc-bg-card);
            padding: 40px;
            border-radius: 16px;
            box-shadow: var(--shadow);
            border: 1px solid var(--gnc-border);
            text-align: center;
            box-sizing: border-box;
        }

        .login-box img {
            width: 180px;
            max-width: 100%;
            margin-bottom: 25px;
        }

        .login-box h1 {
            border-left: none;
            padding-left: 0;
            margin-bottom: 30px;
            font-size: 28px;
        }

        .input-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .input-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--gnc-text-secondary);
            font-weight: 600;
            font-size: 14px;
        }

        .input-group input {
            width: 100%;
            box-sizing: border-box;
        }

        .login-btn {
            width: 100%;
            margin-top: 10px;
        }

        .register-link {
            margin-top: 20px;
        }

        .message {
            margin-bottom: 20px;
            overflow-wrap: anywhere;
        }

        .success-message {
            color: #00ff88;
        }

        .error-message {
            color: #ff8585;
        }

        .warning-message {
            color: orange;
        }
    </style>
</head>

<body>
    <div class="login-box">
        <img
            src="../assets/images/GNC_Logo.svg.png"
            alt="GNC Logo"
        >

        <h1>Iniciar sesión</h1>

        <?php if (is_string($registeredUsername)): ?>
            <p class="message success-message" role="status">
                Cuenta creada. Inicia sesión con:
                <strong>
                    <?= htmlspecialchars(
                        $registeredUsername,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </strong>
            </p>
        <?php endif; ?>

        <?php if ($error === '1'): ?>
            <p class="message error-message" role="alert">
                Usuario o contraseña incorrectos,
                o cuenta sin acceso.
            </p>
        <?php elseif ($error === '2'): ?>
            <p class="message warning-message" role="alert">
                Completa correctamente todos los campos.
            </p>
        <?php endif; ?>

        <?php if (isset($_GET['expired'])): ?>
            <p class="message warning-message" role="status">
                Tu sesión expiró. Inicia sesión nuevamente.
            </p>
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
    </div>
</body>

</html>