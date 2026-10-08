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

$products = $saleModel->getProducts();

$paymentMethods = $saleModel->getPaymentMethods();

if (!isset($_SESSION['cart'])) {

    $_SESSION['cart'] = [];
}

$edit_item = null;
if (isset($_SESSION['edit_index']) && isset($_SESSION['cart'][$_SESSION['edit_index']])) {
    $edit_item = $_SESSION['cart'][$_SESSION['edit_index']];
}

?>

<!DOCTYPE html>

<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ventas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>
    <?php $currentPage = 'sales'; $basePath = '../'; include __DIR__ . '/../partials/navbar.php'; ?>

    <div class="container">
        <h1 class="title">Nueva Venta</h1>
        <p class="subtitle">Registra transacciones y actualiza el stock en tiempo real.</p>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-triangle"></i> <?php echo $_SESSION['error'];
                                                                    unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo $_SESSION['success'];
                                                        unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <div class="sale-grid">
            <!-- Selección de Productos -->
            <div class="sale-card">
                <h2>
                    <?php echo $edit_item ? 'Actualizar Producto' : 'Selección de Productos'; ?>
                </h2>
                <form action="../../controllers/SaleController.php" method="POST">
                    <?php if ($edit_item): ?>
                        <input type="hidden" name="edit_index" value="<?php echo $_SESSION['edit_index']; ?>">
                    <?php endif; ?>

                    <div class="form-group">
                        <label>Seleccionar Producto</label>
                        <select name="product_id">
                            <?php while ($row = $products->fetch(PDO::FETCH_ASSOC)) { ?>
                                <option value="<?php echo $row['id']; ?>" <?php echo ($edit_item && $edit_item['product_id'] == $row['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($row['name']); ?> - Stock: <?php echo $row['stock']; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Cantidad:</label>
                        <input type="number" name="quantity" min="1" value="<?php echo $edit_item ? $edit_item['quantity'] : '1'; ?>" required>
                    </div>

                    <?php if ($edit_item): ?>
                        <button type="submit" name="update_item" class="btn btn-edit btn-block mb-1">
                            GUARDAR CAMBIOS
                        </button>
                        <a href="../../controllers/SaleController.php?action=cancel_edit" class="btn btn-secondary btn-block">
                            CANCELAR EDICIÓN
                        </a>
                    <?php else: ?>
                        <button type="submit" name="add_to_cart" class="btn btn-add-white btn-block">
                            AÑADIR AL CARRITO
                        </button>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Resumen de Carrito -->
            <div class="sale-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2 style="margin: 0; border: none; padding: 0;">Carrito</h2>
                    <?php if (!empty($_SESSION['cart'])): ?>
                        <a href="../../controllers/SaleController.php?action=empty" class="btn-danger" style="padding: 5px 12px; font-size: 11px;">
                            <i class="fas fa-trash-alt"></i> CANCELAR
                        </a>
                    <?php endif; ?>
                </div>

                <div class="mt-1">
                    <?php
                    $total = 0;
                    if (empty($_SESSION['cart'])) {
                        echo "<div class='empty-cart'>El carrito está vacío</div>";
                    } else {
                        foreach ($_SESSION['cart'] as $index => $item) {
                            echo "
                            <div class='item-row'>
                                <div>
                                    <div class='item-name'>" . htmlspecialchars($item['product_name']) . "</div>
                                    <div class='item-detail'>Cantidad: {$item['quantity']}</div>
                                </div>
                                <div style='display: flex; align-items: center; gap: 15px;'>
                                    <div class='item-price'>₡" . number_format($item['subtotal'], 2) . "</div>
                                    <div class='item-actions'>
                                        <a href='../../controllers/SaleController.php?action=edit&index=$index' class='edit-link' title='Editar'><i class='fas fa-pencil-alt'></i></a>
                                        <a href='../../controllers/SaleController.php?action=remove&index=$index' class='delete-link' title='Eliminar'><i class='fas fa-times'></i></a>
                                    </div>
                                </div>
                            </div>";
                            $total += $item['subtotal'];
                        }
                    }
                    ?>
                </div>

                <?php if (!empty($_SESSION['cart'])) { ?>
                    <div class="total-section">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                            <span class="total-label">Total a Pagar:</span>
                            <span class="total-amount">₡<?php echo number_format($total, 2); ?></span>
                        </div>

                        <form action="../../controllers/SaleController.php" method="POST">
                            <div class="form-group">
                                <label>Método de Pago:</label>
                                <select name="payment_method_id">
                                    <?php while ($method = $paymentMethods->fetch(PDO::FETCH_ASSOC)) { ?>
                                        <option value="<?php echo $method['id']; ?>"><?php echo $method['name']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <button type="submit" name="finish_sale" class="btn add-btn btn-block" style="padding: 15px;">
                                FINALIZAR VENTA
                            </button>
                        </form>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</body>

</html>