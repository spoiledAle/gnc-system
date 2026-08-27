<?php

session_start();

if (!isset($_SESSION['user'])) {

    header("Location: ../login.php");

    exit();
}

include_once '../../models/PaymentMethodModel.php';

$paymentModel = new PaymentMethodModel();

$result = $paymentModel->getMethods();

?>

<!DOCTYPE html>
<html>

<head>
    <title>Métodos de Pago</title>
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
        <h1 class="title">Métodos de Pago</h1>
        <p class="subtitle">Opciones de cobro habilitadas en el punto de venta.</p>

        <div style="margin-bottom: 25px;">
            <a href="../home.php" class="btn">Volver</a>
            <a href="add-method.php" class="btn add-btn">Agregar Método</a>
        </div>

        <table>

            <tr>

                <th>ID</th>
                <th>Nombre</th>
                <th>Acciones</th>

            </tr>

            <?php while ($row = $result->fetch(PDO::FETCH_ASSOC)) { ?>

                <tr>

                    <td>

                        <?php echo $row['id']; ?>

                    </td>

                    <td>

                        <?php echo $row['name']; ?>

                    </td>

                    <td>

                        <a href="../../controllers/PaymentMethodController.php?id=<?php echo $row['id']; ?>">

                            <button class="delete-btn">

                                Eliminar

                            </button>

                        </a>

                    </td>

                </tr>

            <?php } ?>

        </table>

</body>

</html>