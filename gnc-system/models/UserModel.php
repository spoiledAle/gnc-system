<?php

require_once __DIR__ . '/../config/sql_connection.php';

class UserModel
{
    public function login(
        string $username,
        string $password
    ): array|false {
        $username = trim($username);

        // La contraseña se conserva exactamente como fue escrita.
        if ($username === '' || $password === '') {
            return false;
        }

        try {
            $connection = connectSqlServer($username, $password);

            return getGncProfile($connection);
        } catch (PDOException $exception) {
            // Diagnóstico temporal: aparece en la terminal de PHP.
            // No se imprime en la página ni se registra la contraseña.
            error_log(
                'GNC: error SQL: ' . $exception->getMessage()
            );

            return false;
        } catch (RuntimeException $exception) {
            error_log(
                'GNC: error de perfil o rol: '
                . $exception->getMessage()
            );

            return false;
        }
    }
}