<?php
namespace App\Models;

use PDO;

class Upload extends BaseModel
{
    public function save(array $data): int
    {
        $stmt = $this->pdo->prepare('INSERT INTO uploads (response_id, question_id, filename_original, filename_stored, mime_type, size, created_at) VALUES (:response_id, :question_id, :filename_original, :filename_stored, :mime_type, :size, NOW())');
        $stmt->execute([
            'response_id' => $data['response_id'],
            'question_id' => $data['question_id'],
            'filename_original' => $data['filename_original'],
            'filename_stored' => $data['filename_stored'],
            'mime_type' => $data['mime_type'],
            'size' => $data['size'],
        ]);
        return (int)$this->pdo->lastInsertId();
    }
}
