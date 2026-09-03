<?php

session_start();

if (!isset($_SESSION['user'])) {

    header("Location: ../login.php");

    exit();
}

include_once '../../models/PurchaseModel.php';

$purchaseModel = new PurchaseModel();

$result = $purchaseModel->getPurchases();

?>

<!DOCTYPE html>

<html>

<head>
    <title>Pedidos a Proveedores</title>
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
        <h1 class="title">Pedidos a Proveedores</h1>
        <p class="subtitle">Gestión de abastecimiento con proveedores externos.</p>

        <div style="margin-bottom: 25px;">
            <a href="../home.php" class="btn">Volver</a>
            <a href="add-purchase.php" class="btn add-btn">Registrar Pedido</a>
        </div>

        <table>


            <tr>

                <th>ID</th>
                <th>Proveedor</th>
                <th>Producto</th>
                <th>Cajas</th>
                <th>Total</th>
                <th>Estado</th>
                <th>Fecha</th>

            </tr>

            <?php while ($row = $result->fetch(PDO::FETCH_ASSOC)) { ?>

                <tr>

                    <td>

                        <?php echo $row['id']; ?>

                    </td>

                    <td>

                        <?php echo $row['supplier']; ?>

                    </td>

                    <td>

                        <?php echo $row['product_name']; ?>

                    </td>

                    <td>

                        <?php echo $row['quantity_boxes']; ?>

                    </td>

                    <td>

                        ₡<?php echo $row['total']; ?>

                    </td>

                    <td>

                        <form
                            method="POST"
                            action="../../controllers/PurchaseController.php">

                            <input
                                type="hidden"
                                name="id"
                                value="<?php echo $row['id']; ?>">

                            <select
                                name="status"
                                onchange="this.form.submit()">

                                <option
                                    value="Pendiente"

                                    <?php

                                    if ($row['status'] == 'Pendiente') {

                                        echo 'selected';
                                    }

                                    ?>>

                                    Pendiente

                                </option>

                                <option
                                    value="En camino"

                                    <?php

                                    if ($row['status'] == 'En camino') {

                                        echo 'selected';
                                    }

                                    ?>>

                                    En camino

                                </option>

                                <option
                                    value="Recibido"

                                    <?php

                                    if ($row['status'] == 'Recibido') {

                                        echo 'selected';
                                    }

                                    ?>>

                                    Recibido

                                </option>

                            </select>

                            <input
                                type="hidden"
                                name="update_status"
                                value="1">

                        </form>

                    </td>

                    <td>

                        <?php echo $row['purchase_date']; ?>

                    </td>

                </tr>

            <?php } ?>


        </table>

</body>

</html>