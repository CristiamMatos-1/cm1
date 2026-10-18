<?php
$currentNav = 'pages';
$pageHeading = 'Gerenciar Páginas Fixas';
require dirname(__DIR__) . '/partials/header.php';
?>

<div class="flex justify-between items-center mb-6">
    <h3 class="text-lg font-medium text-gray-700">Lista de Páginas</h3>
    <a href="<?= url('admin/pages/create') ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md shadow text-sm font-medium">
        <i class="fa-solid fa-plus mr-1"></i> Nova Página
    </a>
</div>

<?php if (!empty($msg)): ?>
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert"><p><?= e($msg) ?></p></div>
<?php endif; ?>

<div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Título</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php if (empty($pages)): ?>
                <tr><td colspan="3" class="px-6 py-4 text-center text-gray-500">Nenhuma página encontrada.</td></tr>
            <?php else: ?>
                <?php foreach ($pages as $page): ?>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900"><i class="fa-solid fa-file-alt text-gray-400 mr-2"></i> <?= e($page['title']) ?></div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <?php if ($page['is_published']): ?>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Publicado</span>
                        <?php else: ?>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Oculto</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="<?= url('admin/pages/edit', ['id' => $page['id']]) ?>" class="text-indigo-600 hover:text-indigo-900 mr-3">Editar</a>
                        <form action="<?= url('admin/pages/delete') ?>" method="POST" class="inline" onsubmit="return confirm('Tem certeza que deseja excluir esta página?');">
                            <?= csrf_field() ?>
                            <input type="hidden" name="id" value="<?= e((string) $page['id']) ?>">
                            <button type="submit" class="text-red-600 hover:text-red-900">Excluir</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require dirname(__DIR__) . '/partials/footer.php'; ?>
