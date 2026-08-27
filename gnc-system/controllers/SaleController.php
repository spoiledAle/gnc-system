<?php
session_start();
include_once '../models/SaleModel.php';

$saleModel = new SaleModel();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_GET['action'])) {

    if ($_GET['action'] == 'remove' && isset($_GET['index'])) {

        unset($_SESSION['cart'][$_GET['index']]);

        $_SESSION['cart'] = array_values($_SESSION['cart']);
    }

    if ($_GET['action'] == 'empty') {

        $_SESSION['cart'] = [];

        unset($_SESSION['edit_index']);
    }

    if ($_GET['action'] == 'edit' && isset($_GET['index'])) {

        $_SESSION['edit_index'] = $_GET['index'];
    }

    if ($_GET['action'] == 'cancel_edit') {

        unset($_SESSION['edit_index']);
    }

    header("Location: ../views/sales/sales.php");

    exit();
}

if (isset($_POST['add_to_cart']) || isset($_POST['update_item'])) {

    $product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);

    $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);

    if (empty($quantity) || $quantity <= 0) {

        $_SESSION['error'] = "La cantidad ingresada no es válida.";

        header("Location: ../views/sales/sales.php");

        exit();
    }

    $product = $saleModel->getProductStock($product_id);

    if (!$product) {

        $_SESSION['error'] = "El producto seleccionado no existe.";

        header("Location: ../views/sales/sales.php");

        exit();
    }

    if ($quantity > $product['stock']) {

        $_SESSION['error'] = "Stock insuficiente para " . htmlspecialchars($product['name']) . ".";

        header("Location: ../views/sales/sales.php");

        exit();
    }

    $subtotal = $product['price'] * $quantity;

    $itemData = [

        'product_id' => $product_id,

        'product_name' => $product['name'],

        'quantity' => $quantity,

        'subtotal' => $subtotal

    ];

    if (isset($_POST['update_item']) && isset($_POST['edit_index'])) {

        $index = $_POST['edit_index'];

        $_SESSION['cart'][$index] = $itemData;

        unset($_SESSION['edit_index']);

    } else {

        $found = false;

        foreach ($_SESSION['cart'] as &$cartItem) {

            if ($cartItem['product_id'] == $product_id) {

                $newQuantity = $cartItem['quantity'] + $quantity;

                if ($newQuantity > $product['stock']) {

                    $_SESSION['error'] = "No hay suficiente stock disponible.";

                    header("Location: ../views/sales/sales.php");

                    exit();
                }

                $cartItem['quantity'] = $newQuantity;

                $cartItem['subtotal'] =
                    $cartItem['quantity'] * $product['price'];

                $found = true;

                break;
            }
        }

        if (!$found) {

            $_SESSION['cart'][] = $itemData;
        }
    }

    header("Location: ../views/sales/sales.php");

    exit();
}

if (isset($_POST['finish_sale'])) {

    if (empty($_SESSION['cart'])) {

        $_SESSION['error'] =
            "No puedes finalizar una venta con el carrito vacío.";

        header("Location: ../views/sales/sales.php");

        exit();
    }

    $payment_method_id = $_POST['payment_method_id'];

    $total = 0;

    foreach ($_SESSION['cart'] as $item) {

        $total += $item['subtotal'];
    }

    $user_id = $_SESSION['user']['id'];

    $conn = $saleModel->getConnection();

    $conn->beginTransaction();

    try {

        $sale_id = $saleModel->createSale(

            $total,

            $user_id,

            $payment_method_id
        );

        if (!$sale_id) {

            throw new Exception(
                "No se pudo registrar la cabecera de la venta."
            );
        }

        foreach ($_SESSION['cart'] as $item) {

            $product =
                $saleModel->getProductStockForUpdate(
                    $item['product_id']
                );

            if (!$product) {

                throw new Exception(
                    "Producto no encontrado."
                );
            }

            if ($item['quantity'] > $product['stock']) {

                throw new Exception(
                    "Stock insuficiente para "
                    . $product['name']
                    . ". Disponible: "
                    . $product['stock']
                );
            }

            $detail_inserted = $saleModel->addSaleDetail(

                $sale_id,

                $item['product_id'],

                $item['quantity'],

                $item['subtotal']
            );

            if (!$detail_inserted) {

                throw new Exception(
                    "Error al insertar el detalle de la venta."
                );
            }
        }

        $conn->commit();

        $_SESSION['cart'] = [];

        $_SESSION['success'] =
            "Venta registrada exitosamente.";

        header(
            "Location: ../views/sales/sales-history.php"
        );

        exit();

    } catch (Exception $e) {

        $conn->rollBack();

        $_SESSION['error'] = $e->getMessage();

        header("Location: ../views/sales/sales.php");

        exit();
    }
}

// Alessandro add
?>
