<?php

include_once __DIR__ . '/../config/database.php';
class SaleDetailModel {

    public function createSaleDetail(
        $sale_id,
        $product_id,
        $quantity,
        $subtotal
    ) {

        global $conn;

        $sql = "INSERT INTO tbl_saleDetails (
            sale_id,
            product_id,
            quantity,
            subtotal
        ) VALUES (?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);

        return $stmt->execute([$sale_id, $product_id, $quantity, $subtotal]);

    }

}
