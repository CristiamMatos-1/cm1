<?php
$seo = $seo ?? [];
$metaTitle = (string) ($seo['meta_title'] ?? '');
$metaDescription = (string) ($seo['meta_description'] ?? '');
$metaKeywords = (string) ($seo['meta_keywords'] ?? '');
?>
<fieldset class="mb-6 border border-gray-200 rounded-md p-4">
    <legend class="text-sm font-medium text-gray-700 px-1">SEO</legend>
    <p class="text-xs text-gray-500 mb-4">Meta tags usadas no &lt;head&gt; da página pública. Em branco, o título do conteúdo é usado.</p>
    <div class="mb-4">
        <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-1">Meta Title</label>
        <input type="text" name="meta_title" id="meta_title" maxlength="255" value="<?= e($metaTitle) ?>"
               class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md border p-2.5"
               placeholder="Título para buscadores (até 255 caracteres)">
    </div>
    <div class="mb-4">
        <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-1">Meta Description</label>
        <textarea name="meta_description" id="meta_description" rows="3" maxlength="320"
                  class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md border p-2.5"
                  placeholder="Resumo curto para Google (até 320 caracteres)"><?= e($metaDescription) ?></textarea>
    </div>
    <div>
        <label for="meta_keywords" class="block text-sm font-medium text-gray-700 mb-1">Meta Keywords</label>
        <input type="text" name="meta_keywords" id="meta_keywords" maxlength="500" value="<?= e($metaKeywords) ?>"
               class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md border p-2.5"
               placeholder="palavra1, palavra2, palavra3">
    </div>
</fieldset>
