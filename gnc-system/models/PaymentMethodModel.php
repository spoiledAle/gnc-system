<?php

include_once __DIR__ . '/../config/database.php';

class PaymentMethodModel {

    public function getMethods() {

        global $conn;

        return $conn->query("SELECT * FROM tbl_paymentMethods");

    }

    public function addMethod($name) {

        global $conn;

        $stmt = $conn->prepare("INSERT INTO tbl_paymentMethods (name) VALUES (?)");

        return $stmt->execute([$name]);

    }

    public function deleteMethod($id) {

        global $conn;

        $stmt = $conn->prepare("DELETE FROM tbl_paymentMethods WHERE id = ?");

        return $stmt->execute([$id]);

    }

}
