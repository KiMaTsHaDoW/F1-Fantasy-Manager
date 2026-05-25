<?php
require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/app/models/UserModel.php';

class AuthController extends Controller {
    private UserModel $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    public function loginForm(): void {
        if ($this->isLogged()) $this->redirect('');
        $this->view('auth/login', ['title' => 'Iniciar sesión', 'flash' => $this->getFlash()]);
    }

    public function login(): void {
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $this->setFlash('error', 'Por favor, rellena todos los campos.');
            $this->redirect('auth/login');
            return;
        }

        $user = $this->userModel->findByEmail($email);
        if (!$user || !$this->userModel->verifyPassword($password, $user['password'])) {
            $this->setFlash('error', 'Email o contraseña incorrectos.');
            $this->redirect('auth/login');
            return;
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user']    = ['id' => $user['id'], 'username' => $user['username'], 'email' => $user['email'], 'role' => $user['role']];
        $this->setFlash('success', '¡Bienvenido, ' . htmlspecialchars($user['username']) . '!');
        $this->redirect('');
    }

    public function registerForm(): void {
        if ($this->isLogged()) $this->redirect('');
        $this->view('auth/register', ['title' => 'Registrarse', 'flash' => $this->getFlash()]);
    }

    public function register(): void {
        $username = trim($_POST['username'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['confirm_password'] ?? '';

        if (empty($username) || empty($email) || empty($password)) {
            $this->setFlash('error', 'Todos los campos son obligatorios.');
            $this->redirect('auth/register');
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->setFlash('error', 'Email no válido.');
            $this->redirect('auth/register');
            return;
        }

        if (strlen($password) < 6) {
            $this->setFlash('error', 'La contraseña debe tener al menos 6 caracteres.');
            $this->redirect('auth/register');
            return;
        }

        if ($password !== $confirm) {
            $this->setFlash('error', 'Las contraseñas no coinciden.');
            $this->redirect('auth/register');
            return;
        }

        if ($this->userModel->emailExists($email)) {
            $this->setFlash('error', 'Este email ya está registrado.');
            $this->redirect('auth/register');
            return;
        }

        if ($this->userModel->usernameExists($username)) {
            $this->setFlash('error', 'Este nombre de usuario ya existe.');
            $this->redirect('auth/register');
            return;
        }

        $userId = $this->userModel->create($username, $email, $password);
        if ($userId) {
            $this->setFlash('success', 'Cuenta creada correctamente. ¡Ya puedes iniciar sesión!');
            $this->redirect('auth/login');
        } else {
            $this->setFlash('error', 'Error al crear la cuenta. Inténtalo de nuevo.');
            $this->redirect('auth/register');
        }
    }

    public function logout(): void {
        session_destroy();
        header('Location: ' . BASE_URL . '/auth/login');
        exit;
    }
}
