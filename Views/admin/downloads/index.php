<?php
use Models\Download;

$currentNav = 'downloads';
$pageHeading = 'Suporte e Downloads';
require dirname(__DIR__) . '/partials/header.php';
?>

<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-6">
    <p class="text-sm text-gray-600">Arquivos publicados aparecem em <a href="<?= url('suporte') ?>" target="_blank" class="text-blue-600 hover:underline">/suporte</a>.</p>
    <a href="<?= url('admin/downloads/create') ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md shadow text-sm font-medium transition text-center">
        <i class="fa-solid fa-plus mr-1"></i> Novo arquivo
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome de exibição</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Arquivo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ordem</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($downloads)): ?>
                    <tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">Nenhum arquivo cadastrado.</td></tr>
                <?php else: ?>
                    <?php foreach ($downloads as $item): ?>
                        <?php
                            $status = $item['status'] ?? Download::STATUS_HIDDEN;
                            $label = Download::STATUS_LABELS[$status] ?? $status;
                            $badge = match ($status) {
                                Download::STATUS_PUBLISHED => 'bg-green-100 text-green-800',
                                Download::STATUS_HIDDEN => 'bg-yellow-100 text-yellow-800',
                                default => 'bg-gray-200 text-gray-700',
                            };
                        ?>
                        <tr>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900"><?= e($item['display_name']) ?></div>
                                <div class="text-xs text-gray-400"><?= e($item['uploader_name'] ?? '') ?></div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                <?= e($item['original_name']) ?><br>
                                <span class="text-xs"><?= e(format_bytes((int) $item['file_size'])) ?></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $badge ?>"><?= e($label) ?></span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500"><?= (int) $item['sort_order'] ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                <a href="<?= url('admin/downloads/edit', ['id' => $item['id']]) ?>" class="inline-flex items-center text-indigo-600 hover:text-indigo-900 bg-indigo-50 px-3 py-1 rounded transition">
                                    <i class="fa-solid fa-edit mr-1"></i> Editar
                                </a>
                                <?php if ($status !== Download::STATUS_REMOVED): ?>
                                    <form action="<?= url('admin/downloads/status') ?>" method="POST" class="inline" onsubmit="return confirm('Marcar este arquivo como removido? Ele sai da página pública.');">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= e((string) $item['id']) ?>">
                                        <input type="hidden" name="status" value="<?= e(Download::STATUS_REMOVED) ?>">
                                        <button type="submit" class="inline-flex items-center text-red-600 hover:text-red-900 bg-red-50 px-3 py-1 rounded transition">
                                            <i class="fa-solid fa-box-archive mr-1"></i> Remover
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="block lg:hidden divide-y divide-gray-200">
        <?php if (empty($downloads)): ?>
            <div class="p-6 text-center text-gray-500">Nenhum arquivo cadastrado.</div>
        <?php else: ?>
            <?php foreach ($downloads as $item): ?>
                <?php
                    $status = $item['status'] ?? Download::STATUS_HIDDEN;
                    $label = Download::STATUS_LABELS[$status] ?? $status;
                    $badge = match ($status) {
                        Download::STATUS_PUBLISHED => 'bg-green-100 text-green-800',
                        Download::STATUS_HIDDEN => 'bg-yellow-100 text-yellow-800',
                        default => 'bg-gray-200 text-gray-700',
                    };
                ?>
                <div class="p-4">
                    <div class="flex justify-between items-start gap-3 mb-2">
                        <h4 class="text-base font-bold text-gray-900"><?= e($item['display_name']) ?></h4>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full <?= $badge ?>"><?= e($label) ?></span>
                    </div>
                    <p class="text-xs text-gray-500 mb-3"><?= e($item['original_name']) ?> · <?= e(format_bytes((int) $item['file_size'])) ?></p>
                    <div class="flex gap-2">
                        <a href="<?= url('admin/downloads/edit', ['id' => $item['id']]) ?>" class="flex-1 text-center bg-indigo-50 text-indigo-700 py-2 rounded-md text-sm font-medium">Editar</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php require dirname(__DIR__) . '/partials/footer.php'; ?>
