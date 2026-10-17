<?php
$currentNav = 'articles';
$pageHeading = 'Novo Artigo';
$backUrl = url('admin/articles');
$useTinymce = true;
$aiUrl = url('admin/articles/generate-ai');
$extraScripts = <<<HTML
<script>
document.addEventListener('DOMContentLoaded', () => {
    const btnAi = document.getElementById('btn-ai-generate');
    const btnAiText = document.getElementById('btn-ai-text');
    const titleInput = document.getElementById('title');
    btnAi.addEventListener('click', async () => {
        const topic = titleInput.value.trim();
        if (!topic) {
            alert('Preencha o Título da Matéria primeiro.');
            titleInput.focus();
            return;
        }
        if (!confirm('Gerar artigo com IA sobre: "' + topic + '"? Isso substituirá o conteúdo atual.')) return;
        btnAi.disabled = true;
        btnAiText.innerText = 'Gerando...';
        try {
            const response = await fetch('{$aiUrl}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ topic })
            });
            const data = await response.json();
            if (data.success) tinymce.get('content-editor').setContent(data.content);
            else alert('Erro ao gerar: ' + (data.message || 'Desconhecido'));
        } catch (error) {
            alert('Erro de comunicação com o servidor.');
        } finally {
            btnAi.disabled = false;
            btnAiText.innerText = 'Gerar com IA';
        }
    });
});
</script>
HTML;
require dirname(__DIR__) . '/partials/header.php';
?>

<?php if (!empty($error)): ?>
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert"><p><?= e($error) ?></p></div>
<?php endif; ?>

<form action="<?= url('admin/articles/store') ?>" method="POST" enctype="multipart/form-data" class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
    <?= csrf_field() ?>
    <div class="mb-6">
        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Título da Matéria</label>
        <input type="text" name="title" id="title" required class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md border p-2.5">
    </div>
    <div class="mb-6">
        <label for="featured_image" class="block text-sm font-medium text-gray-700 mb-2">Imagem de Destaque</label>
        <input type="file" name="featured_image" id="featured_image" accept="image/jpeg, image/png, image/webp" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
        <p class="text-xs text-gray-500 mt-1">Formatos: JPG, PNG, WEBP. Máximo: 10MB.</p>
    </div>
    <div class="mb-6">
        <div class="flex justify-between items-center mb-2">
            <label for="content-editor" class="block text-sm font-medium text-gray-700">Conteúdo</label>
            <button type="button" id="btn-ai-generate" class="text-sm bg-purple-100 text-purple-700 hover:bg-purple-200 px-3 py-1 rounded border border-purple-200 flex items-center gap-1">
                <i class="fa-solid fa-wand-magic-sparkles"></i> <span id="btn-ai-text">Gerar com IA</span>
            </button>
        </div>
        <textarea name="content" id="content-editor"></textarea>
    </div>
    <div class="mb-6 flex items-center">
        <input id="is_published" name="is_published" type="checkbox" checked class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
        <label for="is_published" class="ml-2 block text-sm text-gray-900">Publicar imediatamente</label>
    </div>
    <div class="mb-8 flex items-center bg-blue-50 p-3 rounded border border-blue-100">
        <input id="post_social" name="post_social" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
        <label for="post_social" class="ml-2 block text-sm text-gray-700 flex items-center gap-2">
            Publicar nas Redes Sociais (Meta) <i class="fa-brands fa-facebook text-blue-600"></i> <i class="fa-brands fa-instagram text-pink-600"></i>
        </label>
    </div>
    <div class="flex justify-end">
        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 shadow-sm">Salvar Matéria</button>
    </div>
</form>

<?php require dirname(__DIR__) . '/partials/footer.php'; ?>
