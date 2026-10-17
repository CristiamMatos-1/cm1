<?php

namespace Controllers;

use Core\Controller;
use Models\User;

class AuthController extends Controller
{
    /**
     * Exibe a tela de login
     */
    public function login(): void
    {
        // Se já estiver logado, redireciona para o admin
        if (isset($_SESSION['user_id'])) {
            header('Location: ' . url('admin'));
            exit;
        }

        $error = $_SESSION['login_error'] ?? null;
        unset($_SESSION['login_error']);

        $this->render('auth/login', [
            'title' => 'Login Administrativo',
            'error' => $error
        ]);
    }

    /**
     * Processa a tentativa de login (POST)
     */
    public function authenticate(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . url('login'));
            exit;
        }

        // CSRF Token Check (Básico)
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['login_error'] = 'Sessão expirada ou requisição inválida.';
            header('Location: ' . url('login'));
            exit;
        }

        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $_SESSION['login_error'] = 'Preencha todos os campos.';
            header('Location: ' . url('login'));
            exit;
        }

        $userModel = new User();
        $user = $userModel->authenticate($email, $password);

        if ($user) {
            // Regenera o ID da sessão para prevenir Session Fixation
            session_regenerate_id(true);
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            
            header('Location: ' . url('admin'));
            exit;
        } else {
            $_SESSION['login_error'] = 'E-mail ou senha incorretos.';
            header('Location: ' . url('login'));
            exit;
        }
    }

    /**
     * Encerra a sessão
     */
    public function logout(): void
    {
        session_unset();
        session_destroy();
        header('Location: ' . url('login'));
        exit;
    }
}
