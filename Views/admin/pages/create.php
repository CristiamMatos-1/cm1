<?php
$currentNav = 'pages';
$pageHeading = 'Nova Página Institucional';
$backUrl = url('admin/pages');
$useTinymce = true;
require dirname(__DIR__) . '/partials/header.php';
?>

<?php if (!empty($error)): ?>
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert"><p><?= e($error) ?></p></div>
<?php endif; ?>

<form action="<?= url('admin/pages/store') ?>" method="POST" class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
    <?= csrf_field() ?>
    <div class="mb-6">
        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Título da Página</label>
        <input type="text" name="title" id="title" required class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md border p-2.5">
    </div>
    <div class="mb-6">
        <label for="content-editor" class="block text-sm font-medium text-gray-700 mb-2">Conteúdo HTML</label>
        <textarea name="content" id="content-editor"></textarea>
    </div>
    <div class="mb-6 flex items-center">
        <input id="is_published" name="is_published" type="checkbox" checked class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
        <label for="is_published" class="ml-2 block text-sm text-gray-900">Página visível publicamente (Publicada)</label>
    </div>
    <?php $seo = []; require dirname(__DIR__) . '/partials/seo_fields.php'; ?>
    <fieldset class="mb-6 border border-gray-200 rounded-md p-4">
        <legend class="text-sm font-medium text-gray-700 px-1">Exibir link em</legend>
        <p class="text-xs text-gray-500 mb-3">Escolha onde o link desta página aparece no site público.</p>
        <div class="space-y-2">
            <label class="flex items-center gap-2 text-sm text-gray-900">
                <input type="checkbox" name="show_in_header" value="1" checked class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                Menu Principal
            </label>
            <label class="flex items-center gap-2 text-sm text-gray-900">
                <input type="checkbox" name="show_in_footer" value="1" checked class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                Rodapé
            </label>
            <label class="flex items-center gap-2 text-sm text-gray-900">
                <input type="checkbox" name="show_in_news" value="1" class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                Últimas Novidades (home)
            </label>
        </div>
        <div class="mt-4">
            <label for="menu_order" class="block text-sm font-medium text-gray-700 mb-1">Ordem no menu</label>
            <input type="number" name="menu_order" id="menu_order" value="0" min="0" class="shadow-sm border border-gray-300 rounded-md p-2 w-28 text-sm">
            <p class="text-xs text-gray-500 mt-1">Menor número aparece primeiro.</p>
        </div>
    </fieldset>
    <div class="flex justify-end">
        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 shadow-sm">Criar Página</button>
    </div>
</form>

<?php require dirname(__DIR__) . '/partials/footer.php'; ?>
