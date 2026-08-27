<?php

include_once '../config/database.php';

echo "<h1>Prueba Unitaria - Auditorías</h1>";

$sql = "SELECT * FROM TblAuditLog";

$result = $conn->query($sql);

if($result) {

    $rows = $result->fetchAll(PDO::FETCH_ASSOC);

    echo "

    <h2 style='color: green;'>

        TEST EXITOSO

    </h2>

    <p>

        La tabla de auditorías es accesible y la consulta fue exitosa. Registros encontrados: " . count($rows) . "

    </p>

    ";

} else {

    echo "

    <h2 style='color: red;'>

        TEST FALLIDO

    </h2>

    <p>

        No existen auditorías registradas.

    </p>

    ";

}

?>