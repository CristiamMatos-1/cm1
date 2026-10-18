<?php
$currentNav = 'audit';
$pageHeading = 'Log de Auditoria';
require dirname(__DIR__) . '/partials/header.php';
?>

<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <p class="text-sm text-gray-600">Registro de criar, editar, excluir e alteração de status. Total: <strong><?= (int) ($total ?? 0) ?></strong></p>
</div>

<div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quando</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Usuário</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ação</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tabela</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Registro</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">IP</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Detalhes</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (empty($logs)): ?>
                    <tr><td colspan="7" class="px-4 py-8 text-center text-gray-500">Nenhum evento registrado ainda.</td></tr>
                <?php else: ?>
                    <?php foreach ($logs as $log): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 whitespace-nowrap text-gray-600"><?= e(date('d/m/Y H:i:s', strtotime((string) $log['created_at']))) ?></td>
                            <td class="px-4 py-3">
                                <?php if (!empty($log['user_name'])): ?>
                                    <div class="font-medium text-gray-900"><?= e($log['user_name']) ?></div>
                                    <div class="text-xs text-gray-500">#<?= e((string) $log['user_id']) ?></div>
                                <?php else: ?>
                                    <span class="text-gray-400">#<?= e((string) ($log['user_id'] ?? '—')) ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded text-xs font-semibold bg-slate-100 text-slate-700"><?= e($log['action']) ?></span>
                            </td>
                            <td class="px-4 py-3 font-mono text-xs"><?= e($log['table_name']) ?></td>
                            <td class="px-4 py-3 text-gray-600"><?= $log['record_id'] !== null ? '#' . e((string) $log['record_id']) : '—' ?></td>
                            <td class="px-4 py-3 font-mono text-xs text-gray-500"><?= e($log['ip_address'] ?? '') ?></td>
                            <td class="px-4 py-3 text-gray-600 max-w-xs truncate" title="<?= e($log['details'] ?? '') ?>"><?= e($log['details'] ?? '') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require dirname(__DIR__) . '/partials/footer.php'; ?>
