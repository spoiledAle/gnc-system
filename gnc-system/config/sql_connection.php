<?php

function gncDatabaseSettings(): array
{
    $host = 'localhost';
    $database = 'GNCPROYECTO';

    $localConfig = __DIR__ . '/database.local.php';

    if (is_file($localConfig)) {
        include $localConfig;
    }

    return [
        'host' => $host,
        'database' => $database,
    ];
}

function connectSqlServer(
    string $username,
    string $password
): PDO {
    $settings = gncDatabaseSettings();

    $dsn = 'sqlsrv:Server=' . $settings['host']
        . ';Database=' . $settings['database']
        . ';Encrypt=true'
        . ';TrustServerCertificate=true';

    return new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
}

// Registra LOGIN, LOGOUT o EXPIRADA en tbl_auditSessions. SQL Server
// toma el usuario de la conexión; si falla, no se bloquea al usuario.
function registerGncSessionEvent(PDO $connection, string $event): void
{
    try {
        $statement = $connection->prepare(
            'EXEC dbo.pa_registrarSesion @event = ?, @clientIp = ?'
        );

        $statement->execute([$event, $_SERVER['REMOTE_ADDR'] ?? null]);
        $statement->closeCursor();
    } catch (PDOException $exception) {
        error_log(
            'GNC: no se pudo auditar la sesión (' . $event . ').'
        );
    }
}

// Abre una conexión con las credenciales guardadas en la sesión solo
// para auditar el cierre o la expiración, antes de borrar la sesión.
function registerGncSessionEventFromSession(string $event): void
{
    $credentials = $_SESSION['dbCredentials'] ?? null;

    if (
        !is_array($credentials)
        || !is_string($credentials['username'] ?? null)
        || !is_string($credentials['password'] ?? null)
    ) {
        return;
    }

    try {
        $connection = connectSqlServer(
            $credentials['username'],
            $credentials['password']
        );

        registerGncSessionEvent($connection, $event);
    } catch (PDOException $exception) {
        error_log(
            'GNC: no se pudo auditar la sesión (' . $event . ').'
        );
    }
}

function getGncProfile(PDO $connection): array
{
    $statement = $connection->query('EXEC dbo.pa_validarCuenta');
    $status = $statement->fetch(PDO::FETCH_ASSOC);
    $statement->closeCursor();

    if (!$status || (int) $status['active'] !== 1) {
        throw new RuntimeException('La cuenta está desactivada.');
    }

    $statement = $connection->query("
        SELECT
            IS_ROLEMEMBER('rolAdmin') AS adminMember,
            IS_ROLEMEMBER('rolEmpleado') AS employeeMember,
            IS_ROLEMEMBER('rolCliente') AS customerMember
    ");

    $membership = $statement->fetch(PDO::FETCH_ASSOC);
    $statement->closeCursor();

    if (!$membership) {
        throw new RuntimeException('No se pudieron consultar los roles.');
    }

    $roles = [];

    if ((int) $membership['adminMember'] === 1) {
        $roles[] = 'ADMIN';
    }

    if ((int) $membership['employeeMember'] === 1) {
        $roles[] = 'EMPLEADO';
    }

    if ((int) $membership['customerMember'] === 1) {
        $roles[] = 'CLIENTE';
    }

    if (count($roles) !== 1) {
        throw new RuntimeException(
            'La cuenta debe tener exactamente un rol.'
        );
    }

    $statement = $connection->query("
        SELECT id, name, email, sqlLogin
        FROM dbo.vw_appIdentity
    ");

    $profile = $statement->fetch(PDO::FETCH_ASSOC);
    $statement->closeCursor();

    if (!$profile) {
        throw new RuntimeException('No existe un perfil vinculado.');
    }

    $profile['id'] = (int) $profile['id'];
    $profile['role'] = $roles[0];

    return $profile;
}