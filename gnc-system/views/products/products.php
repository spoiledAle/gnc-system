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

<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Productos</title>

<link rel="stylesheet" href="../../assets/css/style.css">

</head>

<body>

<?php $currentPage = 'products'; $basePath = '../'; include __DIR__ . '/../partials/navbar.php'; ?>

<div class="container">

    <h1 class="title">

        Inventario

    </h1>

    <p class="subtitle">

        Gestión centralizada de suplementos y stock disponible.

    </p>

    <div class="page-actions">

        <a href="../home.php" class="btn btn-secondary">

            ← Volver

        </a>

        <a href="add-product.php" class="btn add-btn">

            Agregar Producto

        </a>

    </div>

    <!-- FILTROS -->

    <div class="filter-container">

        <a href="products.php" class="btn filter-btn">Todos</a>

        <a href="products.php?filter=low" class="btn filter-btn">Bajo Stock</a>

        <a href="products.php?filter=out" class="btn filter-btn">Agotados</a>

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

    <div class="table-responsive">
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
                        alt="<?php echo htmlspecialchars($row['name']); ?>"
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
                    <div class="action-buttons">
                        <a href="modify-product.php?id=<?php echo $row['id']; ?>" class="btn btn-edit">
                            Editar
                        </a>
                        <a href="../../controllers/ProductController.php?id=<?php echo $row['id']; ?>" class="btn-danger">
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
