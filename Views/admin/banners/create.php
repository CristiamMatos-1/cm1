<?php
$currentNav = 'banners';
$pageHeading = 'Novo banner';
$backUrl = url('admin/banners');
require dirname(__DIR__) . '/partials/header.php';
?>

<?php if (!empty($error)): ?>
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert"><p><?= e($error) ?></p></div>
<?php endif; ?>

<form action="<?= url('admin/banners/store') ?>" method="POST" enctype="multipart/form-data" class="bg-white shadow-sm rounded-lg border border-gray-200 p-6 max-w-2xl">
    <?= csrf_field() ?>
    <div class="mb-6">
        <label for="banner_image" class="block text-sm font-medium text-gray-700 mb-2">Imagem</label>
        <input type="file" name="banner_image" id="banner_image" required accept="image/jpeg,image/png,image/webp" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
        <p class="text-xs text-gray-500 mt-1">JPG, PNG ou WEBP. Recomendado 1600×560. Máximo 10 MB.</p>
    </div>
    <div class="mb-6">
        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Texto / título</label>
        <input type="text" name="title" id="title" maxlength="255" placeholder="Deixe vazio para ocultar o texto sobre a imagem" class="shadow-sm block w-full sm:text-sm border-gray-300 rounded-md border p-2.5">
    </div>
    <div class="mb-6">
        <label for="link_url" class="block text-sm font-medium text-gray-700 mb-2">Link de redirecionamento</label>
        <input type="text" name="link_url" id="link_url" maxlength="500" placeholder="https://... ou /suporte" class="shadow-sm block w-full sm:text-sm border-gray-300 rounded-md border p-2.5">
        <p class="text-xs text-gray-500 mt-1">Opcional. Sem link, o banner não é clicável.</p>
    </div>
    <div class="mb-6">
        <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-2">Ordem de exibição</label>
        <input type="number" name="sort_order" id="sort_order" value="0" class="shadow-sm block w-full sm:text-sm border-gray-300 rounded-md border p-2.5">
        <p class="text-xs text-gray-500 mt-1">Menor número aparece primeiro.</p>
    </div>
    <div class="mb-6 flex items-center">
        <input id="is_active" name="is_active" type="checkbox" checked class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
        <label for="is_active" class="ml-2 block text-sm text-gray-900">Ativo na página inicial</label>
    </div>
    <div class="flex justify-end">
        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 shadow-sm">Salvar banner</button>
    </div>
</form>

<?php require dirname(__DIR__) . '/partials/footer.php'; ?>
