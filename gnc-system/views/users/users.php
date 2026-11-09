<?php

require_once __DIR__ . '/../../config/auth_check.php';
requireAdmin();

require_once __DIR__ . '/../../config/forms.php';
require_once __DIR__ . '/../../models/UserAdministrationModel.php';

try {
    $users = (new UserAdministrationModel())->getUsers();
} catch (PDOException $exception) {
    error_log('GNC: listar usuarios. Código: '
        . ($exception->errorInfo[1] ?? 0));

    http_response_code(503);
    exit('No se pudieron consultar los usuarios. Revisa el SQL adicional.');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Administración de usuarios</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
<?php
$currentPage = 'users';
$basePath = '../';
include __DIR__ . '/../partials/navbar.php';
?>

<main class="container">
    <h1 class="title">Administración de usuarios</h1>
    <p class="subtitle">Cambia el rol y el estado de las cuentas del sistema.</p>

    <div class="page-actions">
        <a class="btn btn-secondary" href="../home.php">← Volver</a>
    </div>

    <?php foreach (['usersSuccess', 'usersError'] as $key): ?>
        <?php if (isset($_SESSION[$key])): ?>
            <div
                class="alert <?= $key === 'usersSuccess' ? 'alert-success' : 'alert-error' ?>"
                role="<?= $key === 'usersSuccess' ? 'status' : 'alert' ?>"
            >
                <?= gncEscape($_SESSION[$key]) ?>
            </div>
            <?php unset($_SESSION[$key]); ?>
        <?php endif; ?>
    <?php endforeach; ?>

    <div class="table-responsive">
        <table>
            <thead>
            <tr>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Usuario</th>
                <th>Rol actual</th>
                <th>Estado</th>
                <th>Administrar</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= gncEscape($user['name']) ?></td>
                    <td><?= gncEscape($user['email']) ?></td>
                    <td><?= gncEscape($user['sqlLogin']) ?></td>
                    <td><?= gncEscape($user['role']) ?></td>
                    <td><?= $user['active'] ? 'Activo' : 'Inactivo' ?></td>
                    <td>
                        <form
                            method="post"
                            action="../../controllers/UserAdministrationController.php"
                        >
                            <?= gncCsrfField() ?>
                            <input
                                type="hidden"
                                name="id"
                                value="<?= (int) $user['id'] ?>"
                            >

                            <label>
                                Rol
                                <select name="role">
                                    <?php foreach (['CLIENTE', 'EMPLEADO', 'ADMIN'] as $role): ?>
                                        <option
                                            value="<?= $role ?>"
                                            <?= $role === $user['role'] ? 'selected' : '' ?>
                                        >
                                            <?= $role ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </label>

                            <label>
                                Estado
                                <select name="active">
                                    <option value="1" <?= $user['active'] ? 'selected' : '' ?>>
                                        Activo
                                    </option>
                                    <option value="0" <?= !$user['active'] ? 'selected' : '' ?>>
                                        Inactivo
                                    </option>
                                </select>
                            </label>

                            <button class="btn add-btn" type="submit">
                                Guardar
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>
</body>
</html>