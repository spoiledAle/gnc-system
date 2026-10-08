<?php

require_once __DIR__ . '/../config/auth_check.php';

$role = $_SESSION['user']['role'] ?? '';

if (!in_array($role, ['ADMIN', 'EMPLEADO', 'CLIENTE'], true)) {
    http_response_code(403);
    exit('La sesión no tiene un rol válido. Inicia sesión nuevamente.');
}

$isAdmin = $role === 'ADMIN';
$isEmployee = $role === 'EMPLEADO';
$isCustomer = $role === 'CLIENTE';

$historyUrl = $isCustomer
    ? 'sales/my-purchases.php'
    : 'sales/sales-history.php';

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GNC System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <?php
    $currentPage = 'home';
    $basePath = '';
    include __DIR__ . '/partials/navbar.php';
    ?>

    <main class="container">
        <h1 class="title">GNC System</h1>

        <p class="subtitle">
            <?php if ($isCustomer): ?>
                Explora nuestros suplementos y realiza tus compras.
            <?php else: ?>
                Administración de inventario, ventas
                y control de suplementos.
            <?php endif; ?>
        </p>

        <div class="cards">
            <?php if ($isAdmin || $isEmployee): ?>
                <a href="products/products.php" class="card">
                    <h2>Productos</h2>
                    <p>
                        <?php if ($isAdmin): ?>
                            Gestiona productos e inventario.
                        <?php else: ?>
                            Consulta productos y stock disponible.
                        <?php endif; ?>
                    </p>
                </a>
            <?php endif; ?>

            <a href="sales/sales.php" class="card">
                <h2>
                    <?= $isCustomer ? 'Comprar productos' : 'Ventas' ?>
                </h2>
                <p>
                    <?php if ($isCustomer): ?>
                        Consulta productos y precios, añade productos
                        al carrito y confirma tu compra.
                    <?php else: ?>
                        Registra ventas con control de stock.
                    <?php endif; ?>
                </p>
            </a>

            <a href="<?= $historyUrl ?>" class="card">
                <h2>
                    <?= $isCustomer ? 'Mis compras' : 'Historial de ventas' ?>
                </h2>
                <p>
                    <?php if ($isCustomer): ?>
                        Consulta tus compras y el detalle de sus productos.
                    <?php else: ?>
                        Consulta las ventas realizadas y sus detalles.
                    <?php endif; ?>
                </p>
            </a>

            <?php if ($isAdmin || $isEmployee): ?>
                <a href="suppliers/suppliers.php" class="card">
                    <h2>Proveedores</h2>
                    <p>
                        <?php if ($isAdmin): ?>
                            Gestiona los proveedores del sistema.
                        <?php else: ?>
                            Consulta los proveedores disponibles.
                        <?php endif; ?>
                    </p>
                </a>

                <a href="purchases/purchases.php" class="card">
                    <h2>Pedidos a proveedores</h2>
                    <p>
                        Registra pedidos y actualiza
                        su estado de recepción.
                    </p>
                </a>
            <?php endif; ?>

            <?php if ($isAdmin): ?>
                <a
                    href="payment_methods/payment-methods.php"
                    class="card"
                >
                    <h2>Métodos de pago</h2>
                    <p>Administra los métodos de pago disponibles.</p>
                </a>

                <a href="audits/audits.php" class="card">
                    <h2>Auditorías</h2>
                    <p>Consulta las acciones registradas en el sistema.</p>
                </a>

                <a href="users/users.php" class="card">
                    <h2>Administración de usuarios</h2>
                    <p>
                        Consulta cuentas, cambia roles
                        y activa o desactiva usuarios.
                    </p>
                </a>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>