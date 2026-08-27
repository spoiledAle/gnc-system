<?php
session_start();
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
    <title>Registrar Pedido</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>
    <div class="navbar">
        <a href="../home.php" class="logo">
            <img src="../../assets/images/GNC_Logo.svg.png" alt="GNC Logo">
        </a>
        <div class="nav-actions">
            <a href="../logout.php" class="btn">Cerrar Sesión</a>
        </div>
    </div>

    <div class="container">
        <h1 class="title">Nuevo Pedido</h1>
        <p class="subtitle">Registra las órdenes de compra enviadas a tus proveedores.</p>

        <div style="margin-bottom: 25px;">
            <a href="purchases.php" class="btn">Volver a Pedidos</a>
        </div>

        <div style="max-width: 500px;">
            <form method="POST" action="../../controllers/PurchaseController.php">
                <div style="margin-bottom: 15px;">
                    <label>Proveedor:</label>
                    <select name="supplier_id" required>
                        <option value="">Seleccione proveedor</option>
                        <?php while ($row = $suppliers->fetch(PDO::FETCH_ASSOC)) { ?>
                            <option value="<?php echo $row['id']; ?>">
                                <?php echo $row['name']; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div style="margin-bottom: 15px;">
                    <label>Producto solicitado:</label>
                    <select name="product_id" required>
                        <option value="">Seleccione producto</option>
                        <?php while ($row = $products->fetch(PDO::FETCH_ASSOC)) { ?>
                            <option value="<?php echo $row['id']; ?>">
                                <?php echo $row['name']; ?> (Stock actual: <?php echo $row['stock']; ?>)
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 15px;">
                    <div>
                        <label>Cantidad (Cajas):</label>
                        <input type="number" name="quantity_boxes" required>
                    </div>
                    <div>
                        <label>Costo Total (₡):</label>
                        <input type="number" name="total" required>
                    </div>
                </div>

                <div style="margin-bottom: 25px;">
                    <label>Estado Inicial:</label>
                    <select name="status">
                        <option value="Pendiente">Pendiente</option>
                        <option value="En camino">En camino</option>
                        <option value="Recibido">Recibido</option>
                    </select>
                </div>

                <button type="submit" class="btn add-btn" style="width: 100%;">Registrar Pedido</button>
            </form>
        </div>
    </div>
</body>

</html>