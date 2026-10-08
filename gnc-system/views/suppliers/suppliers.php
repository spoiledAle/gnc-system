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

include_once '../../models/SupplierModel.php';

$supplierModel = new SupplierModel();
$result = $supplierModel->getSuppliers();

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proveedores</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>

    <?php $currentPage = 'suppliers'; $basePath = '../'; include __DIR__ . '/../partials/navbar.php'; ?>

    <div class="container">
        <h1 class="title">Proveedores</h1>
        <p class="subtitle">Directorio de socios comerciales y distribución.</p>

        <div class="page-actions">
            <a href="../home.php" class="btn btn-secondary">← Volver</a>
            <a href="add-supplier.php" class="btn add-btn">Agregar Proveedor</a>
        </div>

        <div class="table-responsive">
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
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['phone']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['address']); ?></td>
                        <td>
                            <div class="action-buttons">
                                <a href="modify-supplier.php?id=<?php echo $row['id']; ?>" class="btn btn-edit">
                                    Editar
                                </a>
                                <a href="../../controllers/SupplierController.php?id=<?php echo $row['id']; ?>" class="btn-danger">
                                    Eliminar
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>

</body>

</html>