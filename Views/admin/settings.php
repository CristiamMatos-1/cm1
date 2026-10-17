<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    <?php require dirname(__DIR__) . '/layout/brand-head.php'; ?>
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
            <a href="<?= htmlspecialchars(url('admin/users')) ?>" class="flex items-center gap-3 text-gray-300 hover:bg-gray-700 hover:text-white px-4 py-3 rounded-lg transition">
                <i class="fa-solid fa-users"></i> Usuários
            </a>
            <a href="<?= htmlspecialchars(url('admin/settings')) ?>" class="flex items-center gap-3 bg-gray-700 text-white px-4 py-3 rounded-lg">
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
                <h2 class="text-xl font-semibold text-gray-800">Configurações Gerais</h2>
            </div>
            <a href="<?= htmlspecialchars(url('logout')) ?>" class="text-sm text-red-600 hover:underline">Sair</a>
        </header>

        <main class="flex-1 overflow-y-auto p-6 bg-gray-50">
            
            <?php if (!empty($msg)): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                    <p><?= htmlspecialchars($msg) ?></p>
                </div>
            <?php endif; ?>

            <form action="<?= htmlspecialchars(url('admin/settings/store')) ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
                
                <!-- Sessão Marca (Nome e Logo) -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
                    <h3 class="text-lg font-medium text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-building text-brand-blue text-xl"></i> Identidade Visual
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="company_name" class="block text-sm font-medium text-gray-700 mb-2">Nome da Empresa</label>
                            <input type="text" name="company_name" id="company_name" 
                                   value="<?= e($settings['company_name'] ?? \Config\App::BRAND_NAME) ?>"
                                   class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md border p-2.5">
                        </div>
                        <div>
                            <label for="company_logo" class="block text-sm font-medium text-gray-700 mb-2">Logotipo da Empresa</label>
                            <?php if (!empty($settings['company_logo'])): ?>
                                <div class="mb-3 flex items-center gap-4">
                                    <img src="<?= htmlspecialchars(asset($settings['company_logo'])) ?>" alt="Logo Atual" class="h-12 w-auto object-contain border p-1 rounded">
                                    <label class="inline-flex items-center text-sm text-red-600 cursor-pointer">
                                        <input type="checkbox" name="remove_logo" value="1" class="mr-2"> Remover Logo
                                    </label>
                                </div>
                            <?php endif; ?>
                            <input type="file" name="company_logo" id="company_logo" accept="image/jpeg, image/png, image/webp"
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        </div>
                    </div>
                </div>

                <!-- Sessão Textos da Página Inicial (Frontend) -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
                    <h3 class="text-lg font-medium text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-pen-to-square text-orange-500 text-xl"></i> Textos da Página Inicial (Frontend)
                    </h3>
                    
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="hero_title" class="block text-sm font-medium text-gray-700 mb-2">Título Principal (Banner Azul)</label>
                                <input type="text" name="hero_title" id="hero_title" 
                                       value="<?= e($settings['hero_title'] ?? 'Inovação e Tecnologia para o seu Negócio') ?>"
                                       class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md border p-2.5">
                            </div>
                            <div>
                                <label for="hero_subtitle" class="block text-sm font-medium text-gray-700 mb-2">Subtítulo (Banner Azul)</label>
                                <textarea name="hero_subtitle" id="hero_subtitle" rows="2"
                                          class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md border p-2.5"><?= e($settings['hero_subtitle'] ?? 'A CONINFOMS entrega soluções de ponta, desenvolvimento de sistemas e infraestrutura moderna para impulsionar o seu sucesso no mundo digital.') ?></textarea>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4 border-t pt-4 border-gray-200">
                            <div>
                                <label for="hero_btn1_text" class="block text-sm font-medium text-gray-700 mb-2">Botão 1 - Texto <span class="text-xs text-gray-500 font-normal">(Deixe vazio para ocultar)</span></label>
                                <input type="text" name="hero_btn1_text" id="hero_btn1_text" 
                                       value="<?= e($settings['hero_btn1_text'] ?? 'Conheça a Empresa') ?>"
                                       class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md border p-2.5">
                            </div>
                            <div>
                                <label for="hero_btn1_link" class="block text-sm font-medium text-gray-700 mb-2">Botão 1 - Link de Destino</label>
                                <input type="text" name="hero_btn1_link" id="hero_btn1_link" 
                                       value="<?= e($settings['hero_btn1_link'] ?? '/pagina?slug=sobre-a-empresa') ?>"
                                       class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md border p-2.5">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                            <div>
                                <label for="hero_btn2_text" class="block text-sm font-medium text-gray-700 mb-2">Botão 2 - Texto <span class="text-xs text-gray-500 font-normal">(Deixe vazio para ocultar)</span></label>
                                <input type="text" name="hero_btn2_text" id="hero_btn2_text" 
                                       value="<?= e($settings['hero_btn2_text'] ?? 'Nossos Serviços') ?>"
                                       class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md border p-2.5">
                            </div>
                            <div>
                                <label for="hero_btn2_link" class="block text-sm font-medium text-gray-700 mb-2">Botão 2 - Link de Destino</label>
                                <input type="text" name="hero_btn2_link" id="hero_btn2_link" 
                                       value="<?= e($settings['hero_btn2_link'] ?? '#servicos') ?>"
                                       class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md border p-2.5">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4 border-t pt-4 border-gray-200">
                            <div>
                                <label for="articles_section_title" class="block text-sm font-medium text-gray-700 mb-2">Título da Seção de Matérias</label>
                                <input type="text" name="articles_section_title" id="articles_section_title" 
                                       value="<?= e($settings['articles_section_title'] ?? 'Nossas Últimas Matérias') ?>"
                                       class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md border p-2.5">
                            </div>
                            <div>
                                <label for="news_section_title" class="block text-sm font-medium text-gray-700 mb-2">Título da Seção de Novidades</label>
                                <input type="text" name="news_section_title" id="news_section_title" 
                                       value="<?= e($settings['news_section_title'] ?? 'Últimas Novidades') ?>"
                                       class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md border p-2.5">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-t pt-4 border-gray-200">
                            <div>
                                <label for="youtube_section_title" class="block text-sm font-medium text-gray-700 mb-2">Subtítulo do Vídeo (Acompanhe nosso Canal)</label>
                                <input type="text" name="youtube_section_title" id="youtube_section_title" 
                                       value="<?= e($settings['youtube_section_title'] ?? 'Acompanhe nosso Canal') ?>"
                                       class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md border p-2.5">
                            </div>
                            <div>
                                <label for="youtube_section_subtitle" class="block text-sm font-medium text-gray-700 mb-2">Texto da Seção de Vídeo</label>
                                <textarea name="youtube_section_subtitle" id="youtube_section_subtitle" rows="2"
                                          class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md border p-2.5"><?= e($settings['youtube_section_subtitle'] ?? 'Fique por dentro das últimas tendências de tecnologia, tutoriais e novidades sobre a CONINFOMS diretamente no nosso canal do YouTube.') ?></textarea>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-t pt-4 border-gray-200">
                            <div>
                                <label for="client_area_text" class="block text-sm font-medium text-gray-700 mb-2">Texto do Botão Área do Cliente <span class="text-xs text-gray-500 font-normal">(Deixe vazio para ocultar)</span></label>
                                <input type="text" name="client_area_text" id="client_area_text" 
                                       value="<?= e($settings['client_area_text'] ?? 'Área do Cliente') ?>"
                                       class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md border p-2.5">
                            </div>
                            <div>
                                <label for="client_area_link" class="block text-sm font-medium text-gray-700 mb-2">Link da Área do Cliente</label>
                                <input type="url" name="client_area_link" id="client_area_link" 
                                       value="<?= e($settings['client_area_link'] ?? \Config\App::SITE_URL . '/cm/') ?>"
                                       class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md border p-2.5">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-6 border-t pt-4 border-gray-200">
                            <div>
                                <label for="footer_about_text" class="block text-sm font-medium text-gray-700 mb-2">Texto Descritivo do Rodapé (Logo/Sobre)</label>
                                <textarea name="footer_about_text" id="footer_about_text" rows="2"
                                          class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md border p-2.5"><?= e($settings['footer_about_text'] ?? 'Soluções em tecnologia da informação. Trazendo inovação e estabilidade para o seu negócio.') ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sessão WhatsApp -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
                    <h3 class="text-lg font-medium text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fa-brands fa-whatsapp text-green-500 text-xl"></i> Contato via WhatsApp (canal único)
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="whatsapp_number" class="block text-sm font-medium text-gray-700 mb-2">Número (Apenas números, com DDI + DDD)</label>
                            <input type="text" name="whatsapp_number" id="whatsapp_number" 
                                   value="<?= e($settings['whatsapp_number'] ?? '556799999999') ?>"
                                   placeholder="Ex: 556799999999"
                                   class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md border p-2.5">
                            <p class="text-xs text-gray-500 mt-1">Exemplo Brasil: 55 (DDI) 67 (DDD) 99999999 (Número)</p>
                        </div>
                        <div>
                            <label for="whatsapp_message" class="block text-sm font-medium text-gray-700 mb-2">Mensagem Padrão</label>
                            <input type="text" name="whatsapp_message" id="whatsapp_message" 
                                   value="<?= e($settings['whatsapp_message'] ?? 'Olá! Gostaria de mais informações sobre os serviços da coninfoms.') ?>"
                                   class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md border p-2.5">
                        </div>
                    </div>
                </div>

                <!-- Sessão YouTube -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
                    <h3 class="text-lg font-medium text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fa-brands fa-youtube text-red-500 text-xl"></i> Canal do YouTube (Página Inicial)
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="youtube_channel_id" class="block text-sm font-medium text-gray-700 mb-2">ID do Canal (Channel ID)</label>
                            <input type="text" name="youtube_channel_id" id="youtube_channel_id" 
                                   value="<?= e($settings['youtube_channel_id'] ?? '') ?>"
                                   placeholder="Ex: UC_x5XG1OV2P6uZZ5FSM9Ttw"
                                   class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md border p-2.5">
                        </div>
                        <div>
                            <label for="youtube_api_key" class="block text-sm font-medium text-gray-700 mb-2">API Key (Google Cloud Console)</label>
                            <input type="text" name="youtube_api_key" id="youtube_api_key" 
                                   value="<?= e($settings['youtube_api_key'] ?? '') ?>"
                                   placeholder="Sua chave de API do YouTube Data v3"
                                   class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md border p-2.5">
                        </div>
                    </div>
                </div>

                <!-- Sessão Redes Sociais (Postagem Automática) -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
                    <h3 class="text-lg font-medium text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-share-nodes text-indigo-500 text-xl"></i> Postagem Automática (Facebook & Instagram)
                    </h3>
                    <p class="text-sm text-gray-500 mb-6">Preencha as chaves da API Graph do Facebook Developers para que o sistema publique automaticamente suas novas matérias nas redes sociais.</p>
                    
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="facebook_page_id" class="block text-sm font-medium text-gray-700 mb-2">ID da Página do Facebook</label>
                                <input type="text" name="facebook_page_id" id="facebook_page_id" 
                                       value="<?= e($settings['facebook_page_id'] ?? '') ?>"
                                       class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md border p-2.5">
                            </div>
                            <div>
                                <label for="facebook_access_token" class="block text-sm font-medium text-gray-700 mb-2">Token de Acesso (Página)</label>
                                <input type="text" name="facebook_access_token" id="facebook_access_token" 
                                       value="<?= e($settings['facebook_access_token'] ?? '') ?>"
                                       class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md border p-2.5">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-t pt-4 border-gray-200">
                            <div>
                                <label for="instagram_account_id" class="block text-sm font-medium text-gray-700 mb-2">ID da Conta Business do Instagram</label>
                                <input type="text" name="instagram_account_id" id="instagram_account_id" 
                                       value="<?= e($settings['instagram_account_id'] ?? '') ?>"
                                       class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md border p-2.5">
                            </div>
                            <div>
                                <label for="instagram_access_token" class="block text-sm font-medium text-gray-700 mb-2">Token de Acesso (Instagram)</label>
                                <input type="text" name="instagram_access_token" id="instagram_access_token" 
                                       value="<?= e($settings['instagram_access_token'] ?? '') ?>"
                                       class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md border p-2.5">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-sm transition">
                        <i class="fa-solid fa-save mr-2"></i> Salvar Configurações
                    </button>
                </div>
            </form>
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