    </main>

    <?php
    $footerAboutText = (new \Models\Setting())->get('footer_about_text', 'Soluções em tecnologia da informação. Trazendo inovação e estabilidade para o seu negócio.');
    $globalWhatsappUrl = whatsapp_link();
    ?>

    <!-- Footer -->
    <footer class="bg-brand-dark text-white pt-12 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Coluna 1 -->
                <div>
                    <div class="flex items-center gap-2 mb-4">
                        <?php if(!empty($globalCompanyLogo)): ?>
                            <img src="<?= htmlspecialchars(asset($globalCompanyLogo)) ?>" alt="<?= e($globalCompanyName) ?>" class="h-8 w-auto object-contain bg-white p-1 rounded">
                        <?php else: ?>
                            <div class="w-8 h-8 bg-white rounded flex items-center justify-center text-brand-dark font-bold">
                                <?= e(mb_substr($globalCompanyName ?? 'c', 0, 1)) ?>
                            </div>
                        <?php endif; ?>
                        <span class="font-bold text-xl"><?= e($globalCompanyName ?? \Config\App::BRAND_NAME) ?></span>
                    </div>
                    <p class="text-brand-light/70 text-sm leading-relaxed">
                        <?= e($footerAboutText) ?>
                    </p>
                </div>

                <!-- Coluna 2 -->
                <div>
                    <h3 class="text-lg font-semibold mb-4 border-b border-brand-slate pb-2">Links Rápidos</h3>
                    <ul class="space-y-2 text-sm text-brand-light/70">
                        <li><a href="<?= htmlspecialchars(url()) ?>" class="hover:text-white transition-colors">Início</a></li>
                        <li><a href="<?= htmlspecialchars(url('materias')) ?>" class="hover:text-white transition-colors">Matérias</a></li>
                        <?php if(!empty($menuPages)): ?>
                            <?php $footerPages = array_slice($menuPages, 0, 4); // Mostra até 4 páginas no rodapé ?>
                            <?php foreach($footerPages as $mp): ?>
                                <li><a href="<?= htmlspecialchars(url('pagina?slug=' . rawurlencode($mp['slug']))) ?>" class="hover:text-white transition-colors"><?= e($mp['title']) ?></a></li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <li><a href="<?= htmlspecialchars(url('contato')) ?>" class="hover:text-white transition-colors">Contato</a></li>
                    </ul>
                </div>

                <!-- Coluna 3: canal único de contato -->
                <div>
                    <h3 class="text-lg font-semibold mb-4 border-b border-brand-slate pb-2">Contato</h3>
                    <p class="text-sm text-brand-light/70 mb-4">Fale com a gente diretamente pelo WhatsApp.</p>
                    <a href="<?= htmlspecialchars($globalWhatsappUrl) ?>" target="_blank" rel="noopener noreferrer"
                       class="inline-flex items-center gap-2 bg-[#25D366] hover:bg-[#1EBE5A] text-white font-bold px-5 py-2.5 rounded-md shadow transition-colors">
                        <i class="fa-brands fa-whatsapp text-xl"></i> Chamar no WhatsApp
                    </a>
                </div>
            </div>

            <div class="border-t border-brand-slate mt-8 pt-8 text-center text-sm text-brand-light/60">
                &copy; <?= date('Y') ?> <?= e($globalCompanyName ?? \Config\App::BRAND_NAME) ?> &middot; <?= htmlspecialchars(\Config\App::SITE_DOMAIN) ?>. Todos os direitos reservados.
            </div>
        </div>
    </footer>

    <!-- WhatsApp: botão flutuante global -->
    <a href="<?= htmlspecialchars($globalWhatsappUrl) ?>" target="_blank" rel="noopener noreferrer"
       class="wa-float" aria-label="Falar com a <?= e(\Config\App::BRAND_NAME) ?> pelo WhatsApp" title="Fale conosco pelo WhatsApp">
        <i class="fa-brands fa-whatsapp"></i>
    </a>

    <!-- Scripts Básicos -->
    <script>
        // Lógica do Menu Mobile
        document.addEventListener('DOMContentLoaded', () => {
            const btn = document.getElementById('mobile-menu-btn');
            const menu = document.getElementById('mobile-menu');

            if (btn && menu) {
                const icon = btn.querySelector('i');
                btn.addEventListener('click', () => {
                    menu.classList.toggle('hidden');
                    if (icon) {
                        if (menu.classList.contains('hidden')) {
                            icon.classList.remove('fa-xmark');
                            icon.classList.add('fa-bars');
                        } else {
                            icon.classList.remove('fa-bars');
                            icon.classList.add('fa-xmark');
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>
