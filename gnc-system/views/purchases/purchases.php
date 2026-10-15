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

include_once '../../models/PurchaseModel.php';

$purchaseModel = new PurchaseModel();
$result = $purchaseModel->getPurchases();

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedidos a Proveedores</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>

    <?php $currentPage = 'purchases'; $basePath = '../'; include __DIR__ . '/../partials/navbar.php'; ?>

    <div class="container">
        <h1 class="title">Pedidos a Proveedores</h1>
        <p class="subtitle">Gestión de abastecimiento con proveedores externos.</p>

        <div class="page-actions">
            <a href="../home.php" class="btn btn-secondary">← Volver</a>
            <a href="add-purchase.php" class="btn add-btn">Registrar Pedido</a>
        </div>

        <div class="table-responsive">
            <table>
                <tr>
                    <th>ID</th>
                    <th>Proveedor</th>
                    <th>Producto</th>
                    <th>Cajas</th>
                    <th>Total</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                </tr>

                <?php while ($row = $result->fetch(PDO::FETCH_ASSOC)) { ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['supplier']); ?></td>
                        <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                        <td><?php echo $row['quantity_boxes']; ?></td>
                        <td>₡<?php echo number_format($row['total'], 0, ',', '.'); ?></td>
                        <td>
                            <form method="POST" action="../../controllers/PurchaseController.php" style="margin: 0;">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <select name="status" onchange="this.form.submit()" style="padding: 6px 10px; font-size: 13px; border-radius: 6px; width: auto; min-width: 110px;">
                                    <option value="Pendiente" <?php if ($row['status'] == 'Pendiente') echo 'selected'; ?>>
                                        Pendiente
                                    </option>
                                    <option value="En camino" <?php if ($row['status'] == 'En camino') echo 'selected'; ?>>
                                        En camino
                                    </option>
                                    <option value="Recibido" <?php if ($row['status'] == 'Recibido') echo 'selected'; ?>>
                                        Recibido
                                    </option>
                                </select>
                                <input type="hidden" name="update_status" value="1">
                            </form>
                        </td>
                        <td><?php echo $row['purchase_date']; ?></td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>

</body>

</html>