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

if(!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}
include_once '../../models/SupplierModel.php';
$supplierModel = new SupplierModel();
$id = $_GET['id'];
$supplier = $supplierModel->getSupplierById($id);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Proveedor</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <?php $currentPage = 'suppliers'; $basePath = '../'; include __DIR__ . '/../partials/navbar.php'; ?>

    <div class="container">
        <h1 class="title">Modificar Proveedor</h1>
        <p class="subtitle">Actualiza los datos de contacto o ubicación de tu socio comercial.</p>

        <div class="page-actions">
            <a href="suppliers.php" class="btn btn-secondary">← Volver a Proveedores</a>
        </div>

        <div class="form-container">
            <form method="POST" action="../../controllers/SupplierController.php">
                <input type="hidden" name="id" value="<?php echo $supplier['id']; ?>">
                
                <div class="form-group">
                    <label for="name">Nombre Comercial:</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($supplier['name']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="phone">Teléfono de Contacto:</label>
                    <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($supplier['phone']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Correo Electrónico:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($supplier['email']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="address">Dirección Física:</label>
                    <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($supplier['address']); ?>" required>
                </div>

                <button type="submit" name="update" class="btn add-btn btn-block">Actualizar Proveedor</button>
            </form>
        </div>
    </div>
</body>
</html>
