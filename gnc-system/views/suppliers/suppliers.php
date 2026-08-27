<?php

session_start();

if (!isset($_SESSION['user'])) {

    header("Location: ../login.php");

    exit();
}

include_once '../../models/SupplierModel.php';

$supplierModel = new SupplierModel();

$result = $supplierModel->getSuppliers();

?>

<!DOCTYPE html>
<html>

<head>
    <title>Proveedores</title>
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
        <h1 class="title">Proveedores</h1>
        <p class="subtitle">Directorio de socios comerciales y distribución.</p>

        <div style="margin-bottom: 25px;">
            <a href="../home.php" class="btn">Volver</a>
            <a href="add-supplier.php" class="btn add-btn">Agregar Proveedor</a>
        </div>

        <table>

            <tr>

                <th>ID</th>
                <th>Nombre</th>
                <th>Teléfono</th>
                <th>Email</th>
                <th>Dirección</th>
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

                        <?php echo $row['phone']; ?>

                    </td>

                    <td>

                        <?php echo $row['email']; ?>

                    </td>

                    <td>

                        <?php echo $row['address']; ?>

                    </td>

                    <td>
                        <a href="modify-supplier.php?id=<?php echo $row['id']; ?>">
                            <button class="btn">Editar</button>
                        </a>
                        <a href="../../controllers/SupplierController.php?id=<?php echo $row['id']; ?>">
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