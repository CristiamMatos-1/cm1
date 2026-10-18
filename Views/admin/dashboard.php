<?php
$currentNav = 'dashboard';
$pageHeading = 'Visão Geral';
require __DIR__ . '/partials/header.php';
?>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Total de Artigos</p>
                <h3 class="text-2xl font-bold text-gray-800"><?= (int) ($articleCount ?? 0) ?></h3>
            </div>
            <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-xl">
                <i class="fa-solid fa-newspaper"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Páginas</p>
                <h3 class="text-2xl font-bold text-gray-800"><?= (int) ($pageCount ?? 0) ?></h3>
            </div>
            <div class="w-12 h-12 bg-green-100 text-green-600 rounded-full flex items-center justify-center text-xl">
                <i class="fa-solid fa-file-alt"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Usuários</p>
                <h3 class="text-2xl font-bold text-gray-800"><?= (int) ($userCount ?? 0) ?></h3>
            </div>
            <div class="w-12 h-12 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center text-xl">
                <i class="fa-solid fa-users"></i>
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Bem-vindo ao Painel Administrativo</h3>
    <p class="text-gray-600">
        O ambiente restrito está configurado. Utilize o menu lateral ou os atalhos abaixo para gerenciar o conteúdo do site.
    </p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Atalhos de Páginas</h3>
        <ul class="space-y-3">
            <li>
                <a href="<?= url('admin/pages') ?>" class="flex items-center text-blue-700 hover:text-blue-900 transition">
                    <i class="fa-solid fa-list w-6 text-gray-400"></i> Listar Todas as Páginas
                </a>
            </li>
            <li>
                <a href="<?= url('admin/pages/create') ?>" class="flex items-center text-blue-700 hover:text-blue-900 transition">
                    <i class="fa-solid fa-plus w-6 text-gray-400"></i> Criar Nova Página
                </a>
            </li>
        </ul>
    </div>
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Configurações do Site</h3>
        <ul class="space-y-3">
            <li>
                <a href="<?= url('admin/settings') ?>" class="flex items-center text-blue-700 hover:text-blue-900 transition">
                    <i class="fa-brands fa-whatsapp w-6 text-green-500"></i> Alterar WhatsApp
                </a>
            </li>
            <li>
                <a href="<?= url('admin/settings') ?>" class="flex items-center text-blue-700 hover:text-blue-900 transition">
                    <i class="fa-brands fa-youtube w-6 text-red-500"></i> Configurar vídeo da página inicial
                </a>
            </li>
        </ul>
    </div>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>
