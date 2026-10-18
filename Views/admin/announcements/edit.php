<?php
use Models\Announcement;

$currentNav = 'announcements';
$pageHeading = 'Editar aviso';
$backUrl = url('admin/announcements');
$item = $item ?? [];
require dirname(__DIR__) . '/partials/header.php';
?>

<?php if (!empty($error)): ?>
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert"><p><?= e($error) ?></p></div>
<?php endif; ?>

<form action="<?= url('admin/announcements/update') ?>" method="POST" class="bg-white shadow-sm rounded-lg border border-gray-200 p-6 max-w-2xl">
    <?= csrf_field() ?>
    <input type="hidden" name="id" value="<?= e((string) ($item['id'] ?? '')) ?>">
    <div class="mb-6">
        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Título (opcional)</label>
        <input type="text" name="title" id="title" maxlength="255" value="<?= e($item['title'] ?? '') ?>" class="shadow-sm block w-full sm:text-sm border-gray-300 rounded-md border p-2.5">
    </div>
    <div class="mb-6">
        <label for="body" class="block text-sm font-medium text-gray-700 mb-2">Texto do aviso</label>
        <textarea name="body" id="body" rows="6" required maxlength="5000" class="shadow-sm block w-full sm:text-sm border-gray-300 rounded-md border p-2.5"><?= e($item['body'] ?? '') ?></textarea>
    </div>
    <div class="mb-6">
        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
        <select name="status" id="status" class="shadow-sm block w-full sm:text-sm border-gray-300 rounded-md border p-2.5">
            <?php foreach (Announcement::STATUS_LABELS as $value => $label): ?>
                <option value="<?= e($value) ?>" <?= ($item['status'] ?? '') === $value ? 'selected' : '' ?>><?= e($label) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="flex justify-end">
        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 shadow-sm">Salvar alterações</button>
    </div>
</form>

<?php require dirname(__DIR__) . '/partials/footer.php'; ?>
