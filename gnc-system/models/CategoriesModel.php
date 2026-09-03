<?php

include_once __DIR__ . '/../config/database.php';
class ProductModel {

    public function getProducts() {

        global $conn;

        $result = $conn->query("SELECT * FROM tbl_products");

        return $result;
    }

}

?>