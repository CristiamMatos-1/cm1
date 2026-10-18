<?php
use Models\Announcement;

$currentNav = 'announcements';
$pageHeading = 'Avisos importantes';
require dirname(__DIR__) . '/partials/header.php';
?>

<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-6">
    <p class="text-sm text-gray-600">Somente um aviso <strong>Publicado</strong> aparece no modal do site. Ao publicar outro, o anterior vira Oculto.</p>
    <a href="<?= url('admin/announcements/create') ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md shadow text-sm font-medium transition text-center">
        <i class="fa-solid fa-plus mr-1"></i> Novo aviso
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Título / texto</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Atualizado</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($announcements)): ?>
                    <tr><td colspan="4" class="px-6 py-4 text-center text-gray-500">Nenhum aviso cadastrado.</td></tr>
                <?php else: ?>
                    <?php foreach ($announcements as $item): ?>
                        <?php
                            $status = $item['status'] ?? Announcement::STATUS_DRAFT;
                            $label = Announcement::STATUS_LABELS[$status] ?? $status;
                            $badge = match ($status) {
                                Announcement::STATUS_PUBLISHED => 'bg-green-100 text-green-800',
                                Announcement::STATUS_HIDDEN => 'bg-gray-200 text-gray-700',
                                default => 'bg-yellow-100 text-yellow-800',
                            };
                            $preview = trim((string) ($item['title'] !== '' ? $item['title'] : $item['body']));
                        ?>
                        <tr>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900 line-clamp-2"><?= e(mb_substr($preview, 0, 120)) ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $badge ?>"><?= e($label) ?></span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500"><?= date('d/m/Y H:i', strtotime($item['updated_at'] ?? $item['created_at'])) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                <a href="<?= url('admin/announcements/edit', ['id' => $item['id']]) ?>" class="inline-flex items-center text-indigo-600 hover:text-indigo-900 bg-indigo-50 px-3 py-1 rounded transition">
                                    <i class="fa-solid fa-edit mr-1"></i> Editar
                                </a>
                                <form action="<?= url('admin/announcements/delete') ?>" method="POST" class="inline" onsubmit="return confirm('Excluir este aviso?');">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= e((string) $item['id']) ?>">
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
        <?php if (empty($announcements)): ?>
            <div class="p-6 text-center text-gray-500">Nenhum aviso cadastrado.</div>
        <?php else: ?>
            <?php foreach ($announcements as $item): ?>
                <?php
                    $status = $item['status'] ?? Announcement::STATUS_DRAFT;
                    $label = Announcement::STATUS_LABELS[$status] ?? $status;
                    $preview = trim((string) ($item['title'] !== '' ? $item['title'] : $item['body']));
                ?>
                <div class="p-4">
                    <div class="flex justify-between gap-3 mb-2">
                        <h4 class="font-bold text-gray-900 line-clamp-2"><?= e(mb_substr($preview, 0, 80)) ?></h4>
                        <span class="text-xs font-semibold text-gray-600 flex-shrink-0"><?= e($label) ?></span>
                    </div>
                    <a href="<?= url('admin/announcements/edit', ['id' => $item['id']]) ?>" class="block text-center bg-indigo-50 text-indigo-700 py-2 rounded-md text-sm font-medium">Editar</a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php require dirname(__DIR__) . '/partials/footer.php'; ?>
