<?php

namespace Controllers;

use Core\Controller;

class AdminController extends Controller
{
    /**
     * Construtor verifica se o usuário está logado em todas as rotas deste controller
     */
    public function __construct()
    {
        $this->checkAuth();
    }

    /**
     * Verifica a sessão
     */
    private function checkAuth(): void
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /cm1/login');
            exit;
        }
    }

    /**
     * Dashboard Principal do Admin
     */
    public function index(): void
    {
        $this->render('admin/dashboard', [
            'title' => 'Dashboard - Admin',
            'user_name' => $_SESSION['user_name']
        ]);
    }
}
