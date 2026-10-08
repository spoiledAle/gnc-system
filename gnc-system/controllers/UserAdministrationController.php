<?php

require_once __DIR__ . '/../config/auth_check.php';
requireAdmin();

require_once __DIR__ . '/../config/forms.php';
gncValidatePost();

require_once __DIR__ . '/../models/UserAdministrationModel.php';

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$role = $_POST['role'] ?? '';
$active = $_POST['active'] ?? '';

if (
    !$id
    || $id < 1
    || !is_string($role)
    || !in_array($role, ['ADMIN', 'EMPLEADO', 'CLIENTE'], true)
    || !is_string($active)
    || !in_array($active, ['0', '1'], true)
) {
    http_response_code(400);
    exit('Datos inválidos.');
}

try {
    $model = new UserAdministrationModel();
    $model->update($id, $role, $active === '1');

    $_SESSION['usersSuccess'] =
        'Cuenta actualizada. El cambio se aplica en su próxima solicitud.';
} catch (PDOException $exception) {
    $code = (int) ($exception->errorInfo[1] ?? 0);

    error_log('GNC: administrar usuarios. Código: ' . $code);

    $_SESSION['usersError'] = match ($code) {
        51205 => 'Debe quedar al menos un administrador activo.',
        51202 => 'Otro administrador está actualizando cuentas.',
        51204 => 'Esta cuenta no puede modificarse desde este módulo.',
        default => 'No se pudo actualizar la cuenta. Revisa la consola PHP.',
    };
}

header('Location: ../views/users/users.php');
exit;