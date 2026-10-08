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
    <style>
        .table-wrap { overflow: auto; }
        select { min-width: 130px; margin: 5px; }
        form { display: flex; gap: 8px; flex-wrap: wrap; align-items: center; }
        .message { padding: 15px; margin: 15px 0; border: 1px solid #666; }
    </style>
</head>
<body>
<div class="navbar">
    <a class="logo" href="../home.php">
        <img src="../../assets/images/GNC_Logo.svg.png" alt="GNC">
    </a>
    <a class="btn" href="../logout.php">Cerrar sesión</a>
</div>

<main class="container">
    <h1>Administración de usuarios</h1>
    <p class="subtitle">Cambia el rol y el estado de las cuentas.</p>
    <a class="btn" href="../home.php">Volver</a>

    <?php foreach (['usersSuccess', 'usersError'] as $key): ?>
        <?php if (isset($_SESSION[$key])): ?>
            <p class="message" role="status">
                <?= gncEscape($_SESSION[$key]) ?>
            </p>
            <?php unset($_SESSION[$key]); ?>
        <?php endif; ?>
    <?php endforeach; ?>

    <div class="table-wrap">
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