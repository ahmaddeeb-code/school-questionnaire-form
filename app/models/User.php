<?php
namespace App\Models;

use PDO;

class User extends BaseModel
{
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    public function all(): array
    {
        $stmt = $this->pdo->query('SELECT id, name, email, role FROM users');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
