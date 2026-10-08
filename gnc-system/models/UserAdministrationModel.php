<?php

require_once __DIR__ . '/../config/database.php';

class UserAdministrationModel
{
    public function getUsers(): array
    {
        global $conn;

        $statement = $conn->query('EXEC dbo.pa_listarUsuarios');

        $users = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();

        return $users;
    }

    public function update(
        int $id,
        string $role,
        bool $active
    ): void {
        global $conn;

        $statement = $conn->prepare("
            EXEC dbo.pa_administrarUsuario
                @id = ?,
                @role = ?,
                @active = ?
        ");

        $statement->execute([$id, $role, $active ? 1 : 0]);
        $statement->closeCursor();
    }
}