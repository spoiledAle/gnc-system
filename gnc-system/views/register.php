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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear cuenta</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body class="auth-body">
    <main class="login-box">
        <img
            src="../assets/images/GNC_Logo.svg.png"
            alt="GNC Logo"
        >

        <h1>Crear cuenta</h1>

        <?php if ($message !== ''): ?>
            <div class="alert alert-error" role="alert">
                <?= gncEscape($message) ?>
            </div>
        <?php endif; ?>

        <form
            method="POST"
            action="../controllers/UserController.php"
        >
            <?= gncCsrfField() ?>

            <input type="hidden" name="action" value="register">

            <div class="input-group">
                <label for="name">Nombre completo</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    maxlength="100"
                    autocomplete="name"
                    required
                >
            </div>

            <div class="input-group">
                <label for="username">Usuario</label>
                <input
                    type="text"
                    id="username"
                    name="username"
                    minlength="4"
                    maxlength="30"
                    pattern="[A-Za-z][A-Za-z0-9_]{3,29}"
                    autocomplete="username"
                    aria-describedby="usernameHelp"
                    required
                >
                <small id="usernameHelp">
                    De 4 a 30 caracteres: letras, números y guion bajo.
                    Debe comenzar con una letra.
                    Si escribes tunombre, entrarás con cliente_tunombre.
                </small>
            </div>

            <div class="input-group">
                <label for="email">Correo electrónico</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    maxlength="100"
                    autocomplete="email"
                    required
                >
            </div>

            <div class="input-group">
                <label for="password">Contraseña</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    minlength="12"
                    maxlength="128"
                    autocomplete="new-password"
                    aria-describedby="passwordHelp"
                    required
                >
                <small id="passwordHelp">
                    Al menos 12 caracteres: mayúscula, minúscula,
                    número y símbolo. Usa caracteres sin acentos
                    en la contraseña.
                </small>
            </div>

            <div class="input-group">
                <label for="confirmPassword">
                    Confirmar contraseña
                </label>
                <input
                    type="password"
                    id="confirmPassword"
                    name="confirmPassword"
                    minlength="12"
                    maxlength="128"
                    autocomplete="new-password"
                    required
                >
            </div>

            <button
                type="submit"
                class="login-btn btn add-btn"
            >
                Crear cuenta
            </button>
        </form>

        <p>Tu cuenta se creará como cliente.</p>

        <div class="register-link">
            <a href="login.php">Volver al login</a>
        </div>
    </main>
</body>
</html>