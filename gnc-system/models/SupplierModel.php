<?php

include_once __DIR__ . '/../config/database.php';

class SupplierModel {

    public function getSuppliers() {

        global $conn;

        return $conn->query("SELECT * FROM TblSupplier");

    }

    public function getSupplierById($id) {

        global $conn;
        $stmt = $conn->prepare("SELECT * FROM TblSupplier WHERE id = ?");
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
        $stmt = $conn->prepare("INSERT INTO TblSupplier (name, phone, email, address) VALUES (?, ?, ?, ?)");
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

        $sql = "UPDATE TblSupplier SET
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

        $stmt = $conn->prepare("DELETE FROM TblSupplier WHERE id = ?");

        return $stmt->execute([$id]);

    }

}
