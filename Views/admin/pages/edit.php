<?php
$currentNav = 'pages';
$pageHeading = 'Editar Página Institucional';
$backUrl = url('admin/pages');
$useTinymce = true;
require dirname(__DIR__) . '/partials/header.php';
?>

<?php if (!empty($error)): ?>
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert"><p><?= e($error) ?></p></div>
<?php endif; ?>

<form action="<?= url('admin/pages/update') ?>" method="POST" class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
    <?= csrf_field() ?>
    <input type="hidden" name="id" value="<?= e((string) $page['id']) ?>">
    <div class="mb-6">
        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Título da Página</label>
        <input type="text" name="title" id="title" required value="<?= e($page['title']) ?>" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md border p-2.5">
    </div>
    <div class="mb-6">
        <label for="content-editor" class="block text-sm font-medium text-gray-700 mb-2">Conteúdo HTML</label>
        <textarea name="content" id="content-editor"><?= e($page['content']) ?></textarea>
    </div>
    <div class="mb-6 flex items-center">
        <input id="is_published" name="is_published" type="checkbox" <?= $page['is_published'] ? 'checked' : '' ?> class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
        <label for="is_published" class="ml-2 block text-sm text-gray-900">Página visível publicamente (Publicada)</label>
    </div>
    <div class="flex justify-end">
        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 shadow-sm">Atualizar Página</button>
    </div>
</form>

<?php require dirname(__DIR__) . '/partials/footer.php'; ?>
