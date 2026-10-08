<?php

require_once __DIR__ . '/../../config/auth_check.php';
requireRole(['CLIENTE']);

require_once __DIR__ . '/../../config/forms.php';
require_once __DIR__ . '/../../models/CustomerPurchaseModel.php';

$model = new CustomerPurchaseModel();

$id = isset($_GET['id'])
    ? filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT)
    : null;

if (isset($_GET['id']) && (!$id || $id < 1)) {
    http_response_code(400);
    exit('Compra inválida.');
}

try {
    $purchases = $model->getPurchases();
    $purchase = $id ? $model->getPurchase($id) : null;

    if ($id && !$purchase) {
        http_response_code(404);
        exit('Compra no encontrada.');
    }

    $details = $id ? $model->getDetails($id) : [];
} catch (PDOException $exception) {
    error_log('GNC: Mis compras. Código: '
        . ($exception->errorInfo[1] ?? 0));

    http_response_code(503);
    exit('No se pudieron consultar las compras. Revisa el SQL adicional.');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mis compras</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
<?php
$currentPage = 'my-purchases';
$basePath = '../';
include __DIR__ . '/../partials/navbar.php';
?>

<main class="container">
    <h1 class="title">Mis compras</h1>
    <p class="subtitle">Consulta tus compras realizadas y el detalle de cada pedido.</p>

    <div class="page-actions">
        <a class="btn btn-secondary" href="../home.php">← Volver</a>
        <a class="btn add-btn" href="sales.php">Comprar productos</a>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success" role="status">
            <?= gncEscape($_SESSION['success']) ?>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (!$purchases): ?>
        <p style="color: var(--gnc-text-secondary); margin-top: 20px;">Aún no tienes compras registradas.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table>
                <thead>
                <tr>
                    <th>Compra</th>
                    <th>Fecha</th>
                    <th>Método de pago</th>
                    <th>Total</th>
                    <th>Detalle</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($purchases as $row): ?>
                    <tr>
                        <td>#<?= (int) $row['id'] ?></td>
                        <td><?= gncEscape($row['sale_date']) ?></td>
                        <td><?= gncEscape($row['payment_method']) ?></td>
                        <td>₡<?= number_format((float) $row['total'], 2) ?></td>
                        <td>
                            <a class="btn" href="?id=<?= (int) $row['id'] ?>">
                                Ver productos
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <?php if ($purchase): ?>
        <h2 style="margin-top: 35px; margin-bottom: 15px;">Detalle de la compra #<?= (int) $purchase['id'] ?></h2>

        <div class="table-responsive">
            <table>
                <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Subtotal</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($details as $row): ?>
                    <tr>
                        <td><?= gncEscape($row['product_name']) ?></td>
                        <td><?= (int) $row['quantity'] ?></td>
                        <td>₡<?= number_format((float) $row['subtotal'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</main>
</body>
</html>