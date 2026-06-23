<?php

namespace App\Controller;

abstract class BaseController
{
    protected function render(string $view, array $data = []): void
    {
        $viewFile = __DIR__ . '/../../../views/' . $view;

        if (!file_exists($viewFile)) {
            throw new \Exception("View not found: " . $viewFile);
        }

        extract($data, EXTR_SKIP);

        require $viewFile;
    }

    protected function ensureMethod(string $method): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== $method) {
            http_response_code(405);
            exit('Method Not Allowed');
        }
    }

    protected function ensureCsrf(): void
    {
        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            http_response_code(403);
            exit('CSRF invalid');
        }
    }
    
}