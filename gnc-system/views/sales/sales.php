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

<html>

<head>
    <title>Ventas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        .sale-grid {
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 40px;
            margin-top: 20px;
        }

        .sale-card {
            background-color: var(--gnc-bg-card);
            padding: 30px;
            border-radius: 16px;
            border: 1px solid var(--gnc-border);
            height: fit-content;
        }

        .item-row {
            padding: 15px 0;
            border-bottom: 1px solid var(--gnc-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 10px;
            color: var(--gnc-text-secondary);
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
        }

        select,
        input[type="number"] {
            width: 100%;
            padding: 12px;
            background: #252525;
            border: 1px solid var(--gnc-border);
            border-radius: 8px;
            color: white;
            font-family: inherit;
        }

        .btn-add {
            background-color: #fff;
            color: #000;
            font-weight: 700;
            transition: all 0.3s;
        }

        .btn-add:hover {
            background-color: var(--gnc-red);
            color: #fff;
        }

        .total-section {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px dashed var(--gnc-border);
        }

        .empty-cart {
            text-align: center;
            padding: 40px 0;
            color: var(--gnc-text-secondary);
            font-style: italic;
        }

        .item-actions a {
            color: var(--gnc-text-secondary);
            margin-left: 10px;
            font-size: 14px;
            transition: 0.2s;
        }

        .item-actions a.edit-link:hover {
            color: #3498db;
        }

        .item-actions a.delete-link:hover {
            color: var(--gnc-red);
        }
    </style>
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
        <h1 class="title">Nueva Venta</h1>
        <p class="subtitle">Registra transacciones y actualiza el stock en tiempo real.</p>

        <?php if (isset($_SESSION['error'])): ?>
            <div style="background: rgba(227, 24, 55, 0.15); border: 1px solid var(--gnc-red); color: white; padding: 15px; border-radius: 8px; margin-bottom: 25px; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-exclamation-triangle" style="color: var(--gnc-red);"></i> <?php echo $_SESSION['error'];
                                                                                            unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
            <div style="background: rgba(46, 204, 113, 0.15); border: 1px solid #2ecc71; color: white; padding: 15px; border-radius: 8px; margin-bottom: 25px; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-check-circle" style="color: #2ecc71;"></i> <?php echo $_SESSION['success'];
                                                                            unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <div class="sale-grid">
            <!-- Selección de Productos -->
            <div class="sale-card">
                <h2 style="margin-bottom: 25px; border-bottom: 2px solid var(--gnc-red); display: inline-block;">
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
                        <button type="submit" name="update_item" class="btn btn-add" style="width: 100%; margin-bottom: 10px; background-color: #3498db; color: white;">
                            GUARDAR CAMBIOS
                        </button>
                        <a href="../../controllers/SaleController.php?action=cancel_edit" class="btn" style="width: 100%; display: block; text-align: center; border: 1px solid var(--gnc-border); color: var(--gnc-text-secondary);">
                            CANCELAR EDICIÓN
                        </a>
                    <?php else: ?>
                        <button type="submit" name="add_to_cart" class="btn btn-add" style="width: 100%;">
                            AÑADIR AL CARRITO
                        </button>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Resumen de Carrito -->
            <div class="sale-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2 style="margin: 0; font-size: 1.2rem; text-transform: uppercase;">Carrito</h2>
                    <?php if (!empty($_SESSION['cart'])): ?>
                        <a href="../../controllers/SaleController.php?action=empty" class="btn" style="padding: 5px 12px; font-size: 11px; border: 1px solid var(--gnc-red); color: var(--gnc-red); font-weight: 700;">
                            <i class="fas fa-trash-alt"></i> CANCELAR VENTA
                        </a>
                    <?php endif; ?>
                </div>

                <div style="margin: 20px 0;">
                    <?php
                    $total = 0;
                    if (empty($_SESSION['cart'])) {
                        echo "<div class='empty-cart'>El carrito está vacío</div>";
                    } else {
                        foreach ($_SESSION['cart'] as $index => $item) {
                            echo "
                            <div class='item-row'>
                                <div>
                                    <div style='font-weight: 600;'>" . htmlspecialchars($item['product_name']) . "</div>
                                    <div style='font-size: 13px; color: var(--gnc-text-secondary);'>Cantidad: {$item['quantity']}</div>
                                </div>
                                <div style='display: flex; align-items: center; gap: 15px;'>
                                    <div style='color: var(--gnc-red); font-weight: 800;'>₡" . number_format($item['subtotal'], 2) . "</div>
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
                            <span style="font-weight: 600; text-transform: uppercase;">Total a Pagar:</span>
                            <span style="color: var(--gnc-red); font-size: 28px; font-weight: 800;">₡<?php echo number_format($total, 2); ?></span>
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
                            <button type="submit" name="finish_sale" class="btn" style="width: 100%; background: var(--gnc-red); padding: 15px;">
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