<?php
$currentNav = 'users';
$pageHeading = 'Gerenciar Usuários';
require dirname(__DIR__) . '/partials/header.php';
?>

<div class="flex justify-between items-center mb-6">
    <h3 class="text-gray-700 text-lg font-medium">Lista de Usuários</h3>
    <a href="<?= url('admin/users/create') ?>" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 shadow flex items-center gap-2">
        <i class="fa-solid fa-plus"></i> <span class="hidden sm:inline">Novo Usuário</span>
    </a>
</div>

<?php if (!empty($msg)): ?>
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert"><p><?= e($msg) ?></p></div>
<?php endif; ?>
<?php if (!empty($error)): ?>
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert"><p><?= e($error) ?></p></div>
<?php endif; ?>

<div class="hidden md:block bg-white shadow-sm rounded-lg overflow-hidden border border-gray-200">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nome</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">E-mail</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Criado em</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Ações</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php foreach ($users as $user): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm text-gray-500">#<?= e((string) $user['id']) ?></td>
                    <td class="px-6 py-4 text-sm font-medium text-gray-900"><?= e($user['name']) ?></td>
                    <td class="px-6 py-4 text-sm text-gray-500"><?= e($user['email']) ?></td>
                    <td class="px-6 py-4 text-sm text-gray-500"><?= date('d/m/Y', strtotime($user['created_at'])) ?></td>
                    <td class="px-6 py-4 text-right text-sm font-medium">
                        <a href="<?= url('admin/users/edit', ['id' => $user['id']]) ?>" class="text-blue-600 hover:text-blue-900 mr-3">Editar</a>
                        <form action="<?= url('admin/users/delete') ?>" method="POST" class="inline" onsubmit="return confirm('Tem certeza que deseja excluir este usuário?');">
                            <?= csrf_field() ?>
                            <input type="hidden" name="id" value="<?= e((string) $user['id']) ?>">
                            <button type="submit" class="text-red-600 hover:text-red-900">Excluir</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($users)): ?>
                <tr><td colspan="5" class="px-6 py-4 text-sm text-gray-500 text-center">Nenhum usuário encontrado.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="md:hidden space-y-4">
    <?php foreach ($users as $user): ?>
        <div class="bg-white shadow rounded-lg p-4 border border-gray-200">
            <div class="flex justify-between items-start mb-2">
                <h4 class="text-lg font-bold text-gray-900"><?= e($user['name']) ?></h4>
                <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">#<?= e((string) $user['id']) ?></span>
            </div>
            <p class="text-sm text-gray-600 mb-1"><?= e($user['email']) ?></p>
            <div class="flex gap-2 border-t pt-3">
                <a href="<?= url('admin/users/edit', ['id' => $user['id']]) ?>" class="flex-1 bg-blue-50 text-blue-600 text-center py-2 rounded-md">Editar</a>
                <form action="<?= url('admin/users/delete') ?>" method="POST" class="flex-1" onsubmit="return confirm('Tem certeza que deseja excluir?');">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= e((string) $user['id']) ?>">
                    <button type="submit" class="w-full bg-red-50 text-red-600 py-2 rounded-md">Excluir</button>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php require dirname(__DIR__) . '/partials/footer.php'; ?>
