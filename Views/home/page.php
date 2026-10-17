<?php require dirname(__DIR__) . '/layout/header.php'; ?>

<div class="bg-gray-50 py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h1 class="text-3xl tracking-tight font-extrabold text-gray-900 sm:text-4xl">
                <?= htmlspecialchars($pageTitle) ?>
            </h1>
            <div class="w-24 h-1 bg-brand-blue mx-auto mt-4 rounded"></div>
        </div>

        <div class="bg-white shadow rounded-lg p-8 prose prose-blue max-w-none">
            <!-- O conteúdo vem do TinyMCE (armazenado no banco de dados), por isso usamos echo puro aqui. -->
            <!-- Atenção: Como é um painel administrativo protegido, confiamos no input do Admin. -->
            <?= $content ?>
        </div>
    </div>
</div>

<?php require dirname(__DIR__) . '/layout/footer.php'; ?>