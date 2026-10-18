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
                        <li><a href="<?= url('suporte') ?>" class="hover:text-white transition-colors"><?= e($supportLinkLabel ?? 'Suporte') ?></a></li>
                        <?php if (!empty($footerPages)): ?>
                            <?php foreach ($footerPages as $mp): ?>
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

            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-sm text-gray-500 space-y-2">
                <p>&copy; <?= date('Y') ?> <?= e($globalCompanyName ?? 'CONINFOMS') ?>. Todos os direitos reservados.</p>
                <p>
                    <a href="<?= url('pagina', ['slug' => 'politica-de-privacidade']) ?>" class="hover:text-white transition-colors">Política de Privacidade</a>
                    <span class="mx-2 text-gray-600">·</span>
                    <button type="button" id="cookie-prefs-open" class="hover:text-white transition-colors underline-offset-2 hover:underline">Preferências de cookies</button>
                </p>
            </div>
        </div>
    </footer>

    <div id="cookie-consent" class="hidden fixed inset-x-0 bottom-0 z-[70] p-4 md:p-6">
        <div class="max-w-4xl mx-auto bg-white border border-gray-200 shadow-2xl rounded-xl p-5 md:p-6">
            <div id="cookie-consent-main">
                <h2 class="text-lg font-bold text-gray-900 mb-2">Cookies e privacidade</h2>
                <p class="text-sm text-gray-600 mb-4 leading-relaxed">
                    Usamos cookies necessários ao funcionamento do site e, com o seu consentimento, cookies opcionais.
                    Consulte a
                    <a href="<?= url('pagina', ['slug' => 'politica-de-privacidade']) ?>" class="text-brand-blue font-medium hover:underline">Política de Privacidade</a>.
                </p>
                <div class="flex flex-col sm:flex-row gap-3 sm:justify-end">
                    <button type="button" id="cookie-manage-btn" class="px-4 py-2.5 rounded-md border border-gray-300 text-gray-700 font-medium hover:bg-gray-50">Gerenciar</button>
                    <button type="button" id="cookie-accept-btn" class="px-4 py-2.5 rounded-md bg-brand-blue text-white font-medium hover:bg-blue-700">Aceitar todos</button>
                </div>
            </div>
            <div id="cookie-consent-manage" class="hidden">
                <h2 class="text-lg font-bold text-gray-900 mb-2">Gerenciar preferências</h2>
                <p class="text-sm text-gray-600 mb-4">Escolha quais categorias deseja permitir.</p>
                <div class="space-y-3 mb-5">
                    <label class="flex items-start gap-3 p-3 rounded-lg bg-gray-50 border border-gray-100">
                        <input type="checkbox" checked disabled class="mt-1 h-4 w-4 text-blue-600 border-gray-300 rounded">
                        <span>
                            <span class="block text-sm font-semibold text-gray-900">Necessários</span>
                            <span class="block text-xs text-gray-500">Essenciais para o site (sempre ativos).</span>
                        </span>
                    </label>
                    <label class="flex items-start gap-3 p-3 rounded-lg bg-gray-50 border border-gray-100 cursor-pointer">
                        <input type="checkbox" id="cookie-pref-analytics" class="mt-1 h-4 w-4 text-blue-600 border-gray-300 rounded">
                        <span>
                            <span class="block text-sm font-semibold text-gray-900">Analíticos</span>
                            <span class="block text-xs text-gray-500">Ajudam a entender o uso do site.</span>
                        </span>
                    </label>
                    <label class="flex items-start gap-3 p-3 rounded-lg bg-gray-50 border border-gray-100 cursor-pointer">
                        <input type="checkbox" id="cookie-pref-marketing" class="mt-1 h-4 w-4 text-blue-600 border-gray-300 rounded">
                        <span>
                            <span class="block text-sm font-semibold text-gray-900">Marketing</span>
                            <span class="block text-xs text-gray-500">Usados para comunicações e campanhas.</span>
                        </span>
                    </label>
                </div>
                <div class="flex flex-col sm:flex-row gap-3 sm:justify-end">
                    <button type="button" id="cookie-manage-back" class="px-4 py-2.5 rounded-md border border-gray-300 text-gray-700 font-medium hover:bg-gray-50">Voltar</button>
                    <button type="button" id="cookie-save-prefs" class="px-4 py-2.5 rounded-md bg-brand-blue text-white font-medium hover:bg-blue-700">Salvar preferências</button>
                </div>
            </div>
        </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const KEY = 'cm1_cookie_consent_v1';
        const banner = document.getElementById('cookie-consent');
        const main = document.getElementById('cookie-consent-main');
        const manage = document.getElementById('cookie-consent-manage');
        if (!banner || !main || !manage) return;

        const read = () => {
            try { return JSON.parse(localStorage.getItem(KEY) || 'null'); } catch (e) { return null; }
        };
        const write = (prefs) => {
            try { localStorage.setItem(KEY, JSON.stringify(prefs)); } catch (e) {}
            banner.classList.add('hidden');
        };
        const showManage = (open) => {
            main.classList.toggle('hidden', open);
            manage.classList.toggle('hidden', !open);
            if (open) {
                const prefs = read() || {};
                const a = document.getElementById('cookie-pref-analytics');
                const m = document.getElementById('cookie-pref-marketing');
                if (a) a.checked = !!prefs.analytics;
                if (m) m.checked = !!prefs.marketing;
            }
        };

        const existing = read();
        if (!existing || existing.necessary !== true) {
            banner.classList.remove('hidden');
        }

        document.getElementById('cookie-accept-btn')?.addEventListener('click', () => {
            write({ necessary: true, analytics: true, marketing: true, at: Date.now() });
        });
        document.getElementById('cookie-manage-btn')?.addEventListener('click', () => showManage(true));
        document.getElementById('cookie-manage-back')?.addEventListener('click', () => showManage(false));
        document.getElementById('cookie-save-prefs')?.addEventListener('click', () => {
            const analytics = !!document.getElementById('cookie-pref-analytics')?.checked;
            const marketing = !!document.getElementById('cookie-pref-marketing')?.checked;
            write({ necessary: true, analytics, marketing, at: Date.now() });
        });
        document.getElementById('cookie-prefs-open')?.addEventListener('click', () => {
            banner.classList.remove('hidden');
            showManage(true);
        });
    });
    </script>

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
