<?php require dirname(__DIR__) . '/layout/header.php'; ?>

<section class="bg-brand-blue text-white py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center md:text-left md:w-2/3">
            <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight mb-4">
                <?= e($heroTitle ?? 'Inovação e Tecnologia para o seu Negócio') ?>
            </h1>
            <p class="text-lg md:text-xl text-blue-100 mb-8">
                <?= e($heroSubtitle ?? 'A CONINFOMS entrega soluções de ponta, desenvolvimento de sistemas e infraestrutura moderna para impulsionar o seu sucesso no mundo digital.') ?>
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center md:justify-start">
                <?php if (!empty(trim($heroBtn1Text ?? ''))): ?>
                    <a href="<?= e($heroBtn1Link ?? '#') ?>" class="bg-white text-brand-blue font-bold py-3 px-8 rounded-md hover:bg-gray-100 transition shadow-lg text-center">
                        <?= e($heroBtn1Text) ?>
                    </a>
                <?php endif; ?>
                <?php if (!empty(trim($heroBtn2Text ?? ''))): ?>
                    <a href="<?= e($heroBtn2Link ?? '#') ?>" class="bg-transparent border-2 border-white text-white font-bold py-3 px-8 rounded-md hover:bg-white hover:text-brand-blue transition text-center">
                        <?= e($heroBtn2Text) ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900"><?= e($articlesSectionTitle ?? 'Nossas Últimas Matérias') ?></h2>
            <div class="w-24 h-1 bg-brand-blue mx-auto mt-4"></div>
        </div>

        <?php if (!empty($articles)): ?>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <?php foreach ($articles as $article): ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                        <?php if (!empty($article['featured_image'])): ?>
                            <img src="<?= asset($article['featured_image']) ?>" alt="<?= e($article['title']) ?>" class="w-full h-48 object-cover">
                        <?php else: ?>
                            <div class="w-full h-48 bg-gray-200 flex items-center justify-center text-gray-400">
                                <i class="fa-solid fa-image text-4xl"></i>
                            </div>
                        <?php endif; ?>
                        <div class="p-6">
                            <div class="text-xs text-brand-blue font-semibold mb-2 uppercase tracking-wide">
                                <?= date('d M, Y', strtotime($article['created_at'])) ?>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-3 line-clamp-2"><?= e($article['title']) ?></h3>
                            <p class="text-gray-600 mb-4 line-clamp-3"><?= e(mb_substr(strip_tags($article['content']), 0, 180)) ?></p>
                            <a href="<?= url('artigo', ['slug' => $article['slug']]) ?>" class="text-brand-blue font-semibold hover:underline flex items-center gap-1">
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

<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900"><?= e($newsSectionTitle ?? 'Últimas Novidades') ?></h2>
            <div class="w-24 h-1 bg-brand-blue mx-auto mt-4"></div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
            <div>
                <h3 class="text-2xl font-bold mb-4 text-gray-800"><?= e($youtubeSectionTitle ?? 'Acompanhe nosso Canal') ?></h3>
                <p class="text-gray-600 mb-6 leading-relaxed">
                    <?= e($youtubeSectionSubtitle ?? 'Fique por dentro das últimas tendências de tecnologia.') ?>
                </p>
                <?php $subscribeLink = !empty($youtubeChannelId) ? 'https://www.youtube.com/channel/' . e($youtubeChannelId) . '?sub_confirmation=1' : '#'; ?>
                <a href="<?= $subscribeLink ?>" target="_blank" class="text-brand-blue font-semibold hover:underline flex items-center gap-2">
                    <i class="fa-brands fa-youtube text-red-600 text-xl"></i> Inscreva-se agora
                </a>
            </div>
            <div class="bg-gray-200 rounded-xl aspect-video flex items-center justify-center shadow-inner relative overflow-hidden">
                <?php if (!empty($latestVideoId)): ?>
                    <iframe width="100%" height="100%" src="https://www.youtube.com/embed/<?= e($latestVideoId) ?>" title="YouTube video player" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen class="absolute inset-0 w-full h-full"></iframe>
                <?php elseif (!empty($youtubeChannelId)): ?>
                    <?php $uploadPlaylistId = 'UU' . substr($youtubeChannelId, 2); ?>
                    <iframe width="100%" height="100%" src="https://www.youtube.com/embed/videoseries?list=<?= e($uploadPlaylistId) ?>" title="YouTube video player" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen class="absolute inset-0 w-full h-full"></iframe>
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
