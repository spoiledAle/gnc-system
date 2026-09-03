<?php

include_once __DIR__ . '/../config/database.php';

class SupplierModel {

    public function getSuppliers() {

        global $conn;

        return $conn->query("SELECT * FROM tbl_suppliers");

    }

    public function getSupplierById($id) {

        global $conn;
        $stmt = $conn->prepare("SELECT * FROM tbl_suppliers WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addSupplier(
        $name,
        $phone,
        $email,
        $address
    ) {
        global $conn;
        $stmt = $conn->prepare("INSERT INTO tbl_suppliers (name, phone, email, address) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$name, $phone, $email, $address]);
    }

    public function updateSupplier(
        $id,
        $name,
        $phone,
        $email,
        $address
    ) {

        global $conn;

        $sql = "UPDATE tbl_suppliers SET
                name = ?,
                phone = ?,
                email = ?,
                address = ?
                WHERE id = ?";

        $stmt = $conn->prepare($sql);

        return $stmt->execute([$name, $phone, $email, $address, $id]);

    }

    public function deleteSupplier($id) {

        global $conn;

        $stmt = $conn->prepare("DELETE FROM tbl_suppliers WHERE id = ?");

        return $stmt->execute([$id]);

    }

}
