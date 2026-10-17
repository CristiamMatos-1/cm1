<?php
$currentNav = 'articles';
$pageHeading = 'Gerenciar Artigos';
require dirname(__DIR__) . '/partials/header.php';
?>

<div class="flex justify-between items-center mb-6">
    <h3 class="text-lg font-medium text-gray-700">Lista de Matérias</h3>
    <a href="<?= url('admin/articles/create') ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md shadow text-sm font-medium transition">
        <i class="fa-solid fa-plus mr-1"></i> Novo Artigo
    </a>
</div>

<?php if (!empty($msg)): ?>
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
        <p><?= e($msg) ?></p>
    </div>
<?php endif; ?>

<div class="bg-white shadow-sm rounded-lg border border-gray-200">
    <div class="hidden lg:block overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Título</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Autor</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($articles)): ?>
                    <tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">Nenhum artigo encontrado.</td></tr>
                <?php else: ?>
                    <?php foreach ($articles as $article): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 truncate max-w-xs" title="<?= e($article['title']) ?>">
                                <?= e($article['title']) ?>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= e($article['author_name'] ?? '') ?></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php if ($article['is_published']): ?>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Publicado</span>
                            <?php else: ?>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Rascunho</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= date('d/m/Y H:i', strtotime($article['created_at'])) ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="<?= url('admin/articles/edit', ['id' => $article['id']]) ?>" class="inline-flex items-center text-indigo-600 hover:text-indigo-900 bg-indigo-50 px-3 py-1 rounded mr-2 transition">
                                <i class="fa-solid fa-edit mr-1"></i> Editar
                            </a>
                            <form action="<?= url('admin/articles/delete') ?>" method="POST" class="inline" onsubmit="return confirm('Tem certeza que deseja excluir?');">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id" value="<?= e((string) $article['id']) ?>">
                                <button type="submit" class="inline-flex items-center text-red-600 hover:text-red-900 bg-red-50 px-3 py-1 rounded transition">
                                    <i class="fa-solid fa-trash mr-1"></i> Excluir
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="block lg:hidden divide-y divide-gray-200">
        <?php if (empty($articles)): ?>
            <div class="p-6 text-center text-gray-500">Nenhum artigo encontrado.</div>
        <?php else: ?>
            <?php foreach ($articles as $article): ?>
                <div class="p-4 bg-white hover:bg-gray-50 transition">
                    <div class="flex justify-between items-start mb-2">
                        <h4 class="text-base font-bold text-gray-900 leading-tight flex-1 pr-4"><?= e($article['title']) ?></h4>
                        <?php if ($article['is_published']): ?>
                            <span class="px-2 py-1 inline-flex text-xs font-semibold rounded-full bg-green-100 text-green-800">Publicado</span>
                        <?php else: ?>
                            <span class="px-2 py-1 inline-flex text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Rascunho</span>
                        <?php endif; ?>
                    </div>
                    <div class="text-xs text-gray-500 mb-4 flex items-center gap-3">
                        <span><i class="fa-solid fa-user mr-1"></i> <?= e($article['author_name'] ?? '') ?></span>
                        <span><i class="fa-solid fa-calendar mr-1"></i> <?= date('d/m/Y', strtotime($article['created_at'])) ?></span>
                    </div>
                    <div class="flex gap-2 pt-3 border-t border-gray-100">
                        <a href="<?= url('admin/articles/edit', ['id' => $article['id']]) ?>" class="flex-1 text-center bg-indigo-50 text-indigo-700 hover:bg-indigo-100 py-2 rounded-md text-sm font-medium">Editar</a>
                        <form action="<?= url('admin/articles/delete') ?>" method="POST" class="flex-1" onsubmit="return confirm('Tem certeza que deseja excluir?');">
                            <?= csrf_field() ?>
                            <input type="hidden" name="id" value="<?= e((string) $article['id']) ?>">
                            <button type="submit" class="w-full bg-red-50 text-red-700 hover:bg-red-100 py-2 rounded-md text-sm font-medium">Excluir</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php require dirname(__DIR__) . '/partials/footer.php'; ?>
