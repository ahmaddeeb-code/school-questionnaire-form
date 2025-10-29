<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Helpers\Csrf;
use App\Helpers\MailStub;
use App\Models\Form;
use App\Models\Audience;
use App\Models\Assignment;
use App\Models\Token;
use PDO;

class AssignController extends BaseController
{
    private Form $forms;
    private Audience $audiences;
    private Assignment $assignments;
    private Token $tokens;

    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
        $this->forms = new Form($pdo);
        $this->audiences = new Audience($pdo);
        $this->assignments = new Assignment($pdo);
        $this->tokens = new Token($pdo);
    }

    public function show(int $id): void
    {
        $form = $this->forms->find($id);
        $audiences = $this->audiences->all();
        $assignments = $this->assignments->getByForm($id);
        $this->view('forms/assign', [
            'form' => $form,
            'audiences' => $audiences,
            'assignments' => $assignments,
            'csrf' => Csrf::field(),
        ]);
    }

    public function assign(int $id): void
    {
        if (!Csrf::validate($_POST['_csrf_token'] ?? '')) {
            http_response_code(400);
            echo 'Invalid CSRF token';
            return;
        }
        $audienceIds = $_POST['audiences'] ?? [];
        $schedule = $_POST['scheduled_at'] ?? date('Y-m-d H:i:s');
        $this->assignments->assign($id, array_map('intval', $audienceIds), $schedule);

        if (!empty($_POST['send_emails'])) {
            $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            foreach ($audienceIds as $recipientId) {
                $token = $this->tokens->generate($id, (int)$recipientId, $_POST['expires_at'] ?? date('Y-m-d H:i:s', strtotime('+7 days')));
                $link = $scheme . '://' . $host . '/respond/' . $token;
                MailStub::send('recipient' . $recipientId . '@example.com', 'New form available', 'Access your form: ' . $link);
            }
        }
        header('Location: /forms/' . $id . '/assign');
        exit;
    }
}
