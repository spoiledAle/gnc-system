<?php

include_once '../config/database.php';

echo "<h1>Prueba Unitaria - Auditorías</h1>";

$tables = ['tbl_auditProducts', 'tbl_auditSales', 'tbl_auditSessions'];

foreach ($tables as $table) {

    try {

        $count = $conn->query("SELECT COUNT(*) FROM $table")->fetchColumn();

        echo "

        <h2 style='color: green;'>

            TEST EXITOSO: $table

        </h2>

        <p>

            La tabla de auditoría es accesible. Registros encontrados: $count

        </p>

        ";

    } catch (PDOException $exception) {

        echo "

        <h2 style='color: red;'>

            TEST FALLIDO: $table

        </h2>

        <p>

            No se pudo consultar la tabla (no existe o el usuario no es administrador).

        </p>

        ";

    }

}

?>
