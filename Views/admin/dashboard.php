<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <aside id="sidebar" class="bg-gray-800 text-white w-64 flex-shrink-0 absolute inset-y-0 left-0 transform -translate-x-full md:relative md:translate-x-0 transition duration-200 ease-in-out z-20 flex flex-col">
        <div class="h-16 flex items-center px-6 bg-gray-900 font-bold text-xl tracking-wider justify-between">
            <span>CONINFOMS</span>
            <button id="close-sidebar" class="md:hidden text-gray-300 hover:text-white">
                <i class="fa-solid fa-times text-xl"></i>
            </button>
        </div>
        <nav class="flex-1 px-4 py-6 space-y-2">
            <a href="/cm1/admin" class="flex items-center gap-3 bg-gray-700 text-white px-4 py-3 rounded-lg">
                <i class="fa-solid fa-chart-line"></i> Dashboard
            </a>
            <a href="/cm1/admin/articles" class="flex items-center gap-3 text-gray-300 hover:bg-gray-700 hover:text-white px-4 py-3 rounded-lg transition">
                <i class="fa-solid fa-newspaper"></i> Artigos / Matérias
            </a>
            <a href="/cm1/admin/pages" class="flex items-center gap-3 text-gray-300 hover:bg-gray-700 hover:text-white px-4 py-3 rounded-lg transition">
                <i class="fa-solid fa-file-alt"></i> Páginas Institucionais
            </a>
            <a href="/cm1/admin/users" class="flex items-center gap-3 text-gray-300 hover:bg-gray-700 hover:text-white px-4 py-3 rounded-lg transition">
                <i class="fa-solid fa-users"></i> Usuários
            </a>
            <!-- LINK DAS CONFIGURAÇÕES ATUALIZADO AQUI -->
            <a href="/cm1/admin/settings" class="flex items-center gap-3 text-gray-300 hover:bg-gray-700 hover:text-white px-4 py-3 rounded-lg transition">
                <i class="fa-solid fa-cog"></i> Configurações
            </a>
        </nav>
        <div class="p-4 bg-gray-900 text-sm">
            Logado como:<br>
            <span class="font-bold text-blue-400"><?= htmlspecialchars($user_name) ?></span>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden">
        <!-- Top Header -->
        <header class="h-16 bg-white shadow-sm flex items-center justify-between px-6 z-10">
            <div class="flex items-center gap-4">
                <button id="open-sidebar" class="md:hidden text-gray-600 hover:text-gray-900">
                    <i class="fa-solid fa-bars text-xl"></i>
                </button>
                <h2 class="text-xl font-semibold text-gray-800">Visão Geral</h2>
            </div>
            <div class="flex items-center gap-4">
                <a href="/cm1/" target="_blank" class="text-sm text-gray-500 hover:text-blue-600 flex items-center gap-1">
                    <i class="fa-solid fa-external-link-alt"></i> Ver Site
                </a>
                <a href="/cm1/logout" class="bg-red-50 text-red-600 hover:bg-red-100 px-4 py-2 rounded-md text-sm font-medium transition flex items-center gap-2">
                    <i class="fa-solid fa-sign-out-alt"></i> Sair
                </a>
            </div>
        </header>

        <!-- Main Body -->
        <main class="flex-1 overflow-y-auto p-6 bg-gray-50">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- Card 1 -->
                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 mb-1">Total de Artigos</p>
                            <h3 class="text-2xl font-bold text-gray-800">0</h3>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-xl">
                            <i class="fa-solid fa-newspaper"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Card 2 -->
                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 mb-1">Visualizações (Mês)</p>
                            <h3 class="text-2xl font-bold text-gray-800">0</h3>
                        </div>
                        <div class="w-12 h-12 bg-green-100 text-green-600 rounded-full flex items-center justify-center text-xl">
                            <i class="fa-solid fa-eye"></i>
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
                <!-- Atalhos de Acesso Rápido -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Atalhos de Páginas</h3>
                    <ul class="space-y-3">
                        <li>
                            <a href="/cm1/admin/pages" class="flex items-center text-brand-blue hover:text-blue-800 transition">
                                <i class="fa-solid fa-list w-6 text-gray-400"></i> Listar Todas as Páginas
                            </a>
                        </li>
                        <li>
                            <a href="/cm1/admin/pages/create" class="flex items-center text-brand-blue hover:text-blue-800 transition">
                                <i class="fa-solid fa-plus w-6 text-gray-400"></i> Criar Nova Página
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Mais Opções -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Configurações do Site</h3>
                    <ul class="space-y-3">
                        <li>
                            <a href="/cm1/admin/settings" class="flex items-center text-brand-blue hover:text-blue-800 transition">
                                <i class="fa-brands fa-whatsapp w-6 text-green-500"></i> Alterar WhatsApp
                            </a>
                        </li>
                        <li>
                            <a href="/cm1/admin/settings" class="flex items-center text-brand-blue hover:text-blue-800 transition">
                                <i class="fa-brands fa-youtube w-6 text-red-500"></i> Configurar Vídeo do YouTube
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </main>
    </div>

    <!-- Overlay para o menu mobile -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-10 hidden md:hidden"></div>

    <script>
        // Lógica do Menu Mobile
        const sidebar = document.getElementById('sidebar');
        const openBtn = document.getElementById('open-sidebar');
        const closeBtn = document.getElementById('close-sidebar');
        const overlay = document.getElementById('sidebar-overlay');

        function toggleSidebar() {
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        if(openBtn) openBtn.addEventListener('click', toggleSidebar);
        if(closeBtn) closeBtn.addEventListener('click', toggleSidebar);
        if(overlay) overlay.addEventListener('click', toggleSidebar);
    </script>
</body>
</html>
