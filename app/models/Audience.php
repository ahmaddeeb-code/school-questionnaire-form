<?php
namespace App\Models;

use PDO;

class Audience extends BaseModel
{
    public function all(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM audiences ORDER BY type, name');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
