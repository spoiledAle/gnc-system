<?php

include_once __DIR__ . '/../config/database.php';

class UserModel
{

    public function login(
        $email,
        $password
    ) {

        global $conn;

        $stmt = $conn->prepare("SELECT * FROM TblUser WHERE email = ?");

        $stmt->execute([$email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return false;
        }

        if (!password_verify($password, $user['password'])) {
            return false;
        }

        unset($user['password']);

        return $user;
    }

    public function register(
        $name,
        $email,
        $password
    ) {

        global $conn;

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO TblUser (
            name,
            email,
            password
        ) VALUES (?, ?, ?)";

        $stmt = $conn->prepare($sql);

        return $stmt->execute([$name, $email, $passwordHash]);
    }
}
