<?php

include_once __DIR__ . '/../config/database.php';

class ProductModel
{


    public function getProducts()
    {
        global $conn;

        $stmt = $conn->query("SELECT * FROM TblProduct");

        return $stmt;
    }



    public function getProductById($id)
    {
        global $conn;

        $stmt = $conn->prepare("SELECT * FROM TblProduct WHERE id = ?");

        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }



    public function getLowStockProducts()
    {
        global $conn;

        $stmt = $conn->query("SELECT * FROM TblProduct WHERE stock < 5 AND stock > 0");

        return $stmt;
    }



    public function getOutOfStockProducts()
    {
        global $conn;

        $stmt = $conn->query("SELECT * FROM vOutOfStockProducts");

        return $stmt;
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

        $sql = "INSERT INTO TblProduct (
                    categoryId,
                    name,
                    description,
                    price,
                    stock,
                    image
                )
                VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);

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

        $sql = "UPDATE TblProduct
                SET categoryId = ?,
                    name = ?,
                    description = ?,
                    price = ?,
                    stock = ?,
                    image = ?
                WHERE id = ?";

        $stmt = $conn->prepare($sql);

        return $stmt->execute([
            $category_id,
            $name,
            $description,
            $price,
            $stock,
            $image,
            $id,
        ]);
    }



    public function deleteProduct($id)
    {
        global $conn;

        $stmt = $conn->prepare("DELETE FROM TblProduct WHERE id = ?");

        return $stmt->execute([$id]);
    }



    public function getCategories()
    {
        global $conn;

        $stmt = $conn->query("SELECT * FROM TblCategory");

        return $stmt;
    }



    public function getProductsByCategory($category_id)
    {
        global $conn;

        $stmt = $conn->prepare("SELECT * FROM TblProduct WHERE categoryId = ?");

        $stmt->execute([$category_id]);

        return $stmt;
    }
}

?>
