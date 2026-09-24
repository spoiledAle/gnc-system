<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


$host = "localhost";

$database = "GNCPROYECTO";


$user = null;

$password = null;


if (file_exists(__DIR__ . '/database.local.php')) {
    include __DIR__ . '/database.local.php';
}

$dsn = "sqlsrv:Server=$host;Database=$database;TrustServerCertificate=true";

try {

    $conn = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

} catch (PDOException $e) {

    die("Error de conexión: " . $e->getMessage());

}

if (isset($_SESSION['user']['id'])) {

    $stmt = $conn->prepare("EXEC sp_set_session_context @key = N'user_id', @value = ?");

    $stmt->execute([$_SESSION['user']['id']]);

}

?>