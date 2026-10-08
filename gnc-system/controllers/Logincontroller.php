<?php

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../models/UserModel.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../views/login.php');
    exit;
}

$csrfToken = $_POST['csrfToken'] ?? '';

if (
    !is_string($csrfToken)
    || !isset($_SESSION['csrfToken'])
    || !hash_equals($_SESSION['csrfToken'], $csrfToken)
) {
    http_response_code(403);
    exit('Solicitud inválida. Vuelve a abrir el login.');
}

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if (!is_string($username) || !is_string($password)) {
    header('Location: ../views/login.php?error=2');
    exit;
}

$username = trim($username);

if (
    $username === ''
    || strlen($username) > 128
    || $password === ''
    || strlen($password) > 512
) {
    header('Location: ../views/login.php?error=2');
    exit;
}

$now = time();

$attempts = array_filter(
    $_SESSION['loginAttempts'] ?? [],
    static fn ($timestamp) => $timestamp > $now - 300
);

if (count($attempts) >= 10) {
    http_response_code(429);
    exit('Demasiados intentos. Inténtalo en cinco minutos.');
}

$attempts[] = $now;
$_SESSION['loginAttempts'] = array_values($attempts);

unset(
    $_SESSION['user'],
    $_SESSION['dbCredentials'],
    $_SESSION['timeout'],
    $_SESSION['cart'],
    $_SESSION['checkoutToken'],
    $_SESSION['edit_index']
);

$userModel = new UserModel();
$user = $userModel->login($username, $password);

if ($user === false) {
    header('Location: ../views/login.php?error=1');
    exit;
}

session_regenerate_id(true);

$_SESSION['user'] = $user;
$_SESSION['timeout'] = time();

$_SESSION['dbCredentials'] = [
    'username' => $username,
    'password' => $password,
];

$_SESSION['csrfToken'] = bin2hex(random_bytes(32));

unset(
    $_SESSION['loginAttempts'],
    $_SESSION['registeredUsername'],
    $_SESSION['success'],
    $_SESSION['error'],
    $_SESSION['usersSuccess'],
    $_SESSION['usersError']
);

header('Location: ../views/home.php');
exit;