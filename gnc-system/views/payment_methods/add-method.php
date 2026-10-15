<?php
session_start();
$inactive = 300;
if (isset($_SESSION['timeout'])) {
    $sessionLife = time() - $_SESSION['timeout'];
    if ($sessionLife > $inactive) {
        session_unset();
        session_destroy();
        header("Location: ../login.php");
        exit();
    }
}
$_SESSION['timeout'] = time();

if(!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Método de Pago</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <?php $currentPage = 'payment_methods'; $basePath = '../'; include __DIR__ . '/../partials/navbar.php'; ?>

    <div class="container">
        <h1 class="title">Nuevo Método de Pago</h1>
        <p class="subtitle">Añade una nueva forma de recibir pagos en el sistema.</p>

        <div class="page-actions">
            <a href="payment-methods.php" class="btn btn-secondary">← Volver a Métodos</a>
        </div>

        <div class="form-container">
            <form method="POST" action="../../controllers/PaymentMethodController.php">
                <div class="form-group">
                    <label for="name">Nombre del Método:</label>
                    <input type="text" id="name" name="name" required placeholder="Ej. SINPE Móvil, Cripto...">
                </div>

                <button type="submit" class="btn add-btn btn-block">Guardar Método</button>
            </form>
        </div>
    </div>
</body>
</html>