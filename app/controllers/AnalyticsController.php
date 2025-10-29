<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Form;
use App\Models\Response;
use PDO;

class AnalyticsController extends BaseController
{
    private Form $forms;
    private Response $responses;

    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
        $this->forms = new Form($pdo);
        $this->responses = new Response($pdo);
    }

    public function show(int $id): void
    {
        $form = $this->forms->find($id);
        $analytics = $this->responses->analyticsSummary($id);
        $this->view('forms/analytics', compact('form', 'analytics'));
    }
}
