<?php
// Compartilhamento sempre aponta para o domínio oficial
$shareUrl = abs_url('artigo?slug=' . rawurlencode($article['slug']));
require dirname(__DIR__) . '/layout/header.php';
?>

<!-- Header do Artigo -->
<section class="bg-brand-dark text-white py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="text-blue-300 font-semibold mb-4 uppercase tracking-wider text-sm">
            <?= date('d M, Y', strtotime($article['created_at'])) ?> • Publicado por <?= e($article['author_name']) ?>
        </div>
        <h1 class="text-3xl md:text-5xl font-bold tracking-tight mb-6">
            <?= e($article['title']) ?>
        </h1>
    </div>
</section>

<!-- Imagem de Destaque -->
<?php if ($article['featured_image']): ?>
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 -mt-8 relative z-10">
    <img src="<?= htmlspecialchars(asset($article['featured_image'])) ?>" 
         alt="<?= e($article['title']) ?>" 
         class="w-full h-auto max-h-[500px] object-cover rounded-xl shadow-2xl">
</div>
<?php endif; ?>

<!-- Conteúdo do Artigo -->
<section class="py-12 bg-white">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="prose prose-lg prose-blue mx-auto">
            <?= brand_html($article['content']) ?> <!-- HTML do TinyMCE (sem escape); grafia da marca corrigida na exibição -->
        </div>

        <!-- Navegação de Retorno -->
        <div class="mt-12 pt-8 border-t border-gray-200 flex justify-between items-center">
            <a href="<?= htmlspecialchars(url()) ?>" class="text-brand-accent hover:text-blue-800 font-medium flex items-center gap-2 transition">
                <i class="fa-solid fa-arrow-left"></i> Voltar para o Início
            </a>
            <div class="flex gap-4">
                <span class="text-gray-500 font-medium">Compartilhe:</span>
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($shareUrl) ?>" target="_blank" class="text-brand-accent hover:text-brand-dark"><i class="fa-brands fa-facebook text-xl"></i></a>
                <a href="https://twitter.com/intent/tweet?url=<?= urlencode($shareUrl) ?>&text=<?= urlencode($article['title']) ?>" target="_blank" class="text-brand-accent hover:text-brand-dark"><i class="fa-brands fa-twitter text-xl"></i></a>
                <a href="https://api.whatsapp.com/send?text=<?= urlencode($article['title'] . ' - ' . $shareUrl) ?>" target="_blank" class="text-[#25D366] hover:text-[#1EBE5A]"><i class="fa-brands fa-whatsapp text-xl"></i></a>
            </div>
        </div>
    </div>
</section>

<?php require dirname(__DIR__) . '/layout/footer.php'; ?>
