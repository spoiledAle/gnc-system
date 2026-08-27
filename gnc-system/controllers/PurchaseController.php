<?php

session_start();

include_once '../models/PurchaseModel.php';
include_once '../models/ProductModel.php';

$purchaseModel = new PurchaseModel();
$productModel = new ProductModel();

if($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['update_status'])) {

    $supplier_id = $_POST['supplier_id'];

    $product_id = $_POST['product_id'];

    $quantity_boxes = $_POST['quantity_boxes'];

    $total = $_POST['total'];

    $status = $_POST['status'];

    if(
        empty($supplier_id) ||
        empty($product_id) ||
        empty($quantity_boxes) ||
        empty($total) ||
        empty($status)
    ) {

        echo "

        <h1>

            Todos los campos son obligatorios

        </h1>

        <a href='../views/purchases/add-purchase.php'>

            Volver

        </a>

        ";

        exit();

    }

    if($quantity_boxes <= 0) {

        echo "

        <h1>

            Cantidad inválida

        </h1>

        <a href='../views/purchases/add-purchase.php'>

            Volver

        </a>

        ";

        exit();

    }

    if($total <= 0) {

        echo "

        <h1>

            Total inválido

        </h1>

        <a href='../views/purchases/add-purchase.php'>

            Volver

        </a>

        ";

        exit();

    }

    // Obtener información del producto para registrar la compra
    $product = $productModel->getProductById($product_id);
    if (!$product) {
        echo "

        <h1>

            Producto no encontrado

        </h1>

        <a href='../views/purchases/add-purchase.php'>

            Volver

        </a>

        ";

        exit();
    }

    $product_name = $product['name'];

    // Iniciar transacción de base de datos
    $conn = $purchaseModel->getConnection();
    $conn->beginTransaction();

    try {
        // 1. Registrar compra cabecera
        $purchase_id = $purchaseModel->addPurchase(
            $supplier_id,
            $product_name,
            $quantity_boxes,
            $total,
            $status
        );

        if (!$purchase_id) {
            throw new Exception("Error al registrar la orden de compra.");
        }

        // 2. Registrar detalle de compra (Disparará el trigger para aumentar stock)
        $detail_inserted = $purchaseModel->addPurchaseDetail(
            $purchase_id,
            $product_id,
            $quantity_boxes,
            $total
        );

        if (!$detail_inserted) {
            throw new Exception("Error al registrar el detalle de la compra.");
        }

        // Confirmar transacción (Commit)
        $conn->commit();

        header("Location: ../views/purchases/purchases.php");
        exit();

    } catch (Exception $e) {
        // Deshacer cambios en caso de error (Rollback)
        $conn->rollBack();

        echo "
        <div style='font-family: sans-serif; padding: 20px; text-align: center;'>
            <h1 style='color: #d9534f;'>Error en la Transacción</h1>
            <p>" . htmlspecialchars($e->getMessage()) . "</p>
            <a href='../views/purchases/add-purchase.php' style='display: inline-block; padding: 10px 20px; background: #0275d8; color: white; text-decoration: none; border-radius: 4px;'>Volver a intentar</a>
        </div>
        ";
        exit();
    }

}

if(isset($_POST['update_status'])) {

    $id = $_POST['id'];

    $status = $_POST['status'];

    $purchaseModel->updateStatus(
        $id,
        $status
    );

    header("Location: ../views/purchases/purchases.php");
    exit();

}

?>
