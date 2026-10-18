<?php require dirname(__DIR__) . '/layout/header.php'; ?>

<section class="bg-brand-blue text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight mb-4"><?= e($supportPageTitle ?? 'Suporte Remoto') ?></h1>
        <?php if (trim((string) ($supportPageText ?? '')) !== ''): ?>
            <p class="text-lg md:text-xl text-blue-100 max-w-2xl mx-auto">
                <?= nl2br(e($supportPageText)) ?>
            </p>
        <?php endif; ?>
    </div>
</section>

<section class="py-16 bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <?php if (!empty($downloads)): ?>
            <ul class="space-y-4">
                <?php foreach ($downloads as $item): ?>
                    <li>
                        <a href="<?= url('download', ['id' => $item['id']]) ?>" class="flex items-center justify-between gap-4 bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow p-5 border border-gray-100 group">
                            <div class="flex items-center gap-4 min-w-0">
                                <div class="w-12 h-12 rounded-lg bg-blue-50 text-brand-blue flex items-center justify-center flex-shrink-0">
                                    <i class="fa-solid fa-file-arrow-down text-xl"></i>
                                </div>
                                <div class="min-w-0">
                                    <h2 class="text-lg font-bold text-gray-900 group-hover:text-brand-blue truncate"><?= e($item['display_name']) ?></h2>
                                    <p class="text-sm text-gray-500 truncate">
                                        <?= e($item['original_name']) ?>
                                        <?php if (!empty($item['file_size'])): ?>
                                            · <?= e(format_bytes((int) $item['file_size'])) ?>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                            <span class="bg-brand-blue text-white text-sm font-semibold px-4 py-2 rounded-md group-hover:bg-blue-700 flex-shrink-0">
                                Baixar
                            </span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <div class="text-center text-gray-500 py-12 bg-white rounded-lg shadow-sm">
                <i class="fa-solid fa-folder-open text-4xl mb-3"></i>
                <p>Nenhum arquivo de suporte disponível no momento.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require dirname(__DIR__) . '/layout/footer.php'; ?>
