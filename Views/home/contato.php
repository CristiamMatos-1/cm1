<?php require dirname(__DIR__) . '/layout/header.php'; ?>

<section class="bg-brand-dark text-white py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-bold tracking-tight mb-4">Fale com a <?= e(\Config\App::BRAND_NAME) ?></h1>
        <p class="text-lg md:text-xl font-light text-brand-light/80 max-w-2xl mx-auto">
            Nosso atendimento é feito exclusivamente pelo WhatsApp: rápido, direto e sem burocracia.
        </p>
    </div>
</section>

<section class="py-16 bg-brand-light">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-xl shadow-lg border-t-4 border-brand-accent p-8 md:p-10 text-center">
            <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-[#25D366] text-white flex items-center justify-center text-5xl shadow">
                <i class="fa-brands fa-whatsapp"></i>
            </div>
            <h2 class="text-2xl font-bold text-brand-dark mb-3">Atendimento via WhatsApp</h2>
            <p class="text-brand-slate font-light mb-8 leading-relaxed">
                Clique no botão abaixo para iniciar uma conversa com nossa equipe. Respondemos o mais rápido possível.
            </p>

            <a href="<?= htmlspecialchars($whatsappUrl) ?>" target="_blank" rel="noopener noreferrer"
               class="inline-flex items-center justify-center gap-3 bg-[#25D366] hover:bg-[#1EBE5A] text-white font-bold text-lg px-8 py-4 rounded-lg shadow-md transition-colors w-full sm:w-auto">
                <i class="fa-brands fa-whatsapp text-2xl"></i> Chamar no WhatsApp
            </a>

            <p class="text-sm text-gray-500 mt-6">
                <i class="fa-regular fa-clock mr-1"></i> Você será redirecionado para o aplicativo do WhatsApp.
            </p>
        </div>
    </div>
</section>

<?php require dirname(__DIR__) . '/layout/footer.php'; ?>
