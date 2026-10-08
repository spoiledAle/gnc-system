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

if (!isset($_SESSION['user'])) {

    header("Location: ../login.php");

    exit();
}

include_once '../../models/SaleModel.php';

$saleModel = new SaleModel();

$sales = $saleModel->getSales();

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Ventas</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>

    <?php $currentPage = 'history'; $basePath = '../'; include __DIR__ . '/../partials/navbar.php'; ?>

    <div class="container">
        <h1 class="title">Historial de Ventas</h1>
        <p class="subtitle">Registro completo de transacciones realizadas en el sistema.</p>

        <div class="page-actions">
            <a href="../home.php" class="btn btn-secondary">← Volver</a>
            <a href="sales.php" class="btn add-btn">Nueva Venta</a>
        </div>

        <div class="table-responsive">
            <table>
                <tr>
                    <th>ID</th>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Total</th>
                    <th>Usuario</th>
                </tr>

                <?php while ($row = $sales->fetch(PDO::FETCH_ASSOC)) { ?>
                    <tr>
                        <td><?php echo $row['venta_id']; ?></td>
                        <td><?php echo htmlspecialchars($row['producto']); ?></td>
                        <td><?php echo $row['cantidad']; ?></td>
                        <td>₡<?php echo number_format($row['total'], 0, ',', '.'); ?></td>
                        <td><?php echo htmlspecialchars($row['usuario']); ?></td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>

</body>

</html>