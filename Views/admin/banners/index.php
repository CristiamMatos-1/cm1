<?php
$currentNav = 'banners';
$pageHeading = 'Banners da página inicial';
require dirname(__DIR__) . '/partials/header.php';
?>

<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-6">
    <p class="text-sm text-gray-600">Somente banners ativos entram no carrossel da home. Sem nenhum ativo, a seção some.</p>
    <a href="<?= url('admin/banners/create') ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md shadow text-sm font-medium transition text-center">
        <i class="fa-solid fa-plus mr-1"></i> Novo banner
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Imagem</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Título</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Link</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ordem</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($banners)): ?>
                    <tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">Nenhum banner cadastrado.</td></tr>
                <?php else: ?>
                    <?php foreach ($banners as $banner): ?>
                        <tr>
                            <td class="px-6 py-4">
                                <?php if (!empty($banner['image_path'])): ?>
                                    <img src="<?= asset($banner['image_path']) ?>" alt="" class="h-12 w-24 object-cover rounded border">
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900"><?= e($banner['title'] !== '' ? $banner['title'] : '—') ?></td>
                            <td class="px-6 py-4 text-sm text-gray-500 truncate max-w-xs"><?= e($banner['link_url'] !== '' ? $banner['link_url'] : '—') ?></td>
                            <td class="px-6 py-4 text-sm text-gray-500"><?= (int) $banner['sort_order'] ?></td>
                            <td class="px-6 py-4">
                                <?php if (!empty($banner['is_active'])): ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Ativo</span>
                                <?php else: ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Oculto</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                <a href="<?= url('admin/banners/edit', ['id' => $banner['id']]) ?>" class="inline-flex items-center text-indigo-600 hover:text-indigo-900 bg-indigo-50 px-3 py-1 rounded transition">
                                    <i class="fa-solid fa-edit mr-1"></i> Editar
                                </a>
                                <form action="<?= url('admin/banners/delete') ?>" method="POST" class="inline" onsubmit="return confirm('Excluir este banner?');">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= e((string) $banner['id']) ?>">
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
        <?php if (empty($banners)): ?>
            <div class="p-6 text-center text-gray-500">Nenhum banner cadastrado.</div>
        <?php else: ?>
            <?php foreach ($banners as $banner): ?>
                <div class="p-4">
                    <div class="flex gap-3 mb-3">
                        <?php if (!empty($banner['image_path'])): ?>
                            <img src="<?= asset($banner['image_path']) ?>" alt="" class="h-16 w-24 object-cover rounded border">
                        <?php endif; ?>
                        <div>
                            <h4 class="font-bold text-gray-900"><?= e($banner['title'] !== '' ? $banner['title'] : 'Banner') ?></h4>
                            <p class="text-xs text-gray-500">Ordem <?= (int) $banner['sort_order'] ?> · <?= !empty($banner['is_active']) ? 'Ativo' : 'Oculto' ?></p>
                        </div>
                    </div>
                    <a href="<?= url('admin/banners/edit', ['id' => $banner['id']]) ?>" class="block text-center bg-indigo-50 text-indigo-700 py-2 rounded-md text-sm font-medium">Editar</a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php require dirname(__DIR__) . '/partials/footer.php'; ?>
