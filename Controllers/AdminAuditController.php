<?php

namespace Controllers;

use Models\AuditLog;

class AdminAuditController extends BaseAdminController
{
    public function index(): void
    {
        $model = new AuditLog();
        $this->render('admin/audit/index', [
            'title' => 'Auditoria - Admin',
            'logs' => $model->latest(150),
            'total' => $model->countAll(),
        ]);
    }
}
