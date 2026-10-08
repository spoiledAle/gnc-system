<?php

require_once __DIR__ . '/../config/forms.php';

if (isset($_SESSION['user'])) {
    header('Location: ../views/home.php');
    exit;
}

gncValidatePost();

require_once __DIR__ . '/../models/RegistrationModel.php';

function registrationError(int $code, string $reason): never
{
    error_log('GNC: registro rechazado. ' . $reason);

    header('Location: ../views/register.php?error=' . $code);
    exit;
}

$requiredFields = [
    'username',
    'name',
    'email',
    'password',
    'confirmPassword',
];

foreach ($requiredFields as $field) {
    if (!isset($_POST[$field]) || !is_string($_POST[$field])) {
        registrationError(
            2,
            'Falta el campo del formulario: ' . $field
        );
    }
}

$username = strtolower(trim($_POST['username']));
$name = trim($_POST['name']);
$email = strtolower(trim($_POST['email']));

// No recortar la contraseña: los espacios pueden formar parte de ella.
$password = $_POST['password'];
$confirmPassword = $_POST['confirmPassword'];

if (!preg_match('/^[a-z][a-z0-9_]{3,29}$/D', $username)) {
    registrationError(
        2,
        'Usuario inválido: debe tener entre 4 y 30 caracteres, '
        . 'comenzar con una letra y usar letras sin acentos, '
        . 'números o guion bajo.'
    );
}

if ($name === '' || strlen($name) > 100) {
    registrationError(
        2,
        'Nombre vacío o superior al límite de 100 bytes.'
    );
}

if (
    strlen($email) > 100
    || !filter_var($email, FILTER_VALIDATE_EMAIL)
) {
    registrationError(
        2,
        'Correo inválido o superior al límite de 100 caracteres.'
    );
}

if ($password !== $confirmPassword) {
    registrationError(3, 'Las contraseñas no coinciden.');
}

if (strlen($password) < 12 || strlen($password) > 128) {
    registrationError(
        2,
        'La contraseña debe tener entre 12 y 128 caracteres.'
    );
}

if (!preg_match('/^[\x20-\x7E]{12,128}$/D', $password)) {
    registrationError(
        2,
        'La contraseña contiene caracteres no admitidos. '
        . 'Usa letras sin acentos, números y símbolos comunes.'
    );
}

if (!preg_match('/[A-Z]/', $password)) {
    registrationError(2, 'La contraseña necesita una mayúscula.');
}

if (!preg_match('/[a-z]/', $password)) {
    registrationError(2, 'La contraseña necesita una minúscula.');
}

if (!preg_match('/[0-9]/', $password)) {
    registrationError(2, 'La contraseña necesita un número.');
}

if (!preg_match('/[^A-Za-z0-9]/', $password)) {
    registrationError(2, 'La contraseña necesita un símbolo.');
}

$now = time();

$attempts = array_filter(
    $_SESSION['registerAttempts'] ?? [],
    static fn ($timestamp) => $timestamp > $now - 900
);

if (count($attempts) >= 5) {
    http_response_code(429);
    exit('Demasiados intentos. Espera quince minutos.');
}

$attempts[] = $now;
$_SESSION['registerAttempts'] = array_values($attempts);

try {
    $model = new RegistrationModel();

    $_SESSION['registeredUsername'] = $model->register(
        $username,
        $name,
        $email,
        $password
    );
} catch (PDOException $exception) {
    $code = (int) ($exception->errorInfo[1] ?? 0);

    registrationError(
        in_array($code, [51302, 2601, 2627, 15025, 15023], true)
            ? 4
            : 1,
        'SQL Server rechazó el registro. Código: ' . $code
    );
} catch (RuntimeException $exception) {
    registrationError(
        1,
        'Revisa la configuración de RegistrationModel '
        . 'y las credenciales de registro en database.local.php.'
    );
}

$_SESSION['csrfToken'] = bin2hex(random_bytes(32));

header('Location: ../views/login.php?success=1');
exit;