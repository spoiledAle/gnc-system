<?php

include_once __DIR__ . '/../../config/database.php';

requireAdmin();

$productAudits = $conn->query("
    SELECT TOP 200 a.*, u.name AS user_name
    FROM tbl_auditProducts a
    LEFT JOIN tbl_users u ON u.id = a.user_id
    ORDER BY a.id DESC
")->fetchAll(PDO::FETCH_ASSOC);

$saleAudits = $conn->query("
    SELECT TOP 200 a.*, u.name AS user_name
    FROM tbl_auditSales a
    LEFT JOIN tbl_users u ON u.id = a.user_id
    ORDER BY a.id DESC
")->fetchAll(PDO::FETCH_ASSOC);

$sessionAudits = $conn->query("
    SELECT TOP 200 a.*, u.name AS user_name
    FROM tbl_auditSessions a
    LEFT JOIN tbl_users u ON u.id = a.user_id
    ORDER BY a.id DESC
")->fetchAll(PDO::FETCH_ASSOC);

function e($value): string
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
}

// Muestra "antes → después" solo cuando el valor cambió.
function change($old, $new): string
{
    if ($old === null) {
        return e($new);
    }

    if ($new === null || (string) $old === (string) $new) {
        return e($old);
    }

    return e($old) . ' → ' . e($new);
}

// Convierte los JSON de tbl_auditSales en una lista "campo: antes → después".
function jsonChanges(?string $oldJson, ?string $newJson): string
{
    $old = $oldJson ? json_decode($oldJson, true) : [];
    $new = $newJson ? json_decode($newJson, true) : [];

    $lines = [];

    foreach (array_unique(array_merge(array_keys($old ?? []), array_keys($new ?? []))) as $field) {
        $before = $old[$field] ?? null;
        $after = $new[$field] ?? null;

        if ($oldJson && $newJson && (string) $before === (string) $after) {
            continue;
        }

        $lines[] = e($field) . ': ' . change(
            $oldJson ? $before : null,
            $newJson ? $after : null
        );
    }

    return implode('<br>', $lines);
}

function userLabel(array $row): string
{
    return e($row['user_name'] ?? $row['login_name']);
}

$badge = 'padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600; background: #e0e7ff; color: #3730a3;';

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
        <p class="subtitle">Registro automático de cambios en productos, ventas y sesiones (últimos 200 de cada uno).</p>

        <div class="page-actions">
            <a href="../home.php" class="btn btn-secondary">← Volver</a>
        </div>

        <h2>Productos</h2>
        <div class="table-responsive">
            <table>
                <tr>
                    <th>Acción</th>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Usuario</th>
                    <th>Fecha</th>
                </tr>

                <?php foreach ($productAudits as $row): ?>
                    <tr>
                        <td><span class="badge" style="<?= $badge ?>"><?= e($row['operation']) ?></span></td>
                        <td><?= e($row['product_id']) ?></td>
                        <td><?= change($row['old_name'], $row['new_name']) ?></td>
                        <td><?= change($row['old_price'], $row['new_price']) ?></td>
                        <td><?= change($row['old_stock'], $row['new_stock']) ?></td>
                        <td><?= userLabel($row) ?></td>
                        <td><?= e($row['action_date']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <h2>Ventas</h2>
        <div class="table-responsive">
            <table>
                <tr>
                    <th>Acción</th>
                    <th>Tabla</th>
                    <th>Venta</th>
                    <th>Cambios</th>
                    <th>Usuario</th>
                    <th>Fecha</th>
                </tr>

                <?php foreach ($saleAudits as $row): ?>
                    <tr>
                        <td><span class="badge" style="<?= $badge ?>"><?= e($row['operation']) ?></span></td>
                        <td><?= $row['table_name'] === 'tbl_sales' ? 'Venta' : 'Detalle #' . e($row['record_id']) ?></td>
                        <td>#<?= e($row['sale_id']) ?></td>
                        <td><?= jsonChanges($row['old_values'], $row['new_values']) ?></td>
                        <td><?= userLabel($row) ?></td>
                        <td><?= e($row['action_date']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <h2>Sesiones</h2>
        <div class="table-responsive">
            <table>
                <tr>
                    <th>Evento</th>
                    <th>Usuario</th>
                    <th>Login SQL</th>
                    <th>IP</th>
                    <th>Fecha</th>
                </tr>

                <?php foreach ($sessionAudits as $row): ?>
                    <tr>
                        <td><span class="badge" style="<?= $badge ?>"><?= e($row['event']) ?></span></td>
                        <td><?= e($row['user_name'] ?? 'Desconocido') ?></td>
                        <td><?= e($row['login_name']) ?></td>
                        <td><?= e($row['client_ip']) ?></td>
                        <td><?= e($row['event_date']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>

</body>

</html>
