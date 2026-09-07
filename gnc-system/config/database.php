<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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

// Dejamos el id del usuario logueado disponible para la sesión de SQL Server,
// así los triggers de auditoría (dis_audit*Product) saben quién hizo el cambio
// sin que cada modelo tenga que pasarlo a mano.
if (isset($_SESSION['user']['id'])) {

    $stmt = $conn->prepare("EXEC sp_set_session_context @key = N'user_id', @value = ?");

    $stmt->execute([$_SESSION['user']['id']]);

}

?>