<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Helpers\Auth;
use App\Helpers\Csrf;
use App\Helpers\I18n;
use App\Models\Form;
use PDO;

class FormController extends BaseController
{
    private Form $forms;

    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
        $this->forms = new Form($pdo);
    }

    public function index(): void
    {
        $user = Auth::user();
        $forms = $this->forms->allByOwner($user['id'], $user['role']);
        $this->view('forms/index', compact('forms', 'user'));
    }

    public function create(): void
    {
        $this->view('forms/create', [
            'csrf' => Csrf::field(),
            'title' => I18n::translate('forms.create'),
        ]);
    }

    public function store(): void
    {
        if (!Csrf::validate($_POST['_csrf_token'] ?? '')) {
            http_response_code(400);
            echo 'Invalid CSRF token';
            return;
        }
        $user = Auth::user();
        $id = $this->forms->create([
            'title' => $_POST['title'] ?? 'Untitled form',
            'description' => $_POST['description'] ?? '',
            'owner_id' => $user['id'],
            'status' => $_POST['status'] ?? 'draft',
            'allow_anonymous' => isset($_POST['allow_anonymous']) ? 1 : 0,
            'allow_edits' => isset($_POST['allow_edits']) ? 1 : 0,
            'target_audience' => $_POST['target_audience'] ?? 'families',
            'settings' => [
                'grade_filters' => $_POST['grade_filters'] ?? [],
                'department_filters' => $_POST['department_filters'] ?? [],
            ],
        ]);
        header('Location: /forms/' . $id . '/builder');
        exit;
    }

    public function edit(int $id): void
    {
        $form = $this->forms->find($id);
        $this->view('forms/edit', [
            'form' => $form,
            'csrf' => Csrf::field(),
        ]);
    }

    public function update(int $id): void
    {
        if (!Csrf::validate($_POST['_csrf_token'] ?? '')) {
            http_response_code(400);
            echo 'Invalid CSRF token';
            return;
        }
        $this->forms->update($id, [
            'title' => $_POST['title'] ?? 'Untitled form',
            'description' => $_POST['description'] ?? '',
            'status' => $_POST['status'] ?? 'draft',
            'allow_anonymous' => isset($_POST['allow_anonymous']) ? 1 : 0,
            'allow_edits' => isset($_POST['allow_edits']) ? 1 : 0,
            'target_audience' => $_POST['target_audience'] ?? 'families',
            'settings' => [
                'grade_filters' => $_POST['grade_filters'] ?? [],
                'department_filters' => $_POST['department_filters'] ?? [],
            ],
        ]);
        header('Location: /forms');
        exit;
    }
}
