<?php
session_start();
if(!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Producto</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <?php $currentPage = 'products'; $basePath = '../'; include __DIR__ . '/../partials/navbar.php'; ?>

    <div class="container">
        <h1 class="title">Nuevo Producto</h1>
        <p class="subtitle">Completa la información para dar de alta un nuevo suplemento en el inventario.</p>

        <div class="page-actions">
            <a href="products.php" class="btn btn-secondary">← Volver al Inventario</a>
        </div>

        <div class="form-container">
            <form action="../../controllers/ProductController.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Categoría:</label>
                    <select name="category_id">
                        <option value="1">Proteínas</option>
                        <option value="2">Creatinas</option>
                        <option value="3">Pre-entrenos</option>
                        <option value="4">Vitaminas</option>
                        <option value="5">Accesorios</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Nombre del Producto:</label>
                    <input type="text" name="name" required placeholder="Ej. Whey Gold Standard">
                </div>

                <div class="form-group">
                    <label>Descripción:</label>
                    <textarea name="description" placeholder="Descripción del producto..."></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Precio (₡):</label>
                        <input type="number" name="price" required>
                    </div>
                    <div class="form-group">
                        <label>Stock Inicial:</label>
                        <input type="number" name="stock" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Imagen del Producto:</label>
                    <input type="file" name="image" required>
                </div>

                <button type="submit" class="btn add-btn btn-block">
                    Guardar Producto
                </button>
            </form>
        </div>
    </div>
</body>
</html>
