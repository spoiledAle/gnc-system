<?php

require_once __DIR__ . '/../config/database.php';

class CustomerPurchaseModel
{
    public function getPurchases(): array
    {
        global $conn;

        return $conn->query("
            SELECT *
            FROM dbo.v_misCompras
            ORDER BY sale_date DESC, id DESC
        ")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPurchase(int $id): array|false
    {
        global $conn;

        $statement = $conn->prepare("
            SELECT *
            FROM dbo.v_misCompras
            WHERE id = ?
        ");

        $statement->execute([$id]);

        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function getDetails(int $id): array
    {
        global $conn;

        $statement = $conn->prepare("
            SELECT *
            FROM dbo.v_misCompraDetalles
            WHERE sale_id = ?
            ORDER BY product_name
        ");

        $statement->execute([$id]);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}