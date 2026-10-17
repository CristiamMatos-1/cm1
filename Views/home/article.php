<?php require dirname(__DIR__) . '/layout/header.php'; ?>

<!-- Header do Artigo -->
<section class="bg-brand-dark text-white py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="text-brand-blue font-semibold mb-4 uppercase tracking-wider text-sm">
            <?= date('d M, Y', strtotime($article['created_at'])) ?> • Publicado por <?= htmlspecialchars($article['author_name']) ?>
        </div>
        <h1 class="text-3xl md:text-5xl font-extrabold tracking-tight mb-6">
            <?= htmlspecialchars($article['title']) ?>
        </h1>
    </div>
</section>

<!-- Imagem de Destaque -->
<?php if ($article['featured_image']): ?>
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 -mt-8 relative z-10">
    <img src="/cm1/Public<?= htmlspecialchars($article['featured_image']) ?>" 
         alt="<?= htmlspecialchars($article['title']) ?>" 
         class="w-full h-auto max-h-[500px] object-cover rounded-xl shadow-2xl">
</div>
<?php endif; ?>

<!-- Conteúdo do Artigo -->
<section class="py-12 bg-white">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="prose prose-lg prose-blue mx-auto">
            <?= $article['content'] ?> <!-- Renderizando HTML sem escape pois vem do TinyMCE -->
        </div>

        <!-- Navegação de Retorno -->
        <div class="mt-12 pt-8 border-t border-gray-200 flex justify-between items-center">
            <a href="/cm1/" class="text-brand-blue hover:text-blue-800 font-medium flex items-center gap-2 transition">
                <i class="fa-solid fa-arrow-left"></i> Voltar para a Início
            </a>
            <div class="flex gap-4">
                <span class="text-gray-500 font-medium">Compartilhe:</span>
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>" target="_blank" class="text-blue-600 hover:text-blue-800"><i class="fa-brands fa-facebook text-xl"></i></a>
                <a href="https://twitter.com/intent/tweet?url=<?= urlencode('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>&text=<?= urlencode($article['title']) ?>" target="_blank" class="text-blue-400 hover:text-blue-600"><i class="fa-brands fa-twitter text-xl"></i></a>
                <a href="https://api.whatsapp.com/send?text=<?= urlencode($article['title'] . ' - https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>" target="_blank" class="text-green-500 hover:text-green-700"><i class="fa-brands fa-whatsapp text-xl"></i></a>
            </div>
        </div>
    </div>
</section>

<?php require dirname(__DIR__) . '/layout/footer.php'; ?>
