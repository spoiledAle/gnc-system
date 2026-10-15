<?php

require_once __DIR__ . '/session.php';

function gncEscape(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function gncCsrfField(): string
{
    $_SESSION['csrfToken'] ??= bin2hex(random_bytes(32));

    return '<input type="hidden" name="csrfToken" value="'
        . gncEscape($_SESSION['csrfToken']) . '">';
}

function gncValidatePost(): void
{
    $token = $_POST['csrfToken'] ?? null;

    if (
        $_SERVER['REQUEST_METHOD'] !== 'POST'
        || !is_string($token)
        || !isset($_SESSION['csrfToken'])
        || !hash_equals($_SESSION['csrfToken'], $token)
    ) {
        http_response_code(403);
        exit('Solicitud inválida. Recarga la página.');
    }
}

function gncCheckoutToken(): string
{
    if (!isset($_SESSION['checkoutToken'])) {
        $hex = bin2hex(random_bytes(16));

        $_SESSION['checkoutToken'] =
            substr($hex, 0, 8) . '-'
            . substr($hex, 8, 4) . '-4'
            . substr($hex, 13, 3) . '-a'
            . substr($hex, 17, 3) . '-'
            . substr($hex, 20, 12);
    }

    return $_SESSION['checkoutToken'];
}