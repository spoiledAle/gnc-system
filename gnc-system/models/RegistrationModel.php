<?php

require_once __DIR__ . '/../config/sql_connection.php';

class RegistrationModel
{
    public function register(
        string $username,
        string $name,
        string $email,
        string $password
    ): string {
        $settings = (static function (): array {
            $registrationUser = '';
            $registrationPassword = '';

            $config = __DIR__ . '/../config/database.local.php';

            if (!is_file($config)) {
                error_log(
                    'GNC: registro: no existe config/database.local.php.'
                );

                throw new RuntimeException(
                    'No existe la configuración local.'
                );
            }

            include $config;

            if (
                !is_string($registrationUser)
                || $registrationUser === ''
                || !is_string($registrationPassword)
                || $registrationPassword === ''
            ) {
                error_log(
                    'GNC: registro: falta registrationUser '
                    . 'o registrationPassword en database.local.php.'
                );

                throw new RuntimeException(
                    'Faltan las credenciales de registro.'
                );
            }

            return [
                'user' => $registrationUser,
                'password' => $registrationPassword,
            ];
        })();

        $connection = connectSqlServer(
            $settings['user'],
            $settings['password']
        );

        $statement = $connection->prepare("
            EXEC dbo.pa_registrarCliente
                @username = ?,
                @name = ?,
                @email = ?,
                @password = ?
        ");

        $statement->execute([
            $username,
            $name,
            $email,
            $password,
        ]);

        // Buscar el resultado con sqlLogin, incluso si SQL Server
        // devuelve otros conjuntos de resultados antes.
        $createdLogin = null;

        do {
            if ($statement->columnCount() > 0) {
                while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                    if (
                        isset($row['sqlLogin'])
                        && is_string($row['sqlLogin'])
                        && $row['sqlLogin'] !== ''
                    ) {
                        $createdLogin = $row['sqlLogin'];
                    }
                }
            }
        } while ($statement->nextRowset());

        $statement->closeCursor();

        if ($createdLogin === null) {
            error_log(
                'GNC: registro: pa_registrarCliente terminó '
                . 'sin devolver sqlLogin. Comprueba la cuenta '
                . 'en SSMS antes de repetir el registro.'
            );

            throw new RuntimeException(
                'No se recibió el usuario creado.'
            );
        }

        return $createdLogin;
    }
}