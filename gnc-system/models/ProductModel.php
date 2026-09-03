<?php

include_once __DIR__ . '/../config/database.php';

class ProductModel
{


    public function getProducts()
    {
        global $conn;

        return $conn->query("SELECT * FROM tbl_products");
    }



    public function getProductById($id)
    {
        global $conn;

        $stmt = $conn->prepare("SELECT * FROM tbl_products WHERE id = ?");

        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }



    public function getLowStockProducts()
    {
        global $conn;

        return $conn->query("SELECT * FROM tbl_products WHERE stock < 5 AND stock > 0");
    }



    public function getOutOfStockProducts()
    {
        global $conn;

        return $conn->query("SELECT * FROM tbl_products WHERE stock = 0");
    }



    public function addProduct(
        $category_id,
        $name,
        $description,
        $price,
        $stock,
        $image
    ) {
        global $conn;

        $stmt = $conn->prepare("{CALL pa_insertarProducto(?, ?, ?, ?, ?, ?)}");

        return $stmt->execute([
            $category_id,
            $name,
            $description,
            $price,
            $stock,
            $image,
        ]);
    }



    public function updateProduct(
        $id,
        $category_id,
        $name,
        $description,
        $price,
        $stock,
        $image
    ) {
        global $conn;

        $stmt = $conn->prepare("{CALL pa_actualizarProducto(?, ?, ?, ?, ?, ?, ?)}");

        return $stmt->execute([
            $id,
            $category_id,
            $name,
            $description,
            $price,
            $stock,
            $image,
        ]);
    }



    public function deleteProduct($id)
    {
        global $conn;

        $stmt = $conn->prepare("{CALL pa_eliminarProducto(?)}");

        return $stmt->execute([$id]);
    }



    public function getCategories()
    {
        global $conn;

        return $conn->query("SELECT * FROM tbl_categories");
    }



    public function getProductsByCategory($category_id)
    {
        global $conn;

        $stmt = $conn->prepare("SELECT * FROM tbl_products WHERE category_id = ?");

        $stmt->execute([$category_id]);

        return $stmt;
    }
}
