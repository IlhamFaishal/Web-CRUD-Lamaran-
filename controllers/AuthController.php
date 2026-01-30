<?php

require_once __DIR__ . '/../models/UserModel.php';

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    public function login() {
        if (isLoggedIn()) {
            redirect('kategori'); // Dashboard redirect
        }

        view('auth/login');
    }

    public function authenticate() {
        if (!isPost()) {
            redirect('auth/login');
        }

        $username = sanitize($_POST['username']);
        $password = $_POST['password'];

        $errors = validateRequired($_POST, ['username', 'password']);

        if (!empty($errors)) {
            setFlashMessage('error', implode('<br>', $errors));
            redirect('auth/login');
        }

        $user = $this->userModel->findByUsername($username);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user'] = [
                'id' => $user['id'],
                'username' => $user['username'],
                'role' => $user['role']
            ];
            redirect('kategori'); // Redirect to main page
        } else {
            setFlashMessage('error', 'Username atau Password salah.');
            redirect('auth/login');
        }
    }

    public function logout() {
        session_destroy();
        redirect('auth/login');
    }
}
