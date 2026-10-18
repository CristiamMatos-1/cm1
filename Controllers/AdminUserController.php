<?php

namespace Controllers;

use Core\Auth;
use Core\Input;
use Models\User;

class AdminUserController extends BaseAdminController
{
    private User $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
    }

    public function index(): void
    {
        $msg = $_SESSION['user_msg'] ?? null;
        $error = $_SESSION['user_error'] ?? null;
        unset($_SESSION['user_msg'], $_SESSION['user_error']);

        $this->render('admin/users/index', [
            'title' => 'Gerenciar Usuários - Admin',
            'users' => $this->userModel->getAll(),
            'msg' => $msg,
            'error' => $error,
        ]);
    }

    public function create(): void
    {
        $error = $_SESSION['user_error'] ?? null;
        unset($_SESSION['user_error']);

        $this->render('admin/users/create', [
            'title' => 'Novo Usuário - Admin',
            'error' => $error,
        ]);
    }

    public function store(): void
    {
        $this->assertCsrf();

        $name = Input::string($_POST['name'] ?? '');
        $email = Input::email($_POST['email'] ?? '');
        $password = (string) ($_POST['password'] ?? '');

        if ($name === '' || $email === '' || $password === '') {
            $_SESSION['user_error'] = 'Todos os campos são obrigatórios para um novo usuário.';
            $this->redirect('admin/users/create');
        }

        $success = $this->userModel->create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ]);

        if ($success) {
            $created = $this->userModel->findByEmail($email);
            $this->audit('create', 'users', $created ? (int) $created['id'] : null, $email);
            $_SESSION['user_msg'] = 'Usuário criado com sucesso!';
            $this->redirect('admin/users');
        }

        $_SESSION['user_error'] = 'Erro: Este e-mail já está em uso por outro usuário.';
        $this->redirect('admin/users/create');
    }

    public function edit(): void
    {
        $id = Input::int($_GET['id'] ?? 0);
        $user = $id ? $this->userModel->findById($id) : null;

        if (!$user) {
            $_SESSION['user_error'] = 'Usuário não encontrado.';
            $this->redirect('admin/users');
        }

        $error = $_SESSION['user_error'] ?? null;
        unset($_SESSION['user_error']);

        $this->render('admin/users/edit', [
            'title' => 'Editar Usuário - Admin',
            'user' => $user,
            'error' => $error,
        ]);
    }

    public function update(): void
    {
        $this->assertCsrf();

        $id = Input::int($_POST['id'] ?? 0) ?? 0;
        $name = Input::string($_POST['name'] ?? '');
        $email = Input::email($_POST['email'] ?? '');
        $password = (string) ($_POST['password'] ?? '');

        if ($id <= 0 || $name === '' || $email === '') {
            $_SESSION['user_error'] = 'Nome e e-mail são obrigatórios.';
            $this->redirect('admin/users/edit', ['id' => (string) $id]);
        }

        $existing = $this->userModel->findByEmail($email);
        if ($existing && (int) $existing['id'] !== $id) {
            $_SESSION['user_error'] = 'Este e-mail já está em uso por outro usuário.';
            $this->redirect('admin/users/edit', ['id' => (string) $id]);
        }

        $success = $this->userModel->update($id, [
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ]);

        if ($success) {
            $this->audit('update', 'users', $id, $email);
        }

        $_SESSION[$success ? 'user_msg' : 'user_error'] = $success
            ? 'Usuário atualizado com sucesso!'
            : 'Falha ao atualizar o usuário.';

        $this->redirect('admin/users');
    }

    public function delete(): void
    {
        $this->assertCsrf();
        $id = Input::int($_POST['id'] ?? $_GET['id'] ?? 0) ?? 0;

        if ($id === Auth::id()) {
            $_SESSION['user_error'] = 'Você não pode excluir a si mesmo enquanto estiver logado.';
        } else {
            $success = $this->userModel->delete($id);
            if ($success) {
                $this->audit('delete', 'users', $id);
            }
            $_SESSION[$success ? 'user_msg' : 'user_error'] = $success
                ? 'Usuário excluído com sucesso!'
                : 'Não é possível excluir o único usuário do sistema.';
        }

        $this->redirect('admin/users');
    }

    public function anonymize(): void
    {
        $this->assertCsrf();
        $id = Input::int($_POST['id'] ?? 0) ?? 0;

        if ($id <= 0) {
            $_SESSION['user_error'] = 'Usuário inválido.';
            $this->redirect('admin/users');
        }

        if ($id === Auth::id()) {
            $_SESSION['user_error'] = 'Você não pode anonimizar a si mesmo enquanto estiver logado.';
            $this->redirect('admin/users');
        }

        $user = $this->userModel->findById($id);
        if (!$user) {
            $_SESSION['user_error'] = 'Usuário não encontrado.';
            $this->redirect('admin/users');
        }

        if ($this->userModel->isAnonymized($user)) {
            $_SESSION['user_error'] = 'Este usuário já está anonimizado.';
            $this->redirect('admin/users');
        }

        $success = $this->userModel->anonymize($id);
        if ($success) {
            $this->audit('anonymize', 'users', $id, 'LGPD: dados pessoais removidos');
        }
        $_SESSION[$success ? 'user_msg' : 'user_error'] = $success
            ? 'Dados do usuário anonimizados conforme LGPD.'
            : 'Falha ao anonimizar o usuário.';

        $this->redirect('admin/users');
    }
}
