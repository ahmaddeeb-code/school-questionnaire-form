<?php
namespace App\Models;

use PDO;

class Assignment extends BaseModel
{
    public function assign(int $formId, array $audienceIds, string $scheduledAt): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO form_assignments (form_id, audience_id, scheduled_at) VALUES (:form_id, :audience_id, :scheduled_at)');
        foreach ($audienceIds as $audienceId) {
            $stmt->execute([
                'form_id' => $formId,
                'audience_id' => $audienceId,
                'scheduled_at' => $scheduledAt,
            ]);
        }
    }

    public function getByForm(int $formId): array
    {
        $stmt = $this->pdo->prepare('SELECT fa.*, a.name, a.type FROM form_assignments fa JOIN audiences a ON fa.audience_id = a.id WHERE fa.form_id = :form_id');
        $stmt->execute(['form_id' => $formId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
