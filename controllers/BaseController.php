<?php

namespace Controller;

abstract class BaseController {
    protected function render(string $view, array $data = []): void {
        extract($data, EXTR_SKIP);
        $db = conectarDB();
        $layoutData = getHeaderData($db);
        extract($layoutData, EXTR_SKIP);
        require __DIR__ . '/../views/layouts/header.php';
        require __DIR__ . '/../views/' . $view . '.php';
        require __DIR__ . '/../views/layouts/footer.php';
    }

    protected function redirect(string $url): void {
        header("Location: {$url}");
        exit;
    }

    protected function requestMethod(): string {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }
}
