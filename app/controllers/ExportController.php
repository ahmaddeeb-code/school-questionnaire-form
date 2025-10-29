<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Form;
use App\Models\Response;
use PDO;

class ExportController extends BaseController
{
    private Form $forms;
    private Response $responses;

    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
        $this->forms = new Form($pdo);
        $this->responses = new Response($pdo);
    }

    public function exportResponses(int $id): void
    {
        $form = $this->forms->find($id);
        $stmt = $this->pdo->prepare('SELECT ra.*, r.submitted_at FROM response_answers ra JOIN responses r ON ra.response_id = r.id WHERE r.form_id = :form_id');
        $stmt->execute(['form_id' => $id]);
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="form_' . $id . '_responses.csv"');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['Response ID', 'Question ID', 'Answer Text', 'Numeric', 'Date', 'File']);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            fputcsv($out, [$row['response_id'], $row['question_id'], $row['answer_text'], $row['answer_numeric'], $row['answer_date'], $row['answer_file']]);
        }
        fclose($out);
    }

    public function exportAnalytics(int $id): void
    {
        $analytics = $this->responses->analyticsSummary($id);
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="form_' . $id . '_analytics.csv"');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['Metric', 'Value']);
        fputcsv($out, ['Total responses', $analytics['summary']['total']]);
        fputcsv($out, ['Completed', $analytics['summary']['completed']]);
        foreach ($analytics['questions'] as $question) {
            fputcsv($out, ['Question ' . $question['id'], $question['answers']]);
        }
        fclose($out);
    }
}
