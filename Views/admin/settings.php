<?php
$currentNav = 'settings';
$pageHeading = 'Configurações Gerais';
$settings = $settings ?? [];
require __DIR__ . '/partials/header.php';
?>

<?php if (!empty($msg)): ?>
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert"><p><?= e($msg) ?></p></div>
<?php endif; ?>

<form action="<?= url('admin/settings/store') ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
    <?= csrf_field() ?>

    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-800 mb-4">Identidade Visual</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="company_name" class="block text-sm font-medium text-gray-700 mb-2">Nome da Empresa</label>
                <input type="text" name="company_name" id="company_name" value="<?= e($settings['company_name'] ?? 'CONINFOMS') ?>" class="shadow-sm block w-full sm:text-sm border-gray-300 rounded-md border p-2.5">
            </div>
            <div>
                <label for="company_logo" class="block text-sm font-medium text-gray-700 mb-2">Logotipo</label>
                <?php if (!empty($settings['company_logo'])): ?>
                    <div class="mb-3 flex items-center gap-4">
                        <img src="<?= asset($settings['company_logo']) ?>" alt="Logo Atual" class="h-12 w-auto object-contain border p-1 rounded">
                        <label class="inline-flex items-center text-sm text-red-600 cursor-pointer">
                            <input type="checkbox" name="remove_logo" value="1" class="mr-2"> Remover Logo
                        </label>
                    </div>
                <?php endif; ?>
                <input type="file" name="company_logo" id="company_logo" accept="image/jpeg, image/png, image/webp" class="block w-full text-sm text-gray-500">
            </div>
        </div>
    </div>

    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6 space-y-4">
        <h3 class="text-lg font-medium text-gray-800">Textos da Página Inicial</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Título Principal</label>
                <input type="text" name="hero_title" value="<?= e($settings['hero_title'] ?? 'Inovação e Tecnologia para o seu Negócio') ?>" class="shadow-sm block w-full border-gray-300 rounded-md border p-2.5">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Subtítulo</label>
                <textarea name="hero_subtitle" rows="2" class="shadow-sm block w-full border-gray-300 rounded-md border p-2.5"><?= e($settings['hero_subtitle'] ?? 'A CONINFOMS entrega soluções de ponta.') ?></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Botão 1 - Texto</label>
                <input type="text" name="hero_btn1_text" value="<?= e($settings['hero_btn1_text'] ?? 'Conheça a Empresa') ?>" class="shadow-sm block w-full border-gray-300 rounded-md border p-2.5">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Botão 1 - Link</label>
                <input type="text" name="hero_btn1_link" value="<?= e($settings['hero_btn1_link'] ?? url('pagina', ['slug' => 'sobre-a-empresa'])) ?>" class="shadow-sm block w-full border-gray-300 rounded-md border p-2.5">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Botão 2 - Texto</label>
                <input type="text" name="hero_btn2_text" value="<?= e($settings['hero_btn2_text'] ?? 'Nossos Serviços') ?>" class="shadow-sm block w-full border-gray-300 rounded-md border p-2.5">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Botão 2 - Link</label>
                <input type="text" name="hero_btn2_link" value="<?= e($settings['hero_btn2_link'] ?? '#servicos') ?>" class="shadow-sm block w-full border-gray-300 rounded-md border p-2.5">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Título da Seção de Matérias</label>
                <input type="text" name="articles_section_title" value="<?= e($settings['articles_section_title'] ?? 'Nossas Últimas Matérias') ?>" class="shadow-sm block w-full border-gray-300 rounded-md border p-2.5">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Título da Seção de Novidades</label>
                <input type="text" name="news_section_title" value="<?= e($settings['news_section_title'] ?? 'Últimas Novidades') ?>" class="shadow-sm block w-full border-gray-300 rounded-md border p-2.5">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Título do Vídeo</label>
                <input type="text" name="youtube_section_title" value="<?= e($settings['youtube_section_title'] ?? 'Acompanhe nosso Canal') ?>" class="shadow-sm block w-full border-gray-300 rounded-md border p-2.5">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Texto da Seção de Vídeo</label>
                <textarea name="youtube_section_subtitle" rows="2" class="shadow-sm block w-full border-gray-300 rounded-md border p-2.5"><?= e($settings['youtube_section_subtitle'] ?? 'Fique por dentro das últimas tendências.') ?></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Texto Área do Cliente</label>
                <input type="text" name="client_area_text" value="<?= e($settings['client_area_text'] ?? 'Área do Cliente') ?>" class="shadow-sm block w-full border-gray-300 rounded-md border p-2.5">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Link Área do Cliente</label>
                <input type="url" name="client_area_link" value="<?= e($settings['client_area_link'] ?? 'https://coninfoms.com.br/cm/') ?>" class="shadow-sm block w-full border-gray-300 rounded-md border p-2.5">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Texto do Rodapé</label>
            <textarea name="footer_about_text" rows="2" class="shadow-sm block w-full border-gray-300 rounded-md border p-2.5"><?= e($settings['footer_about_text'] ?? 'Soluções em tecnologia da informação.') ?></textarea>
        </div>
    </div>

    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-800 mb-4">Contatos do Rodapé</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">E-mail</label>
                <input type="email" name="contact_email" value="<?= e($settings['contact_email'] ?? 'contato@coninfoms.com.br') ?>" class="shadow-sm block w-full border-gray-300 rounded-md border p-2.5">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Telefone</label>
                <input type="text" name="contact_phone" value="<?= e($settings['contact_phone'] ?? '(00) 0000-0000') ?>" class="shadow-sm block w-full border-gray-300 rounded-md border p-2.5">
            </div>
        </div>
    </div>

    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-800 mb-4">WhatsApp</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Número (DDI+DDD)</label>
                <input type="text" name="whatsapp_number" value="<?= e($settings['whatsapp_number'] ?? '556799999999') ?>" class="shadow-sm block w-full border-gray-300 rounded-md border p-2.5">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Mensagem Padrão</label>
                <input type="text" name="whatsapp_message" value="<?= e($settings['whatsapp_message'] ?? 'Olá! Gostaria de mais informações.') ?>" class="shadow-sm block w-full border-gray-300 rounded-md border p-2.5">
            </div>
        </div>
    </div>

    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-800 mb-4">YouTube</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Channel ID</label>
                <input type="text" name="youtube_channel_id" value="<?= e($settings['youtube_channel_id'] ?? '') ?>" class="shadow-sm block w-full border-gray-300 rounded-md border p-2.5">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">API Key</label>
                <input type="text" name="youtube_api_key" value="<?= e($settings['youtube_api_key'] ?? '') ?>" class="shadow-sm block w-full border-gray-300 rounded-md border p-2.5">
            </div>
        </div>
    </div>

    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-800 mb-4">Gemini IA</h3>
        <label class="block text-sm font-medium text-gray-700 mb-2">API Key do Gemini</label>
        <input type="text" name="gemini_api_key" value="<?= e($settings['gemini_api_key'] ?? '') ?>" class="shadow-sm block w-full border-gray-300 rounded-md border p-2.5">
    </div>

    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6 space-y-4">
        <h3 class="text-lg font-medium text-gray-800">Postagem Automática (Facebook e Instagram)</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">ID da Página do Facebook</label>
                <input type="text" name="facebook_page_id" value="<?= e($settings['facebook_page_id'] ?? '') ?>" class="shadow-sm block w-full border-gray-300 rounded-md border p-2.5">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Token de Acesso (Página)</label>
                <input type="text" name="facebook_access_token" value="<?= e($settings['facebook_access_token'] ?? '') ?>" class="shadow-sm block w-full border-gray-300 rounded-md border p-2.5">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">ID do Instagram Business</label>
                <input type="text" name="instagram_account_id" value="<?= e($settings['instagram_account_id'] ?? '') ?>" class="shadow-sm block w-full border-gray-300 rounded-md border p-2.5">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Token de Acesso (Instagram)</label>
                <input type="text" name="instagram_access_token" value="<?= e($settings['instagram_access_token'] ?? '') ?>" class="shadow-sm block w-full border-gray-300 rounded-md border p-2.5">
            </div>
        </div>
    </div>

    <div class="flex justify-end">
        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 shadow-sm">
            <i class="fa-solid fa-save mr-2"></i> Salvar Configurações
        </button>
    </div>
</form>

<?php require __DIR__ . '/partials/footer.php'; ?>
