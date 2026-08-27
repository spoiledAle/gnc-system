<?php

include_once __DIR__ . '/../config/database.php';

class SaleModel {

    public function getConnection() {

        global $conn;

        return $conn;

    }

    public function getSales() {

        global $conn;

        return $conn->query("SELECT * FROM vSalesHistory");

    }

    public function getProducts() {

        global $conn;

        return $conn->query("SELECT * FROM TblProduct");

    }

    public function getPaymentMethods() {

        global $conn;

        return $conn->query("SELECT * FROM TblPaymentMethod");

    }

    public function getProductStock($product_id) {

        global $conn;

        $stmt = $conn->prepare("SELECT * FROM TblProduct WHERE id = ?");

        $stmt->execute([$product_id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);

    }

    public function getProductStockForUpdate($product_id) {

        global $conn;

        $stmt = $conn->prepare("SELECT * FROM TblProduct WITH (UPDLOCK, ROWLOCK) WHERE id = ?");

        $stmt->execute([$product_id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);

    }

    public function createSale(
        $total,
        $user_id,
        $payment_method_id
    ) {

        global $conn;

        $sql = "INSERT INTO TblSale (
            userId,
            paymentMethodId,
            total,
            saleDate
        ) VALUES (?, ?, ?, GETDATE())";

        $stmt = $conn->prepare($sql);

        if ($stmt->execute([$user_id, $payment_method_id, $total])) {
            return $conn->lastInsertId();
        }

        return false;

    }

    public function addSaleDetail(
        $sale_id,
        $product_id,
        $quantity,
        $subtotal
    ) {

        global $conn;

        $sql = "INSERT INTO TblSaleDetail (
            saleId,
            productId,
            quantity,
            subtotal
        ) VALUES (?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);

        return $stmt->execute([$sale_id, $product_id, $quantity, $subtotal]);

    }

}
