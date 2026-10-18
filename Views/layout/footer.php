    </main>

    <footer class="bg-brand-dark text-white pt-12 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <div class="flex items-center gap-2 mb-4">
                        <?php if (!empty($globalCompanyLogo)): ?>
                            <img src="<?= asset($globalCompanyLogo) ?>" alt="<?= e($globalCompanyName) ?>" class="h-8 w-auto object-contain bg-white p-1 rounded">
                        <?php else: ?>
                            <div class="w-8 h-8 bg-white rounded flex items-center justify-center text-brand-dark font-bold">
                                <?= e(mb_substr($globalCompanyName ?? 'C', 0, 1)) ?>
                            </div>
                        <?php endif; ?>
                        <span class="font-bold text-xl"><?= e($globalCompanyName ?? 'CONINFOMS') ?></span>
                    </div>
                    <?php
                    $footerAboutText = 'Soluções em tecnologia da informação. Trazendo inovação e estabilidade para o seu negócio.';
                    try {
                        $footerAboutText = (new \Models\Setting())->get('footer_about_text', $footerAboutText);
                    } catch (Throwable $e) {
                    }
                    ?>
                    <p class="text-gray-400 text-sm leading-relaxed">
                        <?= e($footerAboutText) ?>
                    </p>
                </div>

                <div>
                    <h3 class="text-lg font-semibold mb-4 border-b border-gray-700 pb-2">Links Rápidos</h3>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="<?= url() ?>" class="hover:text-white transition-colors">Início</a></li>
                        <li><a href="<?= url('suporte') ?>" class="hover:text-white transition-colors">Suporte</a></li>
                        <?php if (!empty($menuPages)): ?>
                            <?php foreach (array_slice($menuPages, 0, 4) as $mp): ?>
                                <li><a href="<?= url('pagina', ['slug' => $mp['slug']]) ?>" class="hover:text-white transition-colors"><?= e($mp['title']) ?></a></li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>

                <div>
                    <h3 class="text-lg font-semibold mb-4 border-b border-gray-700 pb-2">Contato</h3>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li class="flex items-center gap-2"><i class="fa-solid fa-envelope"></i> <?= e($contactEmail ?? 'contato@coninfoms.com.br') ?></li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-phone"></i> <?= e($contactPhone ?? '(00) 0000-0000') ?></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-sm text-gray-500">
                &copy; <?= date('Y') ?> <?= e($globalCompanyName ?? 'CONINFOMS') ?>. Todos os direitos reservados.
            </div>
        </div>
    </footer>

    <?php if (!empty($whatsappUrl)): ?>
    <a href="<?= e($whatsappUrl) ?>" target="_blank" class="fixed bottom-6 right-6 bg-green-500 text-white w-14 h-14 rounded-full flex items-center justify-center text-3xl shadow-lg hover:bg-green-600 hover:-translate-y-1 transition-transform z-50" aria-label="WhatsApp">
        <i class="fa-brands fa-whatsapp"></i>
    </a>
    <?php endif; ?>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const btn = document.getElementById('mobile-menu-btn');
            const menu = document.getElementById('mobile-menu');
            if (btn && menu) {
                const icon = btn.querySelector('i');
                btn.addEventListener('click', () => {
                    menu.classList.toggle('hidden');
                    if (icon) {
                        icon.classList.toggle('fa-xmark');
                        icon.classList.toggle('fa-bars');
                    }
                });
            }
        });
    </script>
</body>
</html>
