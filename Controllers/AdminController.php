<?php

namespace Controllers;

use Core\Auth;
use Models\Article;
use Models\Page;
use Models\User;
use Throwable;

class AdminController extends BaseAdminController
{
    public function index(): void
    {
        $articleCount = 0;
        $pageCount = 0;
        $userCount = 0;

        try {
            $articleCount = (new Article())->countAll();
            $pageCount = count((new Page())->getAll());
            $userCount = (new User())->count();
        } catch (Throwable $e) {
            error_log('Dashboard stats: ' . $e->getMessage());
        }

        $this->render('admin/dashboard', [
            'title' => 'Dashboard - Admin',
            'user_name' => Auth::name(),
            'articleCount' => $articleCount,
            'pageCount' => $pageCount,
            'userCount' => $userCount,
        ]);
    }
}
