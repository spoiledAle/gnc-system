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

include_once '../../models/ProductModel.php';

$productModel = new ProductModel();

/*
    FILTROS
*/
$filter = $_GET['filter'] ?? 'all';

$categoryFilter = $_GET['category'] ?? '';

if ($filter == 'low') {

    $result = $productModel->getLowStockProducts();

} elseif ($filter == 'out') {

    $result = $productModel->getOutOfStockProducts();

} elseif (!empty($categoryFilter)) {

    $result = $productModel->getProductsByCategory($categoryFilter);

} else {

    $result = $productModel->getProducts();

}

$categories = $productModel->getCategories();

?>

<!DOCTYPE html>

<html>

<head>


<title>Productos</title>

<link rel="stylesheet" href="../../assets/css/style.css">

<style>

    .product-img {

        width: 60px;

        height: 60px;

        object-fit: cover;

        border-radius: 8px;

        border: 1px solid var(--gnc-border);

    }

    .filter-container {

        margin-bottom: 20px;

    }

    .filter-btn {

        margin-right: 10px;

    }

    .category-filter {

        margin-top: 15px;

    }

</style>


</head>

<body>


<div class="navbar">

    <a href="../home.php" class="logo">

        <img src="../../assets/images/GNC_Logo.svg.png" alt="GNC Logo">

    </a>

    <div class="nav-actions">

        <a href="../logout.php" class="btn">

            Cerrar Sesión

        </a>

    </div>

</div>

<div class="container">

    <h1 class="title">

        Inventario

    </h1>

    <p class="subtitle">

        Gestión centralizada de suplementos y stock disponible.

    </p>

    <div style="margin-bottom: 25px;">

        <a href="../home.php" class="btn">

            Volver

        </a>

        <a href="add-product.php" class="btn add-btn">

            Agregar Producto

        </a>

    </div>

    <!-- FILTROS -->

    <div class="filter-container">

        <a href="products.php">

            <button class="btn filter-btn">

                Todos

            </button>

        </a>

        <a href="products.php?filter=low">

            <button class="btn filter-btn">

                Bajo Stock

            </button>

        </a>

        <a href="products.php?filter=out">

            <button class="btn filter-btn">

                Agotados

            </button>

        </a>

    </div>

    <!-- FILTRO POR CATEGORÍA -->

    <div class="category-filter">

        <form method="GET">

            <select name="category">

                <option value="">

                    Todas las categorías

                </option>

                <?php while($category = $categories->fetch(PDO::FETCH_ASSOC)) { ?>

                    <option value="<?php echo $category['id']; ?>">

                        <?php echo $category['name']; ?>

                    </option>

                <?php } ?>

            </select>

            <button type="submit" class="btn">

                Filtrar Categoría

            </button>

        </form>

    </div>

    <table>

        <tr>

            <th>ID</th>
            <th>Imagen</th>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Precio</th>
            <th>Stock</th>
            <th>Acciones</th>

        </tr>

        <?php while ($row = $result->fetch(PDO::FETCH_ASSOC)) { ?>

            <tr>

                <td>

                    <?php echo $row['id']; ?>

                </td>

                <td>

                    <img
                        src="../../assets/images/<?php echo $row['image']; ?>"
                        class="product-img"
                    >

                </td>

                <td>

                    <?php echo $row['name']; ?>

                </td>

                <td>

                    <?php echo $row['description']; ?>

                </td>

                <td>

                    ₡<?php echo $row['price']; ?>

                </td>

                <td>

                    <?php echo $row['stock']; ?>

                </td>

                <td>

                    <a href="modify-product.php?id=<?php echo $row['id']; ?>">

                        <button class="btn">

                            Editar

                        </button>

                    </a>

                    <a href="../../controllers/ProductController.php?id=<?php echo $row['id']; ?>">

                        <button class="btn">

                            Eliminar

                        </button>

                    </a>

                </td>

            </tr>

        <?php } ?>

    </table>

</div>


</body>

</html>
