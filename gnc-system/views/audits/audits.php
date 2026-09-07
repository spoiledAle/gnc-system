<?php

session_start();

if(!isset($_SESSION['user'])) {

    header("Location: ../login.php");

    exit();

}

include_once '../../config/database.php';

$sql = "SELECT a.*, u.name AS user_name
        FROM tbl_auditLogs a
        LEFT JOIN tbl_users u ON a.user_id = u.id
        ORDER BY a.action_date DESC";

$result = $conn->query($sql);

$numero = $conn->query("SELECT COUNT(*) FROM tbl_auditLogs")->fetchColumn();

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

            <th>No.</th>

            <th>Acción</th>

            <th>Producto</th>

            <th>Usuario</th>

            <th>Fecha</th>

        </tr>

        <?php while($row = $result->fetch(PDO::FETCH_ASSOC)) { ?>

        <tr>

            <td>

                <?php echo $numero--; ?>

            </td>

            <td>

                <?php echo $row['action_type']; ?>

            </td>

            <td>

                <?php echo $row['product_name']; ?>

            </td>

            <td>

                <?php echo $row['user_name'] ?? 'Desconocido'; ?>

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