<?php
require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/app/models/UserModel.php';

class ProfileController extends Controller {
    private UserModel $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    public function show(): void {
        $this->requireLogin();
        $user = $this->userModel->findById($_SESSION['user_id']);
        $this->view('profile/show', [
            'title' => 'Mi Perfil - F1 Fantasy',
            'flash' => $this->getFlash(),
            'user'  => $user,
        ]);
    }

    public function updateUsername(): void {
        $this->requireLogin();

        $username = trim($_POST['username'] ?? '');

        if (empty($username)) {
            $this->setFlash('error', 'El nombre de usuario no puede estar vacío.');
            $this->redirect('profile');
            return;
        }

        if (strlen($username) < 3 || strlen($username) > 30) {
            $this->setFlash('error', 'El nombre debe tener entre 3 y 30 caracteres.');
            $this->redirect('profile');
            return;
        }

        if ($username === $_SESSION['user']['username']) {
            $this->setFlash('error', 'El nombre de usuario es el mismo que el actual.');
            $this->redirect('profile');
            return;
        }

        if ($this->userModel->usernameExists($username)) {
            $this->setFlash('error', 'Ese nombre de usuario ya está en uso.');
            $this->redirect('profile');
            return;
        }

        $this->userModel->updateUsername($_SESSION['user_id'], $username);
        $_SESSION['user']['username'] = $username;
        $this->setFlash('success', 'Nombre de usuario actualizado correctamente.');
        $this->redirect('profile');
    }

    public function updatePassword(): void {
        $this->requireLogin();

        $current = $_POST['current_password'] ?? '';
        $new     = $_POST['new_password']     ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if (empty($current) || empty($new) || empty($confirm)) {
            $this->setFlash('error', 'Todos los campos son obligatorios.');
            $this->redirect('profile');
            return;
        }

        if (strlen($new) < 6) {
            $this->setFlash('error', 'La nueva contraseña debe tener al menos 6 caracteres.');
            $this->redirect('profile');
            return;
        }

        if ($new !== $confirm) {
            $this->setFlash('error', 'Las contraseñas nuevas no coinciden.');
            $this->redirect('profile');
            return;
        }

        $user = $this->userModel->findById($_SESSION['user_id']);
        if (!$this->userModel->verifyPassword($current, $user['password'])) {
            $this->setFlash('error', 'La contraseña actual es incorrecta.');
            $this->redirect('profile');
            return;
        }

        $this->userModel->updatePassword($_SESSION['user_id'], $new);
        $this->setFlash('success', 'Contraseña actualizada correctamente.');
        $this->redirect('profile');
    }
}
