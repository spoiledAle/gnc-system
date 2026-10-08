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
include_once '../../models/ProductModel.php';
$purchaseModel = new PurchaseModel();
$productModel = new ProductModel();
$suppliers = $purchaseModel->getSuppliers();
$products = $productModel->getProducts();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Pedido</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>
    <?php $currentPage = 'purchases'; $basePath = '../'; include __DIR__ . '/../partials/navbar.php'; ?>

    <div class="container">
        <h1 class="title">Nuevo Pedido</h1>
        <p class="subtitle">Registra las órdenes de compra enviadas a tus proveedores.</p>

        <div class="page-actions">
            <a href="purchases.php" class="btn btn-secondary">← Volver a Pedidos</a>
        </div>

        <div class="form-container">
            <form method="POST" action="../../controllers/PurchaseController.php">
                <div class="form-group">
                    <label for="supplier_id">Proveedor:</label>
                    <select id="supplier_id" name="supplier_id" required>
                        <option value="">Seleccione proveedor</option>
                        <?php while ($row = $suppliers->fetch(PDO::FETCH_ASSOC)) { ?>
                            <option value="<?php echo $row['id']; ?>">
                                <?php echo htmlspecialchars($row['name']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="product_id">Producto solicitado:</label>
                    <select id="product_id" name="product_id" required>
                        <option value="">Seleccione producto</option>
                        <?php while ($row = $products->fetch(PDO::FETCH_ASSOC)) { ?>
                            <option value="<?php echo $row['id']; ?>">
                                <?php echo htmlspecialchars($row['name']); ?> (Stock actual: <?php echo $row['stock']; ?>)
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="quantity_boxes">Cantidad (Cajas):</label>
                        <input type="number" id="quantity_boxes" name="quantity_boxes" min="1" required>
                    </div>
                    <div class="form-group">
                        <label for="total">Costo Total (₡):</label>
                        <input type="number" id="total" name="total" min="0" step="any" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="status">Estado Inicial:</label>
                    <select id="status" name="status">
                        <option value="Pendiente">Pendiente</option>
                        <option value="En camino">En camino</option>
                        <option value="Recibido">Recibido</option>
                    </select>
                </div>

                <button type="submit" class="btn add-btn btn-block">Registrar Pedido</button>
            </form>
        </div>
    </div>
</body>

</html>