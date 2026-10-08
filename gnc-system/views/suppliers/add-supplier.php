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
    <title>Agregar Proveedor</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <?php $currentPage = 'suppliers'; $basePath = '../'; include __DIR__ . '/../partials/navbar.php'; ?>

    <div class="container">
        <h1 class="title">Nuevo Proveedor</h1>
        <p class="subtitle">Registra un nuevo aliado comercial para el abastecimiento.</p>

        <div class="page-actions">
            <a href="suppliers.php" class="btn btn-secondary">← Volver a Proveedores</a>
        </div>

        <div class="form-container">
            <form method="POST" action="../../controllers/SupplierController.php">
                <div class="form-group">
                    <label for="name">Nombre Comercial:</label>
                    <input type="text" id="name" name="name" required placeholder="Nombre de la empresa">
                </div>

                <div class="form-group">
                    <label for="phone">Teléfono de Contacto:</label>
                    <input type="text" id="phone" name="phone" required placeholder="Ej. 8888-8888">
                </div>

                <div class="form-group">
                    <label for="email">Correo Electrónico:</label>
                    <input type="email" id="email" name="email" required placeholder="contacto@empresa.com">
                </div>

                <div class="form-group">
                    <label for="address">Dirección Física:</label>
                    <input type="text" id="address" name="address" required placeholder="Ciudad, Provincia...">
                </div>

                <button type="submit" class="btn add-btn btn-block">Guardar Proveedor</button>
            </form>
        </div>
    </div>
</body>
</html>