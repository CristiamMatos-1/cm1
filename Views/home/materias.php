<?php require dirname(__DIR__) . '/layout/header.php'; ?>

<section class="bg-brand-blue text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight mb-4">Matérias e Novidades</h1>
        <p class="text-lg md:text-xl text-blue-100 max-w-2xl mx-auto">
            Acompanhe nossos últimos artigos, tendências de tecnologia e atualizações da CONINFOMS.
        </p>
    </div>
</section>

<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <?php if (!empty($articles)): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($articles as $article): ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow flex flex-col h-full">
                        <?php if (!empty($article['featured_image'])): ?>
                            <img src="<?= asset($article['featured_image']) ?>" alt="<?= e($article['title']) ?>" class="w-full h-56 object-cover">
                        <?php else: ?>
                            <div class="w-full h-56 bg-gray-200 flex items-center justify-center text-gray-400">
                                <i class="fa-solid fa-image text-5xl"></i>
                            </div>
                        <?php endif; ?>
                        <div class="p-6 flex flex-col flex-grow">
                            <div class="text-xs text-brand-blue font-semibold mb-2 uppercase tracking-wide">
                                <?= date('d M, Y', strtotime($article['created_at'])) ?>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-3 line-clamp-2"><?= e($article['title']) ?></h3>
                            <p class="text-gray-600 mb-4 line-clamp-3 flex-grow"><?= e(mb_substr(strip_tags($article['content']), 0, 180)) ?></p>
                            <div class="mt-auto pt-4 border-t border-gray-100">
                                <a href="<?= url('artigo', ['slug' => $article['slug']]) ?>" class="text-brand-blue font-semibold hover:text-blue-800 flex items-center gap-1 transition-colors">
                                    Ler matéria completa <i class="fa-solid fa-arrow-right text-sm"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center text-gray-500 py-16 bg-white rounded-lg shadow-sm border border-gray-100">
                <i class="fa-solid fa-newspaper text-5xl mb-4 text-gray-300"></i>
                <h3 class="text-xl font-medium text-gray-700 mb-2">Nenhuma matéria publicada ainda.</h3>
                <p>Volte em breve para conferir as novidades!</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require dirname(__DIR__) . '/layout/footer.php'; ?>
