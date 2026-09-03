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

        FROM tbl_purchases purchase

        INNER JOIN tbl_suppliers supplier

        ON purchase.supplier_id = supplier.id";

        return $conn->query($sql);

    }

    public function getSuppliers() {

        global $conn;

        return $conn->query("SELECT * FROM tbl_suppliers");

    }

    public function addPurchase(
        $supplier_id,
        $product_name,
        $quantity_boxes,
        $total,
        $status
    ) {

        global $conn;

        $sql = "INSERT INTO tbl_purchases (
            supplier_id,
            product_name,
            quantity_boxes,
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

        $sql = "INSERT INTO tbl_purchaseDetails (
            purchase_id,
            product_id,
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

        $stmt = $conn->prepare("UPDATE tbl_purchases SET status = ? WHERE id = ?");

        return $stmt->execute([$status, $id]);

    }

}
