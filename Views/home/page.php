<?php require dirname(__DIR__) . '/layout/header.php'; ?>

<div class="bg-brand-light py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h1 class="text-3xl tracking-tight font-bold text-brand-dark sm:text-4xl">
                <?= e($pageTitle) ?>
            </h1>
            <div class="w-24 h-1 bg-brand-accent mx-auto mt-4 rounded"></div>
        </div>

        <div class="bg-white shadow rounded-lg p-8 prose prose-blue max-w-none">
            <!-- O conteúdo vem do TinyMCE (armazenado no banco de dados), por isso não escapamos o HTML; apenas a grafia da marca é corrigida e formulários são omitidos na exibição (contato apenas via WhatsApp). -->
            <!-- Atenção: Como é um painel administrativo protegido, confiamos no input do Admin. -->
            <?= brand_html($content) ?>
        </div>
    </div>
</div>

<?php require dirname(__DIR__) . '/layout/footer.php'; ?>