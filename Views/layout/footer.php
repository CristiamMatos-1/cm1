    </main>

    <footer class="bg-brand-dark text-white pt-12 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <div class="flex items-center gap-2 mb-4">
                        <?php if (!empty($globalCompanyLogo)): ?>
                            <img src="<?= asset($globalCompanyLogo) ?>" alt="<?= e($globalCompanyName) ?>" class="h-8 w-auto object-contain bg-white p-1 rounded">
                        <?php else: ?>
                            <span class="font-bold text-xl"><?= e($globalCompanyName ?? 'CONINFOMS') ?></span>
                        <?php endif; ?>
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
                    <?php if (!empty($socialLinks)): ?>
                        <div class="flex flex-wrap gap-3 mt-4">
                            <?php foreach ($socialLinks as $social): ?>
                                <a href="<?= e($social['url']) ?>" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-white transition-colors" aria-label="<?= e($social['label']) ?>">
                                    <i class="<?= e($social['icon']) ?> text-xl"></i>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
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

    <?php
    $siteAnnouncement = null;
    try {
        $siteAnnouncement = (new \Models\Announcement())->getPublished();
    } catch (Throwable $e) {
        $siteAnnouncement = null;
    }
    ?>
    <?php if (!empty($siteAnnouncement) && !empty($siteAnnouncement['body'])): ?>
    <div id="site-announcement-modal" class="hidden fixed inset-0 z-[60] flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="announcement-title" data-announcement-id="<?= e((string) $siteAnnouncement['id']) ?>" data-announcement-updated="<?= e((string) ($siteAnnouncement['updated_at'] ?? '')) ?>">
        <div class="absolute inset-0 bg-black/50" data-announcement-dismiss></div>
        <div class="relative bg-white rounded-xl shadow-xl max-w-lg w-full p-6 md:p-8">
            <button type="button" class="absolute top-3 right-3 text-gray-400 hover:text-gray-700 w-10 h-10 flex items-center justify-center rounded-full hover:bg-gray-100" data-announcement-dismiss aria-label="Fechar aviso">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
            <?php if (trim((string) ($siteAnnouncement['title'] ?? '')) !== ''): ?>
                <h2 id="announcement-title" class="text-xl font-bold text-gray-900 mb-3 pr-8"><?= e($siteAnnouncement['title']) ?></h2>
            <?php else: ?>
                <h2 id="announcement-title" class="sr-only">Aviso importante</h2>
            <?php endif; ?>
            <div class="text-gray-700 leading-relaxed whitespace-pre-line"><?= e($siteAnnouncement['body']) ?></div>
            <div class="mt-6 flex justify-end">
                <button type="button" class="bg-brand-blue text-white px-5 py-2.5 rounded-md font-medium hover:bg-blue-700" data-announcement-dismiss>Entendi</button>
            </div>
        </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('site-announcement-modal');
        if (!modal) return;
        const id = modal.getAttribute('data-announcement-id') || '';
        const updated = modal.getAttribute('data-announcement-updated') || '';
        const storageKey = 'cm1_announcement_dismissed_' + id + '_' + updated;
        try {
            if (sessionStorage.getItem(storageKey) === '1') return;
        } catch (e) {}
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        const dismiss = () => {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            try { sessionStorage.setItem(storageKey, '1'); } catch (e) {}
        };
        modal.querySelectorAll('[data-announcement-dismiss]').forEach((el) => el.addEventListener('click', dismiss));
        document.addEventListener('keydown', (ev) => {
            if (ev.key === 'Escape' && !modal.classList.contains('hidden')) dismiss();
        });
    });
    </script>
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
