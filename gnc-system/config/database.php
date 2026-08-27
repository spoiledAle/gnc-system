<?php

// Nombre de tu instancia de SQL Server (el mismo que ves en "Server name" al
// conectarte con SSMS). Cada quien lo sobreescribe en su propio database.local.php
// -- ver config/database.local.example.php.
$host = "localhost";

$database = "GNCPROYECTO";

// Usuario/contraseña de SQL Server Authentication. Deja en null para usar
// Windows Authentication (Trusted Connection), que es lo que usa SSMS por defecto.
$user = null;

$password = null;

// Si existe un archivo de configuración local personalizado, sobreescribimos las variables.
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

?>