<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- TinyMCE CDN Open Source -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
      tinymce.init({
        selector: '#content-editor',
        plugins: 'advlist autolink lists link image charmap preview anchor pagebreak',
        toolbar_mode: 'floating',
        height: 500
      });
    </script>
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
            <a href="/cm1/admin" class="flex items-center gap-3 text-gray-300 hover:bg-gray-700 hover:text-white px-4 py-3 rounded-lg transition">
                <i class="fa-solid fa-chart-line"></i> Dashboard
            </a>
            <a href="/cm1/admin/articles" class="flex items-center gap-3 bg-gray-700 text-white px-4 py-3 rounded-lg">
                <i class="fa-solid fa-newspaper"></i> Artigos / Matérias
            </a>
            <a href="/cm1/admin/pages" class="flex items-center gap-3 text-gray-300 hover:bg-gray-700 hover:text-white px-4 py-3 rounded-lg transition">
                <i class="fa-solid fa-file-alt"></i> Páginas Institucionais
            </a>
            <a href="/cm1/admin/settings" class="flex items-center gap-3 text-gray-300 hover:bg-gray-700 hover:text-white px-4 py-3 rounded-lg transition">
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
                <a href="/cm1/admin/articles" class="text-gray-500 hover:text-gray-800"><i class="fa-solid fa-arrow-left"></i> Voltar</a>
                <h2 class="text-xl font-semibold text-gray-800">Editar Artigo</h2>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-6 bg-gray-50">
            <?php if (!empty($error)): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                    <p><?= htmlspecialchars($error) ?></p>
                </div>
            <?php endif; ?>

            <form action="/cm1/admin/articles/update" method="POST" enctype="multipart/form-data" class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
                
                <input type="hidden" name="id" value="<?= htmlspecialchars($article['id']) ?>">

                <div class="mb-6">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Título da Matéria</label>
                    <input type="text" name="title" id="title" required value="<?= htmlspecialchars($article['title']) ?>"
                           class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md border p-2.5">
                </div>

                <div class="mb-6">
                    <label for="featured_image" class="block text-sm font-medium text-gray-700 mb-2">Imagem de Destaque</label>
                    <?php if ($article['featured_image']): ?>
                        <div class="mb-2">
                            <img src="/cm1/Public<?= htmlspecialchars($article['featured_image']) ?>" alt="Atual" class="w-32 h-auto rounded shadow-sm border border-gray-200">
                        </div>
                    <?php endif; ?>
                    <input type="file" name="featured_image" id="featured_image" accept="image/jpeg, image/png, image/webp"
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <p class="text-xs text-gray-500 mt-1">Formatos permitidos: JPG, PNG, WEBP. Tamanho máximo: 10MB. Deixe em branco para manter a imagem atual.</p>
                </div>

                <div class="mb-6">
                    <div class="flex justify-between items-center mb-2">
                        <label for="content-editor" class="block text-sm font-medium text-gray-700">Conteúdo</label>
                        <!-- Botão IA -->
                        <button type="button" id="btn-ai-generate" class="text-sm bg-purple-100 text-purple-700 hover:bg-purple-200 px-3 py-1 rounded border border-purple-200 flex items-center gap-1 transition">
                            <i class="fa-solid fa-wand-magic-sparkles"></i> <span id="btn-ai-text">Re-escrever com IA</span>
                        </button>
                    </div>
                    <textarea name="content" id="content-editor"><?= htmlspecialchars($article['content']) ?></textarea>
                </div>

                <div class="mb-6 flex items-center">
                    <input id="is_published" name="is_published" type="checkbox" <?= $article['is_published'] ? 'checked' : '' ?> class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="is_published" class="ml-2 block text-sm text-gray-900">
                        Publicado
                    </label>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-sm transition">
                        Atualizar Matéria
                    </button>
                </div>
            </form>
        </main>
    </div>

    <!-- Overlay para o menu mobile -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-10 hidden md:hidden"></div>

    <!-- Script para Integração IA -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // --- Lógica do Menu Mobile ---
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


            // --- Lógica da IA ---
            const btnAi = document.getElementById('btn-ai-generate');
            const btnAiText = document.getElementById('btn-ai-text');
            const titleInput = document.getElementById('title');

            btnAi.addEventListener('click', async () => {
                const topic = titleInput.value.trim();
                
                if (!topic) {
                    alert('Por favor, preencha o Título da Matéria primeiro. Ele será usado como tema para a IA.');
                    titleInput.focus();
                    return;
                }

                if (!confirm(`Gerar artigo com IA sobre o tema: "${topic}"? Isso substituirá o conteúdo atual.`)) {
                    return;
                }

                // Estado de Loading
                btnAi.disabled = true;
                btnAi.classList.add('opacity-50', 'cursor-not-allowed');
                btnAiText.innerText = 'Gerando...';

                try {
                    const response = await fetch('/cm1/admin/articles/generate-ai', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ topic: topic })
                    });

                    const data = await response.json();

                    if (data.success) {
                        // Insere o HTML gerado no TinyMCE
                        tinymce.get('content-editor').setContent(data.content);
                    } else {
                        alert('Erro ao gerar: ' + (data.message || 'Desconhecido'));
                    }
                } catch (error) {
                    console.error('Erro na requisição:', error);
                    alert('Erro de comunicação com o servidor.');
                } finally {
                    // Restaura botão
                    btnAi.disabled = false;
                    btnAi.classList.remove('opacity-50', 'cursor-not-allowed');
                    btnAiText.innerText = 'Gerar com IA';
                }
            });
        });
    </script>
</body>
</html>
