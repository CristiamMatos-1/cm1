<?php require dirname(__DIR__) . '/layout/header.php'; ?>

<section class="bg-brand-dark text-white py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="text-blue-300 font-semibold mb-4 uppercase tracking-wider text-sm">
            <?= date('d M, Y', strtotime($article['created_at'])) ?>
            <?php if (!empty($article['author_name'])): ?>
                • Publicado por <?= e($article['author_name']) ?>
            <?php endif; ?>
        </div>
        <h1 class="text-3xl md:text-5xl font-extrabold tracking-tight mb-6"><?= e($article['title']) ?></h1>
    </div>
</section>

<?php
$cover = article_cover($article, $globalCompanyLogo ?? '');
$hasFeatured = !empty($article['featured_image']);
?>
<?php if ($cover !== ''): ?>
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 -mt-8 relative z-10">
    <img src="<?= asset($cover) ?>" alt="<?= e($article['title']) ?>" class="w-full h-auto max-h-[500px] rounded-xl shadow-2xl <?= $hasFeatured ? 'object-cover' : 'object-contain bg-white p-10 max-h-64' ?>">
</div>
<?php endif; ?>

<section class="py-12 bg-white">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="prose prose-lg prose-blue mx-auto text-justify [&_p]:text-justify [&_li]:text-justify">
            <?= \Core\HtmlContent::normalize((string) $article['content']) ?>
        </div>
        <div class="mt-12 pt-8 border-t border-gray-200 flex justify-between items-center flex-wrap gap-4">
            <a href="<?= url() ?>" class="text-brand-blue hover:text-blue-800 font-medium flex items-center gap-2 transition">
                <i class="fa-solid fa-arrow-left"></i> Voltar para o Início
            </a>
            <div class="flex gap-4 items-center">
                <span class="text-gray-500 font-medium">Compartilhe:</span>
                <?php $shareUrl = urlencode(\Config\App::absoluteUrl('artigo', ['slug' => $article['slug']])); ?>
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?= $shareUrl ?>" target="_blank" class="text-blue-600 hover:text-blue-800" aria-label="Facebook"><i class="fa-brands fa-facebook text-xl"></i></a>
                <a href="https://twitter.com/intent/tweet?url=<?= $shareUrl ?>&text=<?= urlencode($article['title']) ?>" target="_blank" class="text-blue-400 hover:text-blue-600" aria-label="Twitter"><i class="fa-brands fa-twitter text-xl"></i></a>
                <a href="https://api.whatsapp.com/send?text=<?= urlencode($article['title'] . ' - ' . \Config\App::absoluteUrl('artigo', ['slug' => $article['slug']])) ?>" target="_blank" class="text-green-500 hover:text-green-700" aria-label="WhatsApp"><i class="fa-brands fa-whatsapp text-xl"></i></a>
            </div>
        </div>
    </div>
</section>

<?php require dirname(__DIR__) . '/layout/footer.php'; ?>
