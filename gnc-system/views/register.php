<?php

session_start();

session_destroy();

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Registrar Usuario</title>

    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body class="auth-body">

    <div class="login-box">

        <img
        src="../assets/images/GNC_Logo.svg.png"
        alt="GNC Logo">

        <h1>

            Registrar Usuario

        </h1>

        <form
        action="../controllers/UserController.php"
        method="POST">

            <input
            type="hidden"
            name="action"
            value="register">

            <div class="input-group">

                <label>

                    Nombre

                </label>

                <input
                type="text"
                name="name"
                required>

            </div>

            <div class="input-group">

                <label>

                    Correo Electrónico

                </label>

                <input
                type="email"
                name="email"
                required>

            </div>

            <div class="input-group">

                <label>

                    Contraseña

                </label>

                <input
                type="password"
                name="password"
                required>

            </div>

            <button
            type="submit"
            class="login-btn btn add-btn">

                Registrar Usuario

            </button>

        </form>

        <br>

        <a href="login.php">

            Volver al login

        </a>

    </div>

</body>

</html>