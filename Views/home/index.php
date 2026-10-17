<?php require dirname(__DIR__) . '/layout/header.php'; ?>

<!-- Hero Section -->
<section class="bg-brand-blue text-white py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center md:text-left md:w-2/3">
            <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight mb-4">
                <?= htmlspecialchars($heroTitle ?? 'Inovação e Tecnologia para o seu Negócio') ?>
            </h1>
            <p class="text-lg md:text-xl text-blue-100 mb-8">
                <?= htmlspecialchars($heroSubtitle ?? 'A CONINFOMS entrega soluções de ponta, desenvolvimento de sistemas e infraestrutura moderna para impulsionar o seu sucesso no mundo digital.') ?>
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center md:justify-start">
                <?php if (!empty(trim($heroBtn1Text ?? ''))): ?>
                    <a href="<?= htmlspecialchars($heroBtn1Link ?? '#') ?>" class="bg-white text-brand-blue font-bold py-3 px-8 rounded-md hover:bg-gray-100 transition shadow-lg text-center">
                        <?= htmlspecialchars($heroBtn1Text) ?>
                    </a>
                <?php endif; ?>
                
                <?php if (!empty(trim($heroBtn2Text ?? ''))): ?>
                    <a href="<?= htmlspecialchars($heroBtn2Link ?? '#') ?>" class="bg-transparent border-2 border-white text-white font-bold py-3 px-8 rounded-md hover:bg-white hover:text-brand-blue transition text-center">
                        <?= htmlspecialchars($heroBtn2Text) ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Seção de Artigos / Matérias Recentes -->
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900"><?= htmlspecialchars($articlesSectionTitle ?? 'Nossas Últimas Matérias') ?></h2>
            <div class="w-24 h-1 bg-brand-blue mx-auto mt-4"></div>
        </div>

        <?php if (!empty($articles)): ?>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <?php foreach ($articles as $article): ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                        <?php if ($article['featured_image']): ?>
                            <img src="/cm1/Public<?= htmlspecialchars($article['featured_image']) ?>" alt="<?= htmlspecialchars($article['title']) ?>" class="w-full h-48 object-cover">
                        <?php else: ?>
                            <div class="w-full h-48 bg-gray-200 flex items-center justify-center text-gray-400">
                                <i class="fa-solid fa-image text-4xl"></i>
                            </div>
                        <?php endif; ?>
                        
                        <div class="p-6">
                            <div class="text-xs text-brand-blue font-semibold mb-2 uppercase tracking-wide">
                                <?= date('d M, Y', strtotime($article['created_at'])) ?>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-3 line-clamp-2">
                                <?= htmlspecialchars($article['title']) ?>
                            </h3>
                            <p class="text-gray-600 mb-4 line-clamp-3">
                                <?= strip_tags($article['content']) ?>
                            </p>
                            <a href="/cm1/artigo?slug=<?= htmlspecialchars($article['slug']) ?>" class="text-brand-blue font-semibold hover:underline flex items-center gap-1">
                                Ler mais <i class="fa-solid fa-arrow-right text-sm"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center text-gray-500 py-8">
                <i class="fa-solid fa-newspaper text-4xl mb-3"></i>
                <p>Nenhuma matéria publicada ainda.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Seção de Destaques / YouTube Placeholder -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900"><?= htmlspecialchars($newsSectionTitle ?? 'Últimas Novidades') ?></h2>
            <div class="w-24 h-1 bg-brand-blue mx-auto mt-4"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
            <div>
                <h3 class="text-2xl font-bold mb-4 text-gray-800"><?= htmlspecialchars($youtubeSectionTitle ?? 'Acompanhe nosso Canal') ?></h3>
                <p class="text-gray-600 mb-6 leading-relaxed">
                    <?= htmlspecialchars($youtubeSectionSubtitle ?? 'Fique por dentro das últimas tendências de tecnologia, tutoriais e novidades sobre a CONINFOMS diretamente no nosso canal do YouTube.') ?>
                </p>
                <?php 
                // Define o link de inscrição com base no ID do canal
                $subscribeLink = !empty($youtubeChannelId) ? "https://www.youtube.com/channel/" . htmlspecialchars($youtubeChannelId) . "?sub_confirmation=1" : "#"; 
                ?>
                <a href="<?= $subscribeLink ?>" target="_blank" class="text-brand-blue font-semibold hover:underline flex items-center gap-2">
                    <i class="fa-brands fa-youtube text-red-600 text-xl"></i> Inscreva-se agora
                </a>
            </div>
            
            <!-- Integração YouTube -->
            <div class="bg-gray-200 rounded-xl aspect-video flex items-center justify-center shadow-inner relative overflow-hidden group">
                <?php if (!empty($latestVideoId)): ?>
                    <!-- Vídeo mais recente (via API) -->
                    <iframe width="100%" height="100%" src="https://www.youtube.com/embed/<?= htmlspecialchars($latestVideoId) ?>" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen class="absolute inset-0 w-full h-full"></iframe>
                <?php elseif (!empty($youtubeChannelId)): ?>
                    <?php 
                        // Truque do YouTube: Trocar o 'UC' do ID do canal por 'UU' cria uma Playlist automática 
                        // que SEMPRE reproduz o último vídeo enviado daquele canal, sem precisar de API ou Cache.
                        $uploadPlaylistId = 'UU' . substr($youtubeChannelId, 2); 
                    ?>
                    <!-- Playlist automática com o último vídeo do canal -->
                    <iframe width="100%" height="100%" src="https://www.youtube.com/embed/videoseries?list=<?= htmlspecialchars($uploadPlaylistId) ?>" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen class="absolute inset-0 w-full h-full"></iframe>
                <?php else: ?>
                    <i class="fa-brands fa-youtube text-6xl text-gray-400"></i>
                    <div class="absolute bottom-4 left-4 bg-black bg-opacity-70 text-white px-3 py-1 rounded text-sm font-medium">
                        Configure o Canal no Painel
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require dirname(__DIR__) . '/layout/footer.php'; ?>
