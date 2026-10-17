<?php
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página não encontrada</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-50 flex items-center justify-center p-6">
    <div class="max-w-lg text-center bg-white shadow rounded-xl p-10">
        <p class="text-sm font-semibold text-blue-600 uppercase tracking-wide">Erro 404</p>
        <h1 class="mt-2 text-3xl font-bold text-slate-900">Página não encontrada</h1>
        <p class="mt-3 text-slate-600">A URL solicitada não corresponde a nenhuma rota do site.</p>
        <?php if (!empty($requested)): ?>
            <p class="mt-2 text-xs text-slate-400">URI: <?= $requested ?></p>
        <?php endif; ?>
        <a href="<?= htmlspecialchars(\Config\App::url(), ENT_QUOTES, 'UTF-8') ?>" class="inline-block mt-6 bg-blue-600 text-white px-5 py-2.5 rounded-md font-medium hover:bg-blue-700">
            Ir para a página inicial
        </a>
    </div>
</body>
</html>
