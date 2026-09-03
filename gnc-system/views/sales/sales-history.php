<?php

session_start();

$inactive = 300;

if (isset($_SESSION['timeout'])) {

    $sessionLife = time() - $_SESSION['timeout'];

    if ($sessionLife > $inactive) {

        session_unset();

        session_destroy();

        header("Location: ../login.php");

        exit();
    }
}

$_SESSION['timeout'] = time();

if (!isset($_SESSION['user'])) {

    header("Location: ../login.php");

    exit();
}

include_once '../../models/SaleModel.php';

$saleModel = new SaleModel();

$sales = $saleModel->getSales();

?>

<!DOCTYPE html>
<html>

<head>
    <title>Historial de Ventas</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>

    <div class="navbar">
        <a href="../home.php" class="logo">
            <img src="../../assets/images/GNC_Logo.svg.png" alt="GNC Logo">
        </a>
        <div class="nav-actions">
            <a href="../logout.php" class="btn">Cerrar Sesión</a>
        </div>
    </div>

    <div class="container">
        <h1 class="title">Historial de Ventas</h1>
        <p class="subtitle">Registro completo de transacciones realizadas en el sistema.</p>

        <div style="margin-bottom: 25px;">
            <a href="../home.php" class="btn">Volver</a>
        </div>

        <table>

            <tr>

                <th>ID</th>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Total</th>
                <th>Usuario</th>

            </tr>

            <?php while ($row = $sales->fetch(PDO::FETCH_ASSOC)) { ?>

                <tr>

                    <td>

                        <?php echo $row['venta_id']; ?>

                    </td>

                    <td>

                        <?php echo $row['producto']; ?>

                    </td>

                    <td>

                        <?php echo $row['cantidad']; ?>

                    </td>

                    <td>

                        ₡<?php echo $row['total']; ?>

                    </td>

                    <td>

                        <?php echo $row['usuario']; ?>

                    </td>


                </tr>

            <?php } ?>

        </table>

</body>

</html>