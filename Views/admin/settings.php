<?php
$currentNav = 'settings';
$pageHeading = 'Configurações Gerais';
$settings = $settings ?? [];
require __DIR__ . '/partials/header.php';
?>

<?php if (!empty($msg)): ?>
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert"><p><?= e($msg) ?></p></div>
<?php endif; ?>
<?php if (!empty($error)): ?>
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert"><p><?= e($error) ?></p></div>
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
                <input type="text" name="news_section_title" value="<?= e($settings['news_section_title'] ?? '') ?>" placeholder="Deixe vazio para ocultar" class="shadow-sm block w-full border-gray-300 rounded-md border p-2.5">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Título do Vídeo</label>
                <input type="text" name="youtube_section_title" value="<?= e($settings['youtube_section_title'] ?? '') ?>" placeholder="Deixe vazio para ocultar" class="shadow-sm block w-full border-gray-300 rounded-md border p-2.5">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Texto da Seção de Vídeo</label>
                <textarea name="youtube_section_subtitle" rows="2" placeholder="Deixe vazio para ocultar" class="shadow-sm block w-full border-gray-300 rounded-md border p-2.5"><?= e($settings['youtube_section_subtitle'] ?? '') ?></textarea>
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

    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6 space-y-4">
        <h3 class="text-lg font-medium text-gray-800">Redes sociais</h3>
        <p class="text-sm text-gray-500">Preencha só as redes que quiser exibir. Campos vazios não aparecem no site.</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Instagram</label>
                <input type="url" name="social_instagram_url" value="<?= e($settings['social_instagram_url'] ?? '') ?>" placeholder="https://www.instagram.com/..." class="shadow-sm block w-full border-gray-300 rounded-md border p-2.5">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Facebook</label>
                <input type="url" name="social_facebook_url" value="<?= e($settings['social_facebook_url'] ?? '') ?>" placeholder="https://www.facebook.com/..." class="shadow-sm block w-full border-gray-300 rounded-md border p-2.5">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">LinkedIn</label>
                <input type="url" name="social_linkedin_url" value="<?= e($settings['social_linkedin_url'] ?? '') ?>" placeholder="https://www.linkedin.com/..." class="shadow-sm block w-full border-gray-300 rounded-md border p-2.5">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">YouTube (canal / perfil)</label>
                <input type="url" name="social_youtube_url" value="<?= e($settings['social_youtube_url'] ?? '') ?>" placeholder="https://www.youtube.com/@..." class="shadow-sm block w-full border-gray-300 rounded-md border p-2.5">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">X (Twitter)</label>
                <input type="url" name="social_x_url" value="<?= e($settings['social_x_url'] ?? '') ?>" placeholder="https://x.com/..." class="shadow-sm block w-full border-gray-300 rounded-md border p-2.5">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">TikTok</label>
                <input type="url" name="social_tiktok_url" value="<?= e($settings['social_tiktok_url'] ?? '') ?>" placeholder="https://www.tiktok.com/@..." class="shadow-sm block w-full border-gray-300 rounded-md border p-2.5">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">WhatsApp (link opcional)</label>
                <input type="url" name="social_whatsapp_url" value="<?= e($settings['social_whatsapp_url'] ?? '') ?>" placeholder="Deixe vazio para usar o número acima" class="shadow-sm block w-full border-gray-300 rounded-md border p-2.5">
            </div>
        </div>
    </div>

    <?php
        $videoMode = $settings['home_video_mode'] ?? '';
        if (!in_array($videoMode, ['mp4', 'youtube_video', 'youtube_channel', 'none'], true)) {
            if (!empty($settings['home_video_file'])) {
                $videoMode = 'mp4';
            } elseif (!empty($settings['youtube_video_url'])) {
                $videoMode = 'youtube_video';
            } elseif (!empty($settings['youtube_channel_url']) || !empty($settings['youtube_channel_id'])) {
                $videoMode = 'youtube_channel';
            }
        }
    ?>
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6 space-y-4">
        <h3 class="text-lg font-medium text-gray-800">Vídeo da página inicial</h3>
        <p class="text-sm text-gray-500">Escolha um MP4, o link de um vídeo específico do YouTube ou o link de um canal. Título, texto, player e botão de inscrição só aparecem no site quando estiverem preenchidos.</p>

        <fieldset class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <label class="flex items-start gap-2 border rounded-md p-3 cursor-pointer hover:bg-gray-50">
                <input type="radio" name="home_video_mode" value="mp4" class="mt-1 home-video-mode" <?= $videoMode === 'mp4' ? 'checked' : '' ?>>
                <span><strong>Arquivo MP4</strong><br><span class="text-xs text-gray-500">Enviar um vídeo do computador</span></span>
            </label>
            <label class="flex items-start gap-2 border rounded-md p-3 cursor-pointer hover:bg-gray-50">
                <input type="radio" name="home_video_mode" value="youtube_video" class="mt-1 home-video-mode" <?= $videoMode === 'youtube_video' ? 'checked' : '' ?>>
                <span><strong>Vídeo do YouTube</strong><br><span class="text-xs text-gray-500">Link de um vídeo específico</span></span>
            </label>
            <label class="flex items-start gap-2 border rounded-md p-3 cursor-pointer hover:bg-gray-50">
                <input type="radio" name="home_video_mode" value="youtube_channel" class="mt-1 home-video-mode" <?= $videoMode === 'youtube_channel' ? 'checked' : '' ?>>
                <span><strong>Canal do YouTube</strong><br><span class="text-xs text-gray-500">Link do canal</span></span>
            </label>
            <label class="flex items-start gap-2 border rounded-md p-3 cursor-pointer hover:bg-gray-50">
                <input type="radio" name="home_video_mode" value="none" class="mt-1 home-video-mode" <?= $videoMode === 'none' ? 'checked' : '' ?>>
                <span><strong>Ocultar player</strong><br><span class="text-xs text-gray-500">Não exibir vídeo na home</span></span>
            </label>
        </fieldset>

        <div id="video-panel-mp4" class="video-panel space-y-3">
            <?php if (!empty($settings['home_video_file'])): ?>
                <video class="w-full max-w-md rounded border" controls src="<?= asset($settings['home_video_file']) ?>"></video>
                <label class="inline-flex items-center text-sm text-red-600 cursor-pointer">
                    <input type="checkbox" name="remove_home_video" value="1" class="mr-2"> Remover MP4 atual
                </label>
            <?php endif; ?>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Escolher arquivo MP4</label>
                <input type="file" name="home_video_file" accept="video/mp4" class="block w-full text-sm text-gray-500">
                <p class="text-xs text-gray-500 mt-1">Apenas MP4. Máximo 50 MB (ou o limite do servidor).</p>
            </div>
        </div>

        <div id="video-panel-youtube_video" class="video-panel">
            <label class="block text-sm font-medium text-gray-700 mb-2">Link do vídeo</label>
            <input type="text" name="youtube_video_url" value="<?= e($settings['youtube_video_url'] ?? '') ?>" placeholder="https://www.youtube.com/watch?v=... ou https://youtu.be/..." class="shadow-sm block w-full border-gray-300 rounded-md border p-2.5">
        </div>

        <div id="video-panel-youtube_channel" class="video-panel">
            <label class="block text-sm font-medium text-gray-700 mb-2">Link do canal</label>
            <input type="text" name="youtube_channel_url" value="<?= e($settings['youtube_channel_url'] ?? ($settings['youtube_channel_id'] ?? '')) ?>" placeholder="https://www.youtube.com/@canal ou Channel ID (UC...)" class="shadow-sm block w-full border-gray-300 rounded-md border p-2.5">
            <p class="text-xs text-gray-500 mt-1">O player do canal usa o ID UC... (YouTube Studio → Sobre). Um link @canal mostra só o botão Inscreva-se.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2 border-t border-gray-100">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">API Key (opcional, para último vídeo do canal)</label>
                <input type="text" name="youtube_api_key" value="<?= e($settings['youtube_api_key'] ?? '') ?>" class="shadow-sm block w-full border-gray-300 rounded-md border p-2.5">
            </div>
        </div>
    </div>
    <script>
        (function () {
            const panels = {
                mp4: document.getElementById('video-panel-mp4'),
                youtube_video: document.getElementById('video-panel-youtube_video'),
                youtube_channel: document.getElementById('video-panel-youtube_channel')
            };
            function sync() {
                const selected = document.querySelector('input[name="home_video_mode"]:checked');
                const mode = selected ? selected.value : '';
                Object.entries(panels).forEach(([key, el]) => {
                    if (!el) return;
                    const show = mode === '' || mode === 'none' || mode === key;
                    el.classList.toggle('hidden', !show);
                });
            }
            document.querySelectorAll('.home-video-mode').forEach((el) => el.addEventListener('change', sync));
            sync();
        })();
    </script>

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
