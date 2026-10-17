<?php
$currentNav = 'users';
$pageHeading = 'Novo Usuário';
$backUrl = url('admin/users');
require dirname(__DIR__) . '/partials/header.php';
?>

<?php if (!empty($error)): ?>
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert"><p><?= e($error) ?></p></div>
<?php endif; ?>

<div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden max-w-2xl">
    <form action="<?= url('admin/users/store') ?>" method="POST" class="p-6 space-y-6">
        <?= csrf_field() ?>
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nome Completo <span class="text-red-500">*</span></label>
            <input type="text" name="name" id="name" required class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md border p-2.5">
        </div>
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">E-mail de Acesso <span class="text-red-500">*</span></label>
            <input type="email" name="email" id="email" required class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md border p-2.5">
        </div>
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Senha <span class="text-red-500">*</span></label>
            <input type="password" name="password" id="password" required class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md border p-2.5">
        </div>
        <div class="pt-4 flex justify-end gap-3 border-t border-gray-100">
            <a href="<?= url('admin/users') ?>" class="bg-white border border-gray-300 text-gray-700 px-6 py-2 rounded-md hover:bg-gray-50">Cancelar</a>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 shadow-sm">Salvar Usuário</button>
        </div>
    </form>
</div>

<?php require dirname(__DIR__) . '/partials/footer.php'; ?>
