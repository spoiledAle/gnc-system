<?php

require_once __DIR__ . '/../config/forms.php';

if (isset($_SESSION['user'])) {
    header('Location: home.php');
    exit;
}

$messages = [
    '1' => 'No se pudo crear la cuenta. Revisa la consola PHP.',
    '2' => 'Completa los campos y cumple los requisitos de contraseña.',
    '3' => 'Las contraseñas no coinciden.',
    '4' => 'El usuario o correo ya está registrado.',
];

$error = $_GET['error'] ?? '';
$message = is_string($error) ? ($messages[$error] ?? '') : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Crear cuenta</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 24px;
            box-sizing: border-box;
        }
        .login-box {
            width: 100%;
            max-width: 440px;
            padding: 32px;
            background: var(--gnc-bg-card);
            border: 1px solid var(--gnc-border);
            border-radius: 16px;
            box-sizing: border-box;
        }
        .login-box img { width: 180px; display: block; margin: 0 auto; }
        label { display: block; margin: 18px 0 8px; }
        input { width: 100%; box-sizing: border-box; }
        button { margin-top: 20px; width: 100%; }
        small { display: block; margin-top: 6px; }
    </style>
</head>
<body>
<main class="login-box">
    <img src="../assets/images/GNC_Logo.svg.png" alt="GNC">
    <h1>Crear cuenta</h1>

    <?php if ($message !== ''): ?>
        <p role="alert"><?= gncEscape($message) ?></p>
    <?php endif; ?>

    <form method="post" action="../controllers/UserController.php">
        <?= gncCsrfField() ?>

        <label for="name">Nombre completo</label>
        <input
            id="name"
            name="name"
            maxlength="100"
            autocomplete="name"
            required
        >

        <label for="username">Usuario</label>
        <input
            id="username"
            name="username"
            minlength="4"
            maxlength="30"
            pattern="[A-Za-z][A-Za-z0-9_]{3,29}"
            autocomplete="username"
            required
        >
        <small>
            De 4 a 30 caracteres: letras, números y guion bajo.
            Debe comenzar con una letra.
            Si escribes tunombre, entrarás con cliente_tunombre.
        </small>

        <label for="email">Correo electrónico</label>
        <input
            type="email"
            id="email"
            name="email"
            maxlength="100"
            autocomplete="email"
            required
        >

        <label for="password">Contraseña</label>
        <input
            type="password"
            id="password"
            name="password"
            minlength="8"
            maxlength="128"
            autocomplete="new-password"
            required
        >
        <small>
            Al menos 12 caracteres: mayúscula, minúscula, número
            y símbolo. Usa caracteres sin acentos en la contraseña.
        </small>

        <label for="confirmPassword">Confirmar contraseña</label>
        <input
            type="password"
            id="confirmPassword"
            name="confirmPassword"
            minlength="12"
            maxlength="128"
            autocomplete="new-password"
            required
        >

        <button type="submit" class="btn add-btn">Crear cuenta</button>
    </form>

    <p>Tu cuenta se creará como cliente.</p>
    <a href="login.php">Volver al login</a>
</main>
</body>
</html>