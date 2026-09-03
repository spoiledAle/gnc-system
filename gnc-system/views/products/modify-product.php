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

<title>

    Modificar Producto

</title>

<link rel="stylesheet" href="../../assets/css/style.css">


</head>

<body>


<div class="navbar">

    <a href="../home.php" class="logo">

        <img
            src="../../assets/images/GNC_Logo.svg.png"
            alt="GNC Logo"
        >

    </a>

    <div class="nav-actions">

        <a href="../logout.php" class="btn">

            Cerrar Sesión

        </a>

    </div>

</div>

<div class="container">

    <h1 class="title">

        Modificar Producto

    </h1>

    <p class="subtitle">

        Actualiza la información técnica o el inventario del suplemento seleccionado.

    </p>

    <div style="margin-bottom: 25px;">

        <a href="products.php" class="btn">

            Volver al Inventario

        </a>

    </div>

    <div style="max-width: 600px;">

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

            <div style="margin-bottom: 20px;">

                <label>

                    Categoría:

                </label>

                <select
                    name="category_id"
                    required
                    style="
                        width: 100%;
                        padding: 12px;
                        border-radius: 8px;
                        background: var(--gnc-bg-card);
                        color: white;
                        border: 1.5px solid var(--gnc-border);
                    "
                >

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

            <div style="margin-bottom: 20px;">

                <label>

                    Nombre del Producto:

                </label>

                <input
                    type="text"
                    name="name"
                    value="<?php echo $product['name']; ?>"
                    required
                >

            </div>

            <div style="margin-bottom: 20px;">

                <label>

                    Descripción:

                </label>

                <textarea
                    name="description"
                    style="
                        width: 100%;
                        min-height: 100px;
                        padding: 12px;
                        border-radius: 8px;
                        background: var(--gnc-bg-card);
                        color: white;
                        border: 1.5px solid var(--gnc-border);
                        outline: none;
                    "
                ><?php echo $product['description']; ?></textarea>

            </div>

            <div
                style="
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 20px;
                    margin-bottom: 20px;
                "
            >

                <div>

                    <label>

                        Precio (₡):

                    </label>

                    <input
                        type="number"
                        name="price"
                        value="<?php echo $product['price']; ?>"
                        required
                    >

                </div>

                <div>

                    <label>

                        Stock:

                    </label>

                    <input
                        type="number"
                        name="stock"
                        value="<?php echo $product['stock']; ?>"
                        required
                    >

                </div>

            </div>

            <div style="margin-bottom: 30px;">

                <label>

                    Nueva Imagen (Opcional):

                </label>

                <div
                    style="
                        display: flex;
                        align-items: center;
                        gap: 15px;
                        margin-top: 10px;
                    "
                >

                    <?php if($product['image']) { ?>

                        <img
                            src="../../assets/images/<?php echo $product['image']; ?>"
                            style="
                                width: 50px;
                                height: 50px;
                                border-radius: 5px;
                                object-fit: cover;
                            "
                        >

                    <?php } ?>

                    <input
                        type="file"
                        name="image"
                        style="padding: 10px 0;"
                    >

                </div>

            </div>

            <button
                type="submit"
                name="update"
                class="btn add-btn"
                style="width: 100%;"
            >

                Actualizar Producto

            </button>

        </form>

    </div>

</div>


</body>

</html>
