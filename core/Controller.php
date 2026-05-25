<?php
class Controller {

    protected function view(string $view, array $data = []): void {
        extract($data);
        $viewPath = BASE_PATH . '/app/views/' . str_replace('.', '/', $view) . '.php';

        if (!file_exists($viewPath)) {
            die("Vista no encontrada: $viewPath");
        }

        ob_start();
        require $viewPath;
        $content = ob_get_clean();

        require BASE_PATH . '/app/views/layouts/main.php';
    }

    protected function redirect(string $url): void {
        header('Location: ' . BASE_URL . '/' . ltrim($url, '/'));
        exit;
    }

    protected function isLogged(): bool {
        return isset($_SESSION['user_id']);
    }

    protected function requireLogin(): void {
        if (!$this->isLogged()) {
            $this->redirect('auth/login');
        }
        $this->refreshUserSession();
    }

    private function refreshUserSession(): void {
        require_once BASE_PATH . '/app/models/UserModel.php';
        $user = (new UserModel())->findById($_SESSION['user_id']);
        if (!$user) {
            session_destroy();
            $this->redirect('auth/login');
        }
        $_SESSION['user']['role'] = $user['role'];
    }

    protected function isAdmin(): bool {
        return ($this->currentUser()['role'] ?? '') === 'admin';
    }

    protected function requireAdmin(): void {
        if (!$this->isAdmin()) {
            http_response_code(403);
            die('Acceso restringido.');
        }
    }

    protected function currentUser(): ?array {
        return $_SESSION['user'] ?? null;
    }

    protected function setFlash(string $type, string $message): void {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }

    protected function getFlash(): ?array {
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        return null;
    }
}
