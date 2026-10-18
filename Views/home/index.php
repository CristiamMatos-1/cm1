<?php require dirname(__DIR__) . '/layout/header.php'; ?>

<?php
$banners = $banners ?? [];
$bannerCount = count($banners);
?>
<?php if ($bannerCount > 0): ?>
<section id="home-banners" class="relative bg-gray-900 overflow-hidden" aria-roledescription="carrossel" aria-label="Banners em destaque">
    <div class="relative aspect-[16/7] md:aspect-[21/7] max-h-[520px] w-full">
        <?php foreach ($banners as $index => $banner): ?>
            <?php
                $title = trim((string) ($banner['title'] ?? ''));
                $link = trim((string) ($banner['link_url'] ?? ''));
                $isExternal = preg_match('~^https?://~i', $link) === 1;
            ?>
            <div class="banner-slide absolute inset-0 <?= $index === 0 ? '' : 'hidden' ?>" data-slide="<?= (int) $index ?>">
                <?php if ($link !== ''): ?>
                    <a href="<?= e($link) ?>" class="block h-full w-full" <?= $isExternal ? 'target="_blank" rel="noopener"' : '' ?>>
                <?php endif; ?>
                    <img src="<?= asset($banner['image_path']) ?>" alt="<?= e($title !== '' ? $title : 'Banner') ?>" class="h-full w-full object-cover">
                    <?php if ($title !== ''): ?>
                        <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/70 to-transparent p-6 md:p-10">
                            <p class="text-white text-xl md:text-3xl font-bold max-w-3xl"><?= e($title) ?></p>
                        </div>
                    <?php endif; ?>
                <?php if ($link !== ''): ?>
                    </a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
    <?php if ($bannerCount > 1): ?>
        <button type="button" data-prev class="absolute left-3 top-1/2 -translate-y-1/2 bg-black/40 hover:bg-black/60 text-white w-10 h-10 rounded-full" aria-label="Banner anterior">
            <i class="fa-solid fa-chevron-left"></i>
        </button>
        <button type="button" data-next class="absolute right-3 top-1/2 -translate-y-1/2 bg-black/40 hover:bg-black/60 text-white w-10 h-10 rounded-full" aria-label="Próximo banner">
            <i class="fa-solid fa-chevron-right"></i>
        </button>
        <div class="absolute bottom-3 left-0 right-0 flex justify-center gap-2">
            <?php for ($i = 0; $i < $bannerCount; $i++): ?>
                <button type="button" data-dot="<?= $i ?>" class="w-2.5 h-2.5 rounded-full <?= $i === 0 ? 'bg-white' : 'bg-white/40' ?>" aria-label="Ir para o banner <?= $i + 1 ?>"></button>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</section>
<?php if ($bannerCount > 1): ?>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const root = document.getElementById('home-banners');
    if (!root) return;
    const slides = Array.from(root.querySelectorAll('[data-slide]'));
    if (slides.length < 2) return;
    let current = 0;
    let timer = null;
    const show = (n) => {
        current = (n + slides.length) % slides.length;
        slides.forEach((el, idx) => el.classList.toggle('hidden', idx !== current));
        root.querySelectorAll('[data-dot]').forEach((dot, idx) => {
            dot.classList.toggle('bg-white', idx === current);
            dot.classList.toggle('bg-white/40', idx !== current);
        });
    };
    const stop = () => { if (timer) { clearInterval(timer); timer = null; } };
    const start = () => {
        stop();
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
        timer = setInterval(() => show(current + 1), 5000);
    };
    root.querySelector('[data-prev]')?.addEventListener('click', () => { show(current - 1); start(); });
    root.querySelector('[data-next]')?.addEventListener('click', () => { show(current + 1); start(); });
    root.querySelectorAll('[data-dot]').forEach((dot) => {
        dot.addEventListener('click', () => { show(Number(dot.getAttribute('data-dot'))); start(); });
    });
    root.addEventListener('mouseenter', stop);
    root.addEventListener('mouseleave', start);
    start();
});
</script>
<?php endif; ?>
<?php endif; ?>

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

<?php
$homeVideo = $homeVideo ?? ['visible' => false, 'title' => '', 'subtitle' => '', 'player' => 'none', 'channelUrl' => ''];
$newsSectionTitle = trim($newsSectionTitle ?? '');
$showVideoSection = !empty($homeVideo['visible']) || $newsSectionTitle !== '';
?>
<?php if ($showVideoSection): ?>
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <?php if ($newsSectionTitle !== ''): ?>
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900"><?= e($newsSectionTitle) ?></h2>
                <div class="w-24 h-1 bg-brand-blue mx-auto mt-4"></div>
            </div>
        <?php endif; ?>
        <div class="grid grid-cols-1 <?= ($homeVideo['player'] ?? 'none') !== 'none' ? 'md:grid-cols-2' : '' ?> gap-8 items-center">
            <?php if ($homeVideo['title'] !== '' || $homeVideo['subtitle'] !== '' || $homeVideo['channelUrl'] !== ''): ?>
            <div>
                <?php if ($homeVideo['title'] !== ''): ?>
                    <h3 class="text-2xl font-bold mb-4 text-gray-800"><?= e($homeVideo['title']) ?></h3>
                <?php endif; ?>
                <?php if ($homeVideo['subtitle'] !== ''): ?>
                    <p class="text-gray-600 mb-6 leading-relaxed"><?= e($homeVideo['subtitle']) ?></p>
                <?php endif; ?>
                <?php if ($homeVideo['channelUrl'] !== ''): ?>
                    <a href="<?= e($homeVideo['channelUrl']) ?>" target="_blank" rel="noopener" class="text-brand-blue font-semibold hover:underline flex items-center gap-2">
                        <i class="fa-brands fa-youtube text-red-600 text-xl"></i> Inscreva-se agora
                    </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php if (($homeVideo['player'] ?? 'none') === 'mp4'): ?>
                <div class="bg-black rounded-xl aspect-video shadow-inner relative overflow-hidden">
                    <video class="absolute inset-0 w-full h-full" controls playsinline src="<?= e($homeVideo['mp4Url']) ?>">
                        Seu navegador não reproduz vídeo MP4.
                    </video>
                </div>
            <?php elseif (($homeVideo['player'] ?? 'none') === 'youtube'): ?>
                <div class="bg-gray-200 rounded-xl aspect-video shadow-inner relative overflow-hidden">
                    <iframe width="100%" height="100%" src="https://www.youtube.com/embed/<?= e($homeVideo['youtubeVideoId']) ?>" title="YouTube video player" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen class="absolute inset-0 w-full h-full"></iframe>
                </div>
            <?php elseif (($homeVideo['player'] ?? 'none') === 'youtube_channel'): ?>
                <?php $uploadPlaylistId = 'UU' . substr($homeVideo['youtubeChannelId'], 2); ?>
                <div class="bg-gray-200 rounded-xl aspect-video shadow-inner relative overflow-hidden">
                    <iframe width="100%" height="100%" src="https://www.youtube.com/embed/videoseries?list=<?= e($uploadPlaylistId) ?>" title="YouTube video player" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen class="absolute inset-0 w-full h-full"></iframe>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php require dirname(__DIR__) . '/layout/footer.php'; ?>
