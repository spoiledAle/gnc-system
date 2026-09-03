<?php

session_start();

if(!isset($_SESSION['user'])) {

    header("Location: ../login.php");

    exit();

}

include_once '../../config/database.php';

$sql = "SELECT * FROM tbl_auditLogs ORDER BY action_date DESC";

$result = $conn->query($sql);

?>

<!DOCTYPE html>

<html lang="es">

<head>

<meta charset="UTF-8">

<title>Auditorías</title>

<link
rel="stylesheet"
href="../../assets/css/style.css">

</head>

<body>

<div class="navbar">

    <a href="../home.php" class="logo">

        <img
        src="../../assets/images/GNC_Logo.svg.png"
        alt="GNC Logo">

    </a>

    <div class="nav-actions">

        <a href="../logout.php" class="btn">

            Cerrar Sesión

        </a>

    </div>

</div>

<div class="container">

    <h1 class="title">

        Auditorías del Sistema

    </h1>

    <p class="subtitle">

        Registro automático de acciones realizadas
        sobre productos del inventario.

    </p>

    <div style="margin-bottom: 25px;">

        <a href="../home.php" class="btn">

            Volver

        </a>

    </div>

    <table>

        <tr>

            <th>ID</th>

            <th>Acción</th>

            <th>Producto</th>

            <th>Fecha</th>

        </tr>

        <?php while($row = $result->fetch(PDO::FETCH_ASSOC)) { ?>

        <tr>

            <td>

                <?php echo $row['id']; ?>

            </td>

            <td>

                <?php echo $row['action_type']; ?>

            </td>

            <td>

                <?php echo $row['product_name']; ?>

            </td>

            <td>

                <?php echo $row['action_date']; ?>

            </td>

        </tr>

        <?php } ?>

    </table>

</div>

</body>

</html>