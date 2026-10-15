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

include_once '../../config/database.php';

$sql = "SELECT id, action_type, product_name, action_date
        FROM dbo.tbl_auditLogs
        ORDER BY action_date DESC, id DESC";

$result = $conn->query($sql);

$numero = (int) $conn
    ->query("SELECT COUNT(*) FROM dbo.tbl_auditLogs")
    ->fetchColumn();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auditorías</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>

    <?php
    $currentPage = 'audits';
    $basePath = '../';
    include __DIR__ . '/../partials/navbar.php';
    ?>

    <div class="container">
        <h1 class="title">Auditorías del Sistema</h1>

        <p class="subtitle">
            Registro automático de acciones realizadas sobre productos del inventario.
        </p>

        <div class="page-actions">
            <a href="../home.php" class="btn btn-secondary">← Volver</a>
        </div>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th style="width: 70px;">No.</th>
                        <th>Acción</th>
                        <th>Producto</th>
                        <th>Fecha</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if ($numero === 0) { ?>
                        <tr>
                            <td colspan="4">No hay auditorías registradas.</td>
                        </tr>
                    <?php } ?>

                    <?php while ($row = $result->fetch(PDO::FETCH_ASSOC)) { ?>
                        <tr>
                            <td><?php echo $numero--; ?></td>

                            <td>
                                <span class="badge"
                                      style="padding: 4px 8px;
                                             border-radius: 4px;
                                             font-size: 12px;
                                             font-weight: 600;
                                             background: #e0e7ff;
                                             color: #3730a3;">
                                    <?php
                                    echo htmlspecialchars(
                                        $row['action_type'] ?? '',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                    ?>
                                </span>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $row['product_name'] ?? '',
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $row['action_date'] ?? '',
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>