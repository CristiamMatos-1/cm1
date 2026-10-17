<?php

namespace Controllers;

use Core\Auth;
use Core\Controller;
use Core\Csrf;

/**
 * Template Method: todo controller do painel autenticado herda a proteção.
 */
abstract class BaseAdminController extends Controller
{
    public function __construct()
    {
        Auth::requireLogin();
    }

    protected function assertCsrf(): void
    {
        Csrf::assertValid();
    }
}
