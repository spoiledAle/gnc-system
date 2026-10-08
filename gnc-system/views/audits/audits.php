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

include_once '../../config/database.php';

$sql = "SELECT a.*, u.name AS user_name
        FROM tbl_auditLogs a
        LEFT JOIN tbl_users u ON a.user_id = u.id
        ORDER BY a.action_date DESC";

$result = $conn->query($sql);
$numero = $conn->query("SELECT COUNT(*) FROM tbl_auditLogs")->fetchColumn();

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

    <?php $currentPage = 'audits'; $basePath = '../'; include __DIR__ . '/../partials/navbar.php'; ?>

    <div class="container">
        <h1 class="title">Auditorías del Sistema</h1>
        <p class="subtitle">Registro automático de acciones realizadas sobre productos del inventario.</p>

        <div class="page-actions">
            <a href="../home.php" class="btn btn-secondary">← Volver</a>
        </div>

        <div class="table-responsive">
            <table>
                <tr>
                    <th style="width: 70px;">No.</th>
                    <th>Acción</th>
                    <th>Producto</th>
                    <th>Usuario</th>
                    <th>Fecha</th>
                </tr>

                <?php while($row = $result->fetch(PDO::FETCH_ASSOC)) { ?>
                    <tr>
                        <td><?php echo $numero--; ?></td>
                        <td>
                            <span class="badge" style="padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600; background: #e0e7ff; color: #3730a3;">
                                <?php echo htmlspecialchars($row['action_type']); ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['user_name'] ?? 'Desconocido'); ?></td>
                        <td><?php echo $row['action_date']; ?></td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>

</body>

</html>