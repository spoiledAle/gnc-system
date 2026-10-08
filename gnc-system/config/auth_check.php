<?php

require_once __DIR__ . '/session.php';
require_once __DIR__ . '/sql_connection.php';

header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

function redirectToGncLogin(string $query = ''): never
{
    clearGncSession();

    // Construir la ruta desde este archivo, incluso en subcarpetas.
    $appRoot = realpath(__DIR__ . '/..');
    $documentRoot = realpath($_SERVER['DOCUMENT_ROOT'] ?? '');

    $basePath = '';

    if ($appRoot !== false && $documentRoot !== false) {
        $appRoot = str_replace('\\', '/', $appRoot);
        $documentRoot = rtrim(
            str_replace('\\', '/', $documentRoot),
            '/'
        );

        if (str_starts_with($appRoot, $documentRoot . '/')) {
            $basePath = substr($appRoot, strlen($documentRoot));
        }
    }

    header(
        'Location: ' . $basePath . '/views/login.php' . $query
    );

    exit;
}

function requireLogin(): void
{
    if (
        !isset(
            $_SESSION['user'],
            $_SESSION['dbCredentials'],
            $_SESSION['timeout']
        )
        || !is_array($_SESSION['user'])
        || !is_array($_SESSION['dbCredentials'])
    ) {
        redirectToGncLogin();
    }

    if (time() - (int) $_SESSION['timeout'] >= 300) {
        redirectToGncLogin('?expired=1');
    }
}

function requireRole(array $allowedRoles): void
{
    requireLogin();

    $role = $_SESSION['user']['role'] ?? '';

    if (!in_array($role, $allowedRoles, true)) {
        http_response_code(403);
        exit('No tienes permisos para acceder a esta función.');
    }
}

function isAdmin(): bool
{
    return ($_SESSION['user']['role'] ?? '') === 'ADMIN';
}

function requireAdmin(): void
{
    requireRole(['ADMIN']);
}

requireLogin();

$credentials = $_SESSION['dbCredentials'];

if (
    !isset($credentials['username'], $credentials['password'])
    || !is_string($credentials['username'])
    || !is_string($credentials['password'])
) {
    redirectToGncLogin();
}

try {
    // $conn queda disponible para los modelos existentes.
    $conn = connectSqlServer(
        $credentials['username'],
        $credentials['password']
    );

    $_SESSION['user'] = getGncProfile($conn);
    $_SESSION['timeout'] = time();
} catch (PDOException $exception) {
    $nativeCode = (int) ($exception->errorInfo[1] ?? 0);

    error_log(
        'GNC: fallo al validar la conexión. Código: '
        . $nativeCode
    );

    // Credenciales rechazadas, cuenta bloqueada o sin acceso a la BD.
    if (in_array($nativeCode, [18456, 18470, 4060], true)) {
        redirectToGncLogin('?error=1');
    }

    http_response_code(503);
    exit('No se pudo verificar el acceso. Inténtalo nuevamente.');
} catch (RuntimeException $exception) {
    redirectToGncLogin('?error=1');
}