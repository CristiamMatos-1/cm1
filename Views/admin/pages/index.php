<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    <?php require dirname(__DIR__, 2) . '/layout/brand-head.php'; ?>
</head>
<body class="bg-gray-100 flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <aside id="sidebar" class="bg-gray-800 text-white w-64 flex-shrink-0 absolute inset-y-0 left-0 transform -translate-x-full md:relative md:translate-x-0 transition duration-200 ease-in-out z-20 flex flex-col">
        <div class="h-16 flex items-center px-6 bg-gray-900 font-bold text-xl tracking-wider justify-between">
            <span><?= \Config\App::BRAND_NAME ?></span>
            <button id="close-sidebar" class="md:hidden text-gray-300 hover:text-white">
                <i class="fa-solid fa-times text-xl"></i>
            </button>
        </div>
        <nav class="flex-1 px-4 py-6 space-y-2">
            <a href="<?= htmlspecialchars(url('admin')) ?>" class="flex items-center gap-3 text-gray-300 hover:bg-gray-700 hover:text-white px-4 py-3 rounded-lg transition">
                <i class="fa-solid fa-chart-line"></i> Dashboard
            </a>
            <a href="<?= htmlspecialchars(url('admin/articles')) ?>" class="flex items-center gap-3 text-gray-300 hover:bg-gray-700 hover:text-white px-4 py-3 rounded-lg transition">
                <i class="fa-solid fa-newspaper"></i> Artigos / Matérias
            </a>
            <a href="<?= htmlspecialchars(url('admin/pages')) ?>" class="flex items-center gap-3 bg-gray-700 text-white px-4 py-3 rounded-lg">
                <i class="fa-solid fa-file-alt"></i> Páginas Institucionais
            </a>
            <a href="<?= htmlspecialchars(url('admin/settings')) ?>" class="flex items-center gap-3 text-gray-300 hover:bg-gray-700 hover:text-white px-4 py-3 rounded-lg transition">
                <i class="fa-solid fa-cog"></i> Configurações
            </a>
        </nav>
    </aside>

    <div class="flex-1 flex flex-col h-screen overflow-hidden">
        <header class="h-16 bg-white shadow-sm flex items-center justify-between px-6 z-10">
            <div class="flex items-center gap-4">
                <button id="open-sidebar" class="md:hidden text-gray-600 hover:text-gray-900 mr-2">
                    <i class="fa-solid fa-bars text-xl"></i>
                </button>
                <h2 class="text-xl font-semibold text-gray-800">Gerenciar Páginas Fixas</h2>
            </div>
            <a href="<?= htmlspecialchars(url('logout')) ?>" class="text-sm text-red-600 hover:underline">Sair</a>
        </header>

        <main class="flex-1 overflow-y-auto p-6 bg-gray-50">
            
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-medium text-gray-700">Lista de Páginas</h3>
                <a href="<?= htmlspecialchars(url('admin/pages/create')) ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md shadow text-sm font-medium transition">
                    <i class="fa-solid fa-plus mr-1"></i> Nova Página
                </a>
            </div>

            <?php if (!empty($msg)): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                    <p><?= htmlspecialchars($msg) ?></p>
                </div>
            <?php endif; ?>

            <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Título</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if(empty($pages)): ?>
                            <tr><td colspan="3" class="px-6 py-4 text-center text-gray-500">Nenhuma página encontrada.</td></tr>
                        <?php else: ?>
                            <?php foreach ($pages as $page): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><i class="fa-solid fa-file-alt text-gray-400 mr-2"></i> <?= htmlspecialchars($page['title']) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if($page['is_published']): ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Publicado</span>
                                    <?php else: ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Oculto</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="<?= htmlspecialchars(url('admin/pages/edit?id=' . $page['id'])) ?>" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                        <i class="fa-solid fa-edit mr-1"></i> Editar
                                    </a>
                                    <a href="<?= htmlspecialchars(url('admin/pages/delete?id=' . $page['id'])) ?>" onclick="return confirm('Tem certeza que deseja excluir esta página?');" class="text-red-600 hover:text-red-900">
                                        <i class="fa-solid fa-trash mr-1"></i> Excluir
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="mt-6 text-sm text-gray-500">
                <p><i class="fa-solid fa-info-circle mr-1"></i> Você pode criar novas páginas institucionais ou editar as existentes. Use o status "Oculto" para remover temporariamente do ar.</p>
            </div>
        </main>
    </div>

    <!-- Overlay para o menu mobile -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-10 hidden md:hidden"></div>

    <script>
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