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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $customer ? 'Comprar productos' : 'Ventas' ?></title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>
    <?php
    $currentPage = 'sales';
    $basePath = '../';
    include __DIR__ . '/../partials/navbar.php';
    ?>

    <main class="container">
        <h1 class="title">
            <?= $customer ? 'Comprar productos' : 'Nueva venta' ?>
        </h1>

        <p class="subtitle">
            <?php if ($customer): ?>
                Selecciona tus productos y confirma tu compra.
            <?php else: ?>
                Registra transacciones con control de stock.
            <?php endif; ?>
        </p>

        <div class="page-actions">
            <a class="btn btn-secondary" href="../home.php">
                Volver
            </a>
            <a
                class="btn btn-secondary"
                href="<?= $customer
                    ? 'my-purchases.php'
                    : 'sales-history.php' ?>"
            >
                <?= $customer ? 'Mis compras' : 'Historial de ventas' ?>
            </a>
        </div>

        <?php foreach (['error', 'success'] as $key): ?>
            <?php if (isset($_SESSION[$key])): ?>
                <div
                    class="alert <?= $key === 'error'
                        ? 'alert-error'
                        : 'alert-success' ?>"
                    role="<?= $key === 'error' ? 'alert' : 'status' ?>"
                >
                    <?= gncEscape($_SESSION[$key]) ?>
                </div>
                <?php unset($_SESSION[$key]); ?>
            <?php endif; ?>
        <?php endforeach; ?>

        <div class="sale-grid">
            <section class="sale-card">
                <h2>Seleccionar productos</h2>

                <?php if (!$products): ?>
                    <p>No hay productos disponibles.</p>
                <?php else: ?>
                    <form
                        method="POST"
                        action="../../controllers/SaleController.php"
                    >
                        <?= gncCsrfField() ?>

                        <input
                            type="hidden"
                            name="action"
                            value="add"
                        >

                        <div class="form-group">
                            <label for="product_id">Producto</label>
                            <select
                                id="product_id"
                                name="product_id"
                                required
                            >
                                <option value="">
                                    Selecciona un producto
                                </option>

                                <?php foreach ($products as $product): ?>
                                    <option
                                        value="<?= (int) $product['id'] ?>"
                                        <?= (int) $product['stock'] < 1
                                            ? 'disabled'
                                            : '' ?>
                                    >
                                        <?= gncEscape($product['name']) ?>
                                        — ₡<?= number_format(
                                            (float) $product['price'],
                                            2,
                                            ',',
                                            '.'
                                        ) ?>
                                        — Stock:
                                        <?= (int) $product['stock'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="quantity">Cantidad</label>
                            <input
                                type="number"
                                id="quantity"
                                name="quantity"
                                min="1"
                                max="100000"
                                value="1"
                                required
                            >
                        </div>

                        <button
                            type="submit"
                            class="btn btn-add-white btn-block"
                        >
                            Añadir al carrito
                        </button>
                    </form>
                <?php endif; ?>
            </section>

            <section class="sale-card">
                <h2>Carrito</h2>

                <?php if (!$_SESSION['cart']): ?>
                    <div class="empty-cart">
                        El carrito está vacío.
                    </div>
                <?php else: ?>
                    <?php foreach ($_SESSION['cart'] as $index => $item): ?>
                        <?php $total += (float) $item['subtotal']; ?>

                        <div class="item-row">
                            <div>
                                <div class="item-name">
                                    <?= gncEscape($item['product_name']) ?>
                                </div>
                                <div class="item-detail">
                                    Cantidad:
                                    <?= (int) $item['quantity'] ?>
                                </div>
                            </div>

                            <div>
                                <div class="item-price">
                                    ₡<?= number_format(
                                        (float) $item['subtotal'],
                                        2,
                                        ',',
                                        '.'
                                    ) ?>
                                </div>

                                <form
                                    method="POST"
                                    action="../../controllers/SaleController.php"
                                >
                                    <?= gncCsrfField() ?>

                                    <input
                                        type="hidden"
                                        name="action"
                                        value="remove"
                                    >
                                    <input
                                        type="hidden"
                                        name="index"
                                        value="<?= (int) $index ?>"
                                    >

                                    <button type="submit" class="btn">
                                        Quitar
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <div class="total-section">
                        <p>
                            <span class="total-label">
                                Total estimado:
                            </span>
                            <strong class="total-amount">
                                ₡<?= number_format(
                                    $total,
                                    2,
                                    ',',
                                    '.'
                                ) ?>
                            </strong>
                        </p>

                        <p>
                            El total final utiliza los precios
                            vigentes al confirmar.
                        </p>

                        <?php if (!$methods): ?>
                            <div class="alert alert-warning" role="status">
                                No hay métodos de pago disponibles.
                            </div>
                        <?php endif; ?>

                        <form
                            method="POST"
                            action="../../controllers/SaleController.php"
                        >
                            <?= gncCsrfField() ?>

                            <input
                                type="hidden"
                                name="action"
                                value="finish"
                            >
                            <input
                                type="hidden"
                                name="checkoutToken"
                                value="<?= gncEscape(gncCheckoutToken()) ?>"
                            >

                            <div class="form-group">
                                <label for="payment_method_id">
                                    Método de pago
                                </label>

                                <select
                                    id="payment_method_id"
                                    name="payment_method_id"
                                    required
                                >
                                    <option value="">
                                        Selecciona un método de pago
                                    </option>

                                    <?php foreach ($methods as $method): ?>
                                        <option
                                            value="<?= (int) $method['id'] ?>"
                                        >
                                            <?= gncEscape($method['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <button
                                type="submit"
                                class="btn add-btn btn-block"
                                <?= !$methods ? 'disabled' : '' ?>
                            >
                                <?= $customer
                                    ? 'Confirmar compra'
                                    : 'Registrar venta' ?>
                            </button>
                        </form>

                        <form
                            method="POST"
                            action="../../controllers/SaleController.php"
                            class="mt-1"
                        >
                            <?= gncCsrfField() ?>

                            <input
                                type="hidden"
                                name="action"
                                value="empty"
                            >

                            <button
                                type="submit"
                                class="btn btn-secondary btn-block"
                            >
                                Vaciar carrito
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </main>
</body>
</html>