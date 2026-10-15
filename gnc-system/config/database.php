<?php

require_once __DIR__ . '/auth_check.php';

try {
    $statement = $conn->prepare("
        EXEC sys.sp_set_session_context
            @key = N'user_id',
            @value = ?
    ");

    $statement->execute([$_SESSION['user']['id']]);
    $statement->closeCursor();
} catch (PDOException $exception) {
    error_log('GNC: no se pudo establecer el contexto de usuario.');

    http_response_code(503);
    exit('No se pudo preparar la conexión del usuario.');
}