<?php
use Models\Announcement;

$currentNav = 'announcements';
$pageHeading = 'Novo aviso';
$backUrl = url('admin/announcements');
require dirname(__DIR__) . '/partials/header.php';
?>

<?php if (!empty($error)): ?>
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert"><p><?= e($error) ?></p></div>
<?php endif; ?>

<form action="<?= url('admin/announcements/store') ?>" method="POST" class="bg-white shadow-sm rounded-lg border border-gray-200 p-6 max-w-2xl">
    <?= csrf_field() ?>
    <div class="mb-6">
        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Título (opcional)</label>
        <input type="text" name="title" id="title" maxlength="255" placeholder="Ex.: Manutenção programada" class="shadow-sm block w-full sm:text-sm border-gray-300 rounded-md border p-2.5">
    </div>
    <div class="mb-6">
        <label for="body" class="block text-sm font-medium text-gray-700 mb-2">Texto do aviso</label>
        <textarea name="body" id="body" rows="6" required maxlength="5000" class="shadow-sm block w-full sm:text-sm border-gray-300 rounded-md border p-2.5" placeholder="Mensagem exibida no modal..."></textarea>
    </div>
    <div class="mb-6">
        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
        <select name="status" id="status" class="shadow-sm block w-full sm:text-sm border-gray-300 rounded-md border p-2.5">
            <?php foreach (Announcement::STATUS_LABELS as $value => $label): ?>
                <option value="<?= e($value) ?>" <?= $value === Announcement::STATUS_DRAFT ? 'selected' : '' ?>><?= e($label) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="flex justify-end">
        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 shadow-sm">Salvar aviso</button>
    </div>
</form>

<?php require dirname(__DIR__) . '/partials/footer.php'; ?>
