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
            <a href="<?= htmlspecialchars(url('admin/users')) ?>" class="flex items-center gap-3 bg-gray-700 text-white px-4 py-3 rounded-lg">
                <i class="fa-solid fa-users"></i> Usuários
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
                <h2 class="text-xl font-semibold text-gray-800">Gerenciar Usuários</h2>
            </div>
            <a href="<?= htmlspecialchars(url('logout')) ?>" class="text-sm text-red-600 hover:underline">Sair</a>
        </header>

        <main class="flex-1 overflow-y-auto p-6 bg-gray-50">
            
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-gray-700 text-lg font-medium">Lista de Usuários</h3>
                <a href="<?= htmlspecialchars(url('admin/users/create')) ?>" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 shadow transition flex items-center gap-2">
                    <i class="fa-solid fa-plus"></i> <span class="hidden sm:inline">Novo Usuário</span>
                </a>
            </div>

            <?php if (!empty($msg)): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                    <p><?= htmlspecialchars($msg) ?></p>
                </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                    <p><?= htmlspecialchars($error) ?></p>
                </div>
            <?php endif; ?>

            <!-- Desktop Table -->
            <div class="hidden md:block bg-white shadow-sm rounded-lg overflow-hidden border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">E-mail</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Criado em</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($users as $user): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">#<?= $user['id'] ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= htmlspecialchars($user['name']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($user['email']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= date('d/m/Y', strtotime($user['created_at'])) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="<?= htmlspecialchars(url('admin/users/edit?id=' . $user['id'])) ?>" class="text-blue-600 hover:text-blue-900 mr-3" title="Editar">
                                        <i class="fa-solid fa-edit"></i> Editar
                                    </a>
                                    <a href="<?= htmlspecialchars(url('admin/users/delete?id=' . $user['id'])) ?>" onclick="return confirm('Tem certeza que deseja excluir este usuário?');" class="text-red-600 hover:text-red-900" title="Excluir">
                                        <i class="fa-solid fa-trash"></i> Excluir
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        
                        <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Nenhum usuário encontrado.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards -->
            <div class="md:hidden space-y-4">
                <?php foreach ($users as $user): ?>
                    <div class="bg-white shadow rounded-lg p-4 border border-gray-200">
                        <div class="flex justify-between items-start mb-2">
                            <h4 class="text-lg font-bold text-gray-900"><?= htmlspecialchars($user['name']) ?></h4>
                            <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">#<?= $user['id'] ?></span>
                        </div>
                        <p class="text-sm text-gray-600 mb-1"><i class="fa-solid fa-envelope mr-1"></i> <?= htmlspecialchars($user['email']) ?></p>
                        <p class="text-sm text-gray-500 mb-4"><i class="fa-solid fa-calendar mr-1"></i> Criado: <?= date('d/m/Y', strtotime($user['created_at'])) ?></p>
                        
                        <div class="flex gap-2 border-t pt-3">
                            <a href="<?= htmlspecialchars(url('admin/users/edit?id=' . $user['id'])) ?>" class="flex-1 bg-blue-50 text-blue-600 text-center py-2 rounded-md hover:bg-blue-100 font-medium transition">
                                <i class="fa-solid fa-edit"></i> Editar
                            </a>
                            <a href="<?= htmlspecialchars(url('admin/users/delete?id=' . $user['id'])) ?>" onclick="return confirm('Tem certeza que deseja excluir?');" class="flex-1 bg-red-50 text-red-600 text-center py-2 rounded-md hover:bg-red-100 font-medium transition">
                                <i class="fa-solid fa-trash"></i> Excluir
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>

                <?php if (empty($users)): ?>
                    <div class="bg-white shadow rounded-lg p-6 border border-gray-200 text-center text-gray-500">
                        Nenhum usuário encontrado.
                    </div>
                <?php endif; ?>
            </div>

        </main>
    </div>

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