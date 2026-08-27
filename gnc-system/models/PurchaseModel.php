<?php

include_once __DIR__ . '/../config/database.php';

class PurchaseModel {

    public function getConnection() {
        global $conn;
        return $conn;
    }

    public function getPurchases() {

        global $conn;

        $sql = "SELECT purchase.*,

        supplier.name AS supplier

        FROM TblPurchase purchase

        INNER JOIN TblSupplier supplier

        ON purchase.supplierId = supplier.id";

        return $conn->query($sql);

    }

    public function getSuppliers() {

        global $conn;

        return $conn->query("SELECT * FROM TblSupplier");

    }

    public function addPurchase(
        $supplier_id,
        $product_name,
        $quantity_boxes,
        $total,
        $status
    ) {

        global $conn;

        $sql = "INSERT INTO TblPurchase (
            supplierId,
            productName,
            quantityBoxes,
            total,
            status
        ) VALUES (?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);

        if ($stmt->execute([$supplier_id, $product_name, $quantity_boxes, $total, $status])) {
            return $conn->lastInsertId();
        }

        return false;

    }

    public function addPurchaseDetail(
        $purchase_id,
        $product_id,
        $quantity,
        $subtotal
    ) {

        global $conn;

        $sql = "INSERT INTO TblPurchaseDetail (
            purchaseId,
            productId,
            quantity,
            subtotal
        ) VALUES (?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);

        return $stmt->execute([$purchase_id, $product_id, $quantity, $subtotal]);

    }

    public function updateStatus(
        $id,
        $status
    ) {

        global $conn;

        $stmt = $conn->prepare("UPDATE TblPurchase SET status = ? WHERE id = ?");

        return $stmt->execute([$status, $id]);

    }

}
