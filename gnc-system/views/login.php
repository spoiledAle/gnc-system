<?php

session_start();

if(isset($_SESSION['user'])) {

    header("Location: home.php");

    exit();

}

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

    <div class="login-box">

        <img
        src="../assets/images/GNC_Logo.svg.png"
        alt="GNC Logo">

        <h1>

            Iniciar Sesión

        </h1>

        <?php if(isset($_GET['success'])) { ?>
            <div class="alert alert-success">
                ✓ Usuario registrado correctamente
            </div>
        <?php } ?>

        <?php if(isset($_GET['error']) && $_GET['error'] == 1) { ?>
            <div class="alert alert-error">
                ✕ Correo o contraseña incorrectos
            </div>
        <?php } ?>

        <?php if(isset($_GET['error']) && $_GET['error'] == 2) { ?>
            <div class="alert alert-warning">
                ⚠ Todos los campos son obligatorios
            </div>
        <?php } ?>

        <form
        action="../controllers/LoginController.php"
        method="POST">

            <div class="input-group">

                <label for="email">

                    Correo Electrónico

                </label>

                <input
                type="email"
                id="email"
                name="email"
                required>

            </div>

            <div class="input-group">

                <label for="password">

                    Contraseña

                </label>

                <input
                type="password"
                id="password"
                name="password"
                required>

            </div>

            <button
            type="submit"
            class="login-btn btn add-btn">

                Entrar al Sistema

            </button>

        </form>

        <div class="register-link">

            <a href="register.php">

                Crear nuevo usuario administrativo

            </a>

        </div>

    </div>

</body>

</html>