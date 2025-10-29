<?php
namespace App\Models;

use PDO;

class Form extends BaseModel
{
    public function allByOwner(int $userId, ?string $role = null): array
    {
        if ($role === 'admin') {
            $stmt = $this->pdo->query('SELECT * FROM forms ORDER BY created_at DESC');
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        $stmt = $this->pdo->prepare('SELECT * FROM forms WHERE owner_id = :owner ORDER BY created_at DESC');
        $stmt->execute(['owner' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM forms WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $form = $stmt->fetch(PDO::FETCH_ASSOC);
        return $form ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare('INSERT INTO forms (title, description, owner_id, status, allow_anonymous, allow_edits, target_audience, settings, created_at, updated_at) VALUES (:title, :description, :owner_id, :status, :allow_anonymous, :allow_edits, :target_audience, :settings, NOW(), NOW())');
        $stmt->execute([
            'title' => $data['title'],
            'description' => $data['description'],
            'owner_id' => $data['owner_id'],
            'status' => $data['status'],
            'allow_anonymous' => $data['allow_anonymous'] ?? 0,
            'allow_edits' => $data['allow_edits'] ?? 0,
            'target_audience' => $data['target_audience'],
            'settings' => json_encode($data['settings'] ?? []),
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = $this->pdo->prepare('UPDATE forms SET title = :title, description = :description, status = :status, allow_anonymous = :allow_anonymous, allow_edits = :allow_edits, target_audience = :target_audience, settings = :settings, updated_at = NOW() WHERE id = :id');
        $stmt->execute([
            'title' => $data['title'],
            'description' => $data['description'],
            'status' => $data['status'],
            'allow_anonymous' => $data['allow_anonymous'] ?? 0,
            'allow_edits' => $data['allow_edits'] ?? 0,
            'target_audience' => $data['target_audience'],
            'settings' => json_encode($data['settings'] ?? []),
            'id' => $id,
        ]);
    }
}
