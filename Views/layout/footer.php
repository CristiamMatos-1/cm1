    </main>

    <!-- Footer -->
    <footer class="bg-brand-dark text-white pt-12 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Coluna 1 -->
                <div>
                    <div class="flex items-center gap-2 mb-4">
                        <?php if(!empty($globalCompanyLogo)): ?>
                            <img src="/cm1/Public<?= htmlspecialchars($globalCompanyLogo) ?>" alt="<?= htmlspecialchars($globalCompanyName) ?>" class="h-8 w-auto object-contain bg-white p-1 rounded">
                        <?php else: ?>
                            <div class="w-8 h-8 bg-white rounded flex items-center justify-center text-brand-dark font-bold">
                                <?= substr($globalCompanyName ?? 'C', 0, 1) ?>
                            </div>
                        <?php endif; ?>
                        <span class="font-bold text-xl"><?= htmlspecialchars($globalCompanyName ?? 'CONINFOMS') ?></span>
                    </div>
                    <?php 
                    $footerAboutText = (new \Models\Setting())->get('footer_about_text', 'Soluções em tecnologia da informação. Trazendo inovação e estabilidade para o seu negócio.');
                    ?>
                    <p class="text-gray-400 text-sm leading-relaxed">
                        <?= htmlspecialchars($footerAboutText) ?>
                    </p>
                </div>
                
                <!-- Coluna 2 -->
                <div>
                    <h3 class="text-lg font-semibold mb-4 border-b border-gray-700 pb-2">Links Rápidos</h3>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="/cm1/" class="hover:text-white transition-colors">Início</a></li>
                        <?php if(!empty($menuPages)): ?>
                            <?php $footerPages = array_slice($menuPages, 0, 4); // Mostra até 4 páginas no rodapé ?>
                            <?php foreach($footerPages as $mp): ?>
                                <li><a href="/cm1/pagina?slug=<?= htmlspecialchars($mp['slug']) ?>" class="hover:text-white transition-colors"><?= htmlspecialchars($mp['title']) ?></a></li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>
                
                <!-- Coluna 3 -->
                <div>
                    <h3 class="text-lg font-semibold mb-4 border-b border-gray-700 pb-2">Contato</h3>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li class="flex items-center gap-2"><i class="fa-solid fa-envelope"></i> <?= htmlspecialchars($contactEmail ?? 'contato@coninfoms.com.br') ?></li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-phone"></i> <?= htmlspecialchars($contactPhone ?? '(00) 0000-0000') ?></li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-sm text-gray-500">
                &copy; <?= date('Y') ?> <?= htmlspecialchars($globalCompanyName ?? 'CONINFOMS') ?>. Todos os direitos reservados.
            </div>
        </div>
    </footer>

    <!-- WhatsApp Floating Button -->
    <?php if(!empty($whatsappUrl)): ?>
    <a href="<?= htmlspecialchars($whatsappUrl) ?>" target="_blank" class="fixed bottom-6 right-6 bg-green-500 text-white w-14 h-14 rounded-full flex items-center justify-center text-3xl shadow-lg hover:bg-green-600 hover:-translate-y-1 transition-transform z-50">
        <i class="fa-brands fa-whatsapp"></i>
    </a>
    <?php endif; ?>

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
