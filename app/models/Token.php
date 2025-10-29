<?php
namespace App\Models;

use PDO;

class Token extends BaseModel
{
    public function generate(int $formId, int $recipientId, string $expiresAt): string
    {
        $token = bin2hex(random_bytes(32));
        $stmt = $this->pdo->prepare('INSERT INTO access_tokens (form_id, recipient_id, token, expires_at, used_at, attempts) VALUES (:form_id, :recipient_id, :token, :expires_at, NULL, 0)');
        $stmt->execute([
            'form_id' => $formId,
            'recipient_id' => $recipientId,
            'token' => $token,
            'expires_at' => $expiresAt,
        ]);
        return $token;
    }

    public function validate(string $token, int $rateLimit): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM access_tokens WHERE token = :token');
        $stmt->execute(['token' => $token]);
        $record = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$record) {
            return null;
        }
        if ((int)$record['attempts'] >= $rateLimit) {
            return null;
        }
        if ($record['expires_at'] && strtotime($record['expires_at']) < time()) {
            return null;
        }
        $this->pdo->prepare('UPDATE access_tokens SET attempts = attempts + 1 WHERE id = :id')->execute(['id' => $record['id']]);
        return $record;
    }

    public function markUsed(int $id): void
    {
        $stmt = $this->pdo->prepare('UPDATE access_tokens SET used_at = NOW() WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
