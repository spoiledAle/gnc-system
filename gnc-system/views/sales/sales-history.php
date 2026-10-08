<?php

require_once __DIR__ . '/../../config/auth_check.php';

if ($_SESSION['user']['role'] === 'CLIENTE') {
    header('Location: my-purchases.php');
    exit;
}

requireRole(['ADMIN', 'EMPLEADO']);

require_once __DIR__ . '/../../config/forms.php';
require_once __DIR__ . '/../../models/SaleModel.php';

try {
    $saleModel = new SaleModel();
    $statement = $saleModel->getSales();

    $sales = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement->closeCursor();
} catch (PDOException $exception) {
    error_log(
        'GNC: historial de ventas. Código: '
        . ($exception->errorInfo[1] ?? 0)
    );

    http_response_code(503);
    exit('No se pudo consultar el historial. Revisa la consola PHP.');
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de ventas</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>
    <?php
    $currentPage = 'history';
    $basePath = '../';
    include __DIR__ . '/../partials/navbar.php';
    ?>

    <main class="container">
        <h1 class="title">Historial de ventas</h1>

        <p class="subtitle">
            Registro de transacciones realizadas en el sistema.
            Cada fila muestra un producto de la transacción.
        </p>

        <div class="page-actions">
            <a href="../home.php" class="btn btn-secondary">
                Volver
            </a>
            <a href="sales.php" class="btn add-btn">
                Nueva venta
            </a>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success" role="status">
                <?= gncEscape($_SESSION['success']) ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (!$sales): ?>
            <p>No hay ventas registradas.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID de venta</th>
                            <th>Fecha</th>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Subtotal del producto</th>
                            <th>Total de la venta</th>
                            <th>Método de pago</th>
                            <th>Usuario</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($sales as $row): ?>
                            <tr>
                                <td><?= (int) $row['venta_id'] ?></td>
                                <td>
                                    <?= gncEscape($row['fecha_venta']) ?>
                                </td>
                                <td>
                                    <?= gncEscape($row['producto']) ?>
                                </td>
                                <td><?= (int) $row['cantidad'] ?></td>
                                <td>
                                    ₡<?= number_format(
                                        (float) $row['subtotal'],
                                        2,
                                        ',',
                                        '.'
                                    ) ?>
                                </td>
                                <td>
                                    ₡<?= number_format(
                                        (float) $row['total'],
                                        2,
                                        ',',
                                        '.'
                                    ) ?>
                                </td>
                                <td>
                                    <?= gncEscape($row['metodo_pago']) ?>
                                </td>
                                <td>
                                    <?= gncEscape($row['usuario']) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>