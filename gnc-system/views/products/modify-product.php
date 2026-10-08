<?php
session_start();

if(!isset($_SESSION['user'])) {

    header("Location: ../login.php");

    exit();
}

include_once '../../models/ProductModel.php';

$productModel = new ProductModel();

$id = $_GET['id'];

$product = $productModel->getProductById($id);

$categories = $productModel->getCategories();

?>

<!DOCTYPE html>

<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Modificar Producto</title>

<link rel="stylesheet" href="../../assets/css/style.css">

</head>

<body>

<?php $currentPage = 'products'; $basePath = '../'; include __DIR__ . '/../partials/navbar.php'; ?>

<div class="container">

    <h1 class="title">

        Modificar Producto

    </h1>

    <p class="subtitle">

        Actualiza la información técnica o el inventario del suplemento seleccionado.

    </p>

    <div class="page-actions">

        <a href="products.php" class="btn btn-secondary">

            ← Volver al Inventario

        </a>

    </div>

    <div class="form-container">

        <form
            action="../../controllers/ProductController.php"
            method="POST"
            enctype="multipart/form-data"
        >

            <input
                type="hidden"
                name="id"
                value="<?php echo $product['id']; ?>"
            >

            <div class="form-group">

                <label>Categoría:</label>

                <select name="category_id" required>

                    <?php while($category = $categories->fetch(PDO::FETCH_ASSOC)) { ?>

                        <option
                            value="<?php echo $category['id']; ?>"
                            <?php
                            if($category['id'] == $product['category_id']) {
                                echo "selected";
                            }
                            ?>
                        >

                            <?php echo $category['name']; ?>

                        </option>

                    <?php } ?>

                </select>

            </div>

            <div class="form-group">

                <label>Nombre del Producto:</label>

                <input
                    type="text"
                    name="name"
                    value="<?php echo $product['name']; ?>"
                    required
                >

            </div>

            <div class="form-group">

                <label>Descripción:</label>

                <textarea name="description"><?php echo $product['description']; ?></textarea>

            </div>

            <div class="form-row">

                <div class="form-group">

                    <label>Precio (₡):</label>

                    <input
                        type="number"
                        name="price"
                        value="<?php echo $product['price']; ?>"
                        required
                    >

                </div>

                <div class="form-group">

                    <label>Stock:</label>

                    <input
                        type="number"
                        name="stock"
                        value="<?php echo $product['stock']; ?>"
                        required
                    >

                </div>

            </div>

            <div class="form-group">

                <label>Nueva Imagen (Opcional):</label>

                <div class="form-image-row">

                    <?php if($product['image']) { ?>

                        <img
                            src="../../assets/images/<?php echo $product['image']; ?>"
                            class="form-image-preview"
                            alt="Imagen actual"
                        >

                    <?php } ?>

                    <input type="file" name="image">

                </div>

            </div>

            <button
                type="submit"
                name="update"
                class="btn add-btn btn-block"
            >

                Actualizar Producto

            </button>

        </form>

    </div>

</div>


</body>

</html>
