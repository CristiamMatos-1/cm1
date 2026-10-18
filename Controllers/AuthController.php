<?php

namespace Controllers;

use Core\Auth;
use Core\Controller;
use Core\Csrf;
use Core\Input;
use Models\User;
use Throwable;

class AuthController extends Controller
{
    public function login(): void
    {
        if (Auth::check()) {
            $this->redirect('admin');
        }

        $error = $_SESSION['login_error'] ?? null;
        unset($_SESSION['login_error']);

        $this->render('auth/login', [
            'title' => 'Login Administrativo',
            'error' => $error,
        ]);
    }

    public function authenticate(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('login');
        }

        if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
            $_SESSION['login_error'] = 'Sessão expirada ou requisição inválida.';
            $this->redirect('login');
        }

        $email = Input::email($_POST['email'] ?? '');
        $password = (string) ($_POST['password'] ?? '');

        if ($email === '' || $password === '') {
            $_SESSION['login_error'] = 'Preencha todos os campos.';
            $this->redirect('login');
        }

        try {
            $user = (new User())->authenticate($email, $password);
        } catch (Throwable $e) {
            error_log('Login falhou: ' . $e->getMessage());
            $_SESSION['login_error'] = 'Não foi possível autenticar. Tente novamente.';
            $this->redirect('login');
        }

        if ($user) {
            Auth::login($user);
            $this->redirect('admin');
        }

        $_SESSION['login_error'] = 'E-mail ou senha incorretos.';
        $this->redirect('login');
    }

    public function logout(): void
    {
        Auth::logout();
        $this->redirect('login');
    }
}
