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

include_once '../../models/PaymentMethodModel.php';

$paymentModel = new PaymentMethodModel();
$result = $paymentModel->getMethods();

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Métodos de Pago</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>

    <?php $currentPage = 'payment_methods'; $basePath = '../'; include __DIR__ . '/../partials/navbar.php'; ?>

    <div class="container">
        <h1 class="title">Métodos de Pago</h1>
        <p class="subtitle">Opciones de cobro habilitadas en el punto de venta.</p>

        <div class="page-actions">
            <a href="../home.php" class="btn btn-secondary">← Volver</a>
            <a href="add-method.php" class="btn add-btn">Agregar Método</a>
        </div>

        <div class="table-responsive">
            <table>
                <tr>
                    <th style="width: 80px;">ID</th>
                    <th>Nombre</th>
                    <th style="width: 120px;">Acciones</th>
                </tr>

                <?php while ($row = $result->fetch(PDO::FETCH_ASSOC)) { ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td>
                            <a href="../../controllers/PaymentMethodController.php?id=<?php echo $row['id']; ?>" class="btn-danger">
                                Eliminar
                            </a>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>

</body>

</html>