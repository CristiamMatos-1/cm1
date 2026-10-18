<?php
use Models\Download;

$currentNav = 'downloads';
$pageHeading = 'Editar arquivo de suporte';
$backUrl = url('admin/downloads');
$item = $item ?? [];
require dirname(__DIR__) . '/partials/header.php';
?>

<?php if (!empty($error)): ?>
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert"><p><?= e($error) ?></p></div>
<?php endif; ?>

<form action="<?= url('admin/downloads/update') ?>" method="POST" enctype="multipart/form-data" class="bg-white shadow-sm rounded-lg border border-gray-200 p-6 max-w-2xl">
    <?= csrf_field() ?>
    <input type="hidden" name="id" value="<?= e((string) ($item['id'] ?? '')) ?>">
    <div class="mb-6">
        <label for="display_name" class="block text-sm font-medium text-gray-700 mb-2">Nome de exibição do link</label>
        <input type="text" name="display_name" id="display_name" required maxlength="255" value="<?= e($item['display_name'] ?? '') ?>" class="shadow-sm block w-full sm:text-sm border-gray-300 rounded-md border p-2.5">
    </div>
    <div class="mb-6">
        <p class="block text-sm font-medium text-gray-700 mb-1">Arquivo atual</p>
        <p class="text-sm text-gray-600"><?= e($item['original_name'] ?? '') ?> · <?= e(format_bytes((int) ($item['file_size'] ?? 0))) ?></p>
        <label for="support_file" class="block text-sm font-medium text-gray-700 mt-3 mb-2">Substituir arquivo (opcional)</label>
        <input type="file" name="support_file" id="support_file" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
    </div>
    <div class="mb-6">
        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Visibilidade</label>
        <select name="status" id="status" class="shadow-sm block w-full sm:text-sm border-gray-300 rounded-md border p-2.5">
            <?php foreach (Download::STATUS_LABELS as $value => $label): ?>
                <option value="<?= e($value) ?>" <?= ($item['status'] ?? '') === $value ? 'selected' : '' ?>><?= e($label) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="mb-6">
        <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-2">Ordem de exibição</label>
        <input type="number" name="sort_order" id="sort_order" value="<?= e((string) ($item['sort_order'] ?? 0)) ?>" class="shadow-sm block w-full sm:text-sm border-gray-300 rounded-md border p-2.5">
    </div>
    <div class="flex justify-end">
        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 shadow-sm">Salvar alterações</button>
    </div>
</form>

<?php require dirname(__DIR__) . '/partials/footer.php'; ?>
