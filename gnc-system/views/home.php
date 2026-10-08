<?php

session_start();
$inactive = 300;

if(!isset($_SESSION['user'])) {

    header("Location: login.php");

    exit();

}

header("Cache-Control: no-cache, no-store, must-revalidate");

header("Pragma: no-cache");

header("Expires: 0");

?>

<!DOCTYPE html>

<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>GNC SYSTEM</title>

<link
rel="stylesheet"
href="../assets/css/style.css">

</head>

<body>

<?php $currentPage = 'home'; $basePath = ''; include __DIR__ . '/partials/navbar.php'; ?>

<div class="container">

    <h1 class="title">

        GNC System

    </h1>

    <p class="subtitle">

        Administración premium de inventario,
        ventas y control de suplementos.

    </p>

    <div class="cards">

        <a href="products/products.php" class="card">

            <h2>

                Productos

            </h2>

            <p>

                Gestiona inventario.

            </p>

        </a>

        <a href="sales/sales.php" class="card">

            <h2>
                Ventas
            </h2>

            <p>
                Registra ventas
                con control de stock.
            </p>

        </a>

        <a href="sales/sales-history.php" class="card">

            <h2>
                Historial
            </h2>

            <p>
                Consulta ventas realizadas
                y detalles de transacciones.
            </p>

        </a>

        <a href="suppliers/suppliers.php" class="card">

            <h2>
                Proveedores
            </h2>

            <p>
                Gestiona proveedores
                del sistema.
            </p>

        </a>

        <a href="payment_methods/payment-methods.php" class="card">

            <h2>
                Métodos de Pago
            </h2>

            <p>
                Administra métodos
                de pago disponibles.
            </p>

        </a>

        <a href="purchases/purchases.php" class="card">

            <h2>
                Pedidos
            </h2>

            <p>
                Registra pedidos
                realizados a proveedores.
            </p>

        </a>

        <a href="audits/audits.php" class="card">

            <h2>
                Auditorías
            </h2>

            <p>
                Visualiza registros automáticos
                de acciones realizadas en el sistema.
            </p>

        </a>

    </div>

</div>

</body>

</html>