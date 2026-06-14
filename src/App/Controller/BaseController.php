<?php

namespace App\Controller;

abstract class BaseController
{
    protected function render(string $view, array $data = []): void
    {
        $viewFile = __DIR__ . '/../../../views/front/' . $view;

        if (!file_exists($viewFile)) {
            throw new \Exception("View not found: $view");
        }

        extract($data, EXTR_SKIP);

        require $viewFile;
    }
}