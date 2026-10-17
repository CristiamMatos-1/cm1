<?php

namespace Controllers;

use Core\Controller;
use Models\User;

class AdminUserController extends Controller
{
    private User $userModel;

    public function __construct()
    {
        // Protege o controller: só acessa se estiver logado
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . url('login'));
            exit;
        }
        
        $this->userModel = new User();
    }

    /**
     * Lista todos os usuários
     */
    public function index(): void
    {
        $users = $this->userModel->getAll();
        
        $msg = $_SESSION['user_msg'] ?? null;
        $error = $_SESSION['user_error'] ?? null;
        unset($_SESSION['user_msg'], $_SESSION['user_error']);

        $this->render('admin/users/index', [
            'title' => 'Gerenciar Usuários - Admin',
            'users' => $users,
            'msg' => $msg,
            'error' => $error
        ]);
    }

    /**
     * Exibe o formulário de criação
     */
    public function create(): void
    {
        $this->render('admin/users/create', [
            'title' => 'Novo Usuário - Admin'
        ]);
    }

    /**
     * Salva um novo usuário no banco
     */
    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($name) || empty($email) || empty($password)) {
                $_SESSION['user_error'] = "Todos os campos são obrigatórios para um novo usuário.";
                header('Location: ' . url('admin/users/create'));
                exit;
            }

            $success = $this->userModel->create([
                'name' => $name,
                'email' => $email,
                'password' => $password
            ]);

            if ($success) {
                $_SESSION['user_msg'] = "Usuário criado com sucesso!";
                header('Location: ' . url('admin/users'));
            } else {
                $_SESSION['user_error'] = "Erro: Este e-mail já está em uso por outro usuário.";
                header('Location: ' . url('admin/users/create'));
            }
            exit;
        }
    }

    /**
     * Exibe o formulário de edição
     */
    public function edit(): void
    {
        $id = $_GET['id'] ?? 0;
        $user = $this->userModel->findById((int)$id);

        if (!$user) {
            $_SESSION['user_error'] = "Usuário não encontrado.";
            header('Location: ' . url('admin/users'));
            exit;
        }

        $this->render('admin/users/edit', [
            'title' => 'Editar Usuário - Admin',
            'user' => $user
        ]);
    }

    /**
     * Atualiza o usuário no banco
     */
    public function update(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['id'] ?? 0);
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($name) || empty($email)) {
                $_SESSION['user_error'] = "Nome e e-mail são obrigatórios.";
                header('Location: ' . url("admin/users/edit?id={$id}"));
                exit;
            }

            // Opcional: verificar se o email já existe (se foi alterado)
            $existing = $this->userModel->findByEmail($email);
            if ($existing && $existing['id'] != $id) {
                $_SESSION['user_error'] = "Este e-mail já está em uso por outro usuário.";
                header('Location: ' . url("admin/users/edit?id={$id}"));
                exit;
            }

            $success = $this->userModel->update($id, [
                'name' => $name,
                'email' => $email,
                'password' => $password
            ]);

            if ($success) {
                $_SESSION['user_msg'] = "Usuário atualizado com sucesso!";
            } else {
                $_SESSION['user_error'] = "Falha ao atualizar o usuário.";
            }

            header('Location: ' . url('admin/users'));
            exit;
        }
    }

    /**
     * Exclui um usuário
     */
    public function delete(): void
    {
        $id = (int)($_GET['id'] ?? 0);

        if ($id === (int)$_SESSION['user_id']) {
            $_SESSION['user_error'] = "Você não pode excluir a si mesmo enquanto estiver logado.";
        } else {
            $success = $this->userModel->delete($id);
            if ($success) {
                $_SESSION['user_msg'] = "Usuário excluído com sucesso!";
            } else {
                $_SESSION['user_error'] = "Não é possível excluir o único usuário do sistema.";
            }
        }

        header('Location: ' . url('admin/users'));
        exit;
    }
}
