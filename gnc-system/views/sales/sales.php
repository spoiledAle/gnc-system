<?php

require_once __DIR__ . '/../../config/auth_check.php';
requireRole(['ADMIN', 'EMPLEADO', 'CLIENTE']);

require_once __DIR__ . '/../../config/forms.php';
require_once __DIR__ . '/../../models/SaleModel.php';

$model = new SaleModel();

$products = $model->getProducts()->fetchAll(PDO::FETCH_ASSOC);
$methods = $model->getPaymentMethods()->fetchAll(PDO::FETCH_ASSOC);

$_SESSION['cart'] ??= [];

$customer = $_SESSION['user']['role'] === 'CLIENTE';
$total = 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $customer ? 'Comprar productos' : 'Ventas' ?></title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        .sale-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            margin-top: 25px;
        }
        .sale-card {
            background: var(--gnc-bg-card);
            padding: 25px;
            border: 1px solid var(--gnc-border);
            border-radius: 16px;
        }
        label { display: block; margin: 15px 0; }
        select, input { width: 100%; box-sizing: border-box; }
        .item {
            padding: 15px 0;
            border-bottom: 1px solid var(--gnc-border);
        }
        .message { padding: 15px; border: 1px solid #777; }
        @media (max-width: 800px) {
            .sale-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="navbar">
    <a class="logo" href="../home.php">
        <img src="../../assets/images/GNC_Logo.svg.png" alt="GNC">
    </a>
    <a class="btn" href="../logout.php">Cerrar sesión</a>
</div>

<main class="container">
    <h1><?= $customer ? 'Comprar productos' : 'Nueva venta' ?></h1>

    <a class="btn" href="../home.php">Volver</a>
    <a
        class="btn"
        href="<?= $customer ? 'my-purchases.php' : 'sales-history.php' ?>"
    >
        Ver historial
    </a>

    <?php foreach (['error', 'success'] as $key): ?>
        <?php if (isset($_SESSION[$key])): ?>
            <p class="message" role="status">
                <?= gncEscape($_SESSION[$key]) ?>
            </p>
            <?php unset($_SESSION[$key]); ?>
        <?php endif; ?>
    <?php endforeach; ?>

    <div class="sale-grid">
        <section class="sale-card">
            <h2>Seleccionar productos</h2>

            <form method="post" action="../../controllers/SaleController.php">
                <?= gncCsrfField() ?>
                <input type="hidden" name="action" value="add">

                <label>
                    Producto
                    <select name="product_id" required>
                        <?php foreach ($products as $product): ?>
                            <option
                                value="<?= (int) $product['id'] ?>"
                                <?= (int) $product['stock'] < 1 ? 'disabled' : '' ?>
                            >
                                <?= gncEscape($product['name']) ?>
                                — ₡<?= number_format((float) $product['price'], 2) ?>
                                — Stock: <?= (int) $product['stock'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label>
                    Cantidad
                    <input
                        type="number"
                        name="quantity"
                        min="1"
                        max="100000"
                        value="1"
                        required
                    >
                </label>

                <button type="submit" class="btn add-btn">
                    Añadir al carrito
                </button>
            </form>
        </section>

        <section class="sale-card">
            <h2>Carrito</h2>

            <?php if (!$_SESSION['cart']): ?>
                <p>El carrito está vacío.</p>
            <?php else: ?>
                <?php foreach ($_SESSION['cart'] as $index => $item): ?>
                    <?php $total += (float) $item['subtotal']; ?>

                    <div class="item">
                        <strong><?= gncEscape($item['product_name']) ?></strong>

                        <p>
                            Cantidad: <?= (int) $item['quantity'] ?>
                            — ₡<?= number_format((float) $item['subtotal'], 2) ?>
                        </p>

                        <form
                            method="post"
                            action="../../controllers/SaleController.php"
                        >
                            <?= gncCsrfField() ?>
                            <input type="hidden" name="action" value="remove">
                            <input
                                type="hidden"
                                name="index"
                                value="<?= (int) $index ?>"
                            >
                            <button type="submit" class="btn">Quitar</button>
                        </form>
                    </div>
                <?php endforeach; ?>

                <p>
                    Total estimado:
                    <strong>₡<?= number_format($total, 2) ?></strong>
                </p>

                <p>El total final utiliza los precios vigentes al confirmar.</p>

                <form
                    method="post"
                    action="../../controllers/SaleController.php"
                >
                    <?= gncCsrfField() ?>
                    <input type="hidden" name="action" value="finish">
                    <input
                        type="hidden"
                        name="checkoutToken"
                        value="<?= gncEscape(gncCheckoutToken()) ?>"
                    >

                    <label>
                        Método de pago
                        <select name="payment_method_id" required>
                            <?php foreach ($methods as $method): ?>
                                <option value="<?= (int) $method['id'] ?>">
                                    <?= gncEscape($method['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <button
                        type="submit"
                        class="btn add-btn"
                        <?= !$methods ? 'disabled' : '' ?>
                    >
                        <?= $customer ? 'Confirmar compra' : 'Registrar venta' ?>
                    </button>
                </form>

                <br>

                <form
                    method="post"
                    action="../../controllers/SaleController.php"
                >
                    <?= gncCsrfField() ?>
                    <input type="hidden" name="action" value="empty">
                    <button type="submit" class="btn">Vaciar carrito</button>
                </form>
            <?php endif; ?>
        </section>
    </div>
</main>
</body>
</html>