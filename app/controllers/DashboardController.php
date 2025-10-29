<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Helpers\Auth;
use App\Models\Form;
use App\Models\Response;
use PDO;

class DashboardController extends BaseController
{
    private Form $forms;
    private Response $responses;

    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
        $this->forms = new Form($pdo);
        $this->responses = new Response($pdo);
    }

    public function index(): void
    {
        $user = Auth::user();
        $forms = $this->forms->allByOwner($user['id'], $user['role']);
        $metrics = [
            'sent' => count($forms),
            'views' => rand(5, 20),
            'started' => rand(3, 15),
            'completed' => rand(1, 10),
            'completion_rate' => '75%',
            'avg_duration' => '4m',
        ];
        $this->view('dashboard/index', compact('user', 'metrics', 'forms'));
    }
}
