<?php
use Core\Services\SocialLinksService;
use Models\Page;
use Models\Setting;

$globalCompanyName = 'CONINFOMS';
$globalCompanyLogo = '';
$menuPages = $menuPages ?? [];
$footerPages = $footerPages ?? [];
$clientAreaText = 'Área do Cliente';
$clientAreaLink = 'https://coninfoms.com.br/cm/';
$socialLinks = $socialLinks ?? [];

try {
    $globalSetting = new Setting();
    $globalCompanyName = $globalSetting->get('company_name', 'CONINFOMS');
    $globalCompanyLogo = $globalSetting->get('company_logo', '');
    $clientAreaText = $globalSetting->get('client_area_text', 'Área do Cliente');
    $clientAreaLink = $globalSetting->get('client_area_link', 'https://coninfoms.com.br/cm/');
    $pageModel = new Page();
    if ($menuPages === []) {
        $menuPages = $pageModel->getPublishedFor(Page::PLACE_HEADER);
    }
    if ($footerPages === []) {
        $footerPages = $pageModel->getPublishedFor(Page::PLACE_FOOTER);
    }
    if ($socialLinks === []) {
        $socialLinks = (new SocialLinksService($globalSetting))->links();
    }
} catch (Throwable $e) {
    error_log('Layout header: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? $globalCompanyName) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            blue: '#004aad',
                            dark: '#1e293b',
                            light: '#f8fafc'
                        }
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 text-gray-800 font-sans flex flex-col min-h-screen">

    <header class="bg-white shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex-shrink-0 flex items-center">
                    <a href="<?= url() ?>" class="flex items-center gap-2 min-w-0">
                        <?php if (!empty($globalCompanyLogo)): ?>
                            <img src="<?= asset($globalCompanyLogo) ?>" alt="<?= e($globalCompanyName) ?>" class="h-10 w-auto object-contain">
                        <?php else: ?>
                            <span class="font-bold text-2xl text-brand-dark tracking-tight truncate"><?= e($globalCompanyName) ?></span>
                        <?php endif; ?>
                    </a>
                </div>

                <nav class="hidden md:flex space-x-6 items-center">
                    <a href="<?= url() ?>" class="text-gray-600 hover:text-brand-blue font-medium transition-colors">Início</a>
                    <a href="<?= url('materias') ?>" class="text-gray-600 hover:text-brand-blue font-medium transition-colors">Matérias</a>
                    <a href="<?= url('suporte') ?>" class="text-gray-600 hover:text-brand-blue font-medium transition-colors">Suporte</a>
                    <?php foreach ($menuPages as $mp): ?>
                        <a href="<?= url('pagina', ['slug' => $mp['slug']]) ?>" class="text-gray-600 hover:text-brand-blue font-medium transition-colors"><?= e($mp['title']) ?></a>
                    <?php endforeach; ?>
                    <?php if (!empty($socialLinks)): ?>
                        <div class="flex items-center gap-3 pl-2 border-l border-gray-200">
                            <?php foreach ($socialLinks as $social): ?>
                                <?php if (($social['key'] ?? '') === 'whatsapp') continue; ?>
                                <a href="<?= e($social['url']) ?>" target="_blank" rel="noopener noreferrer" class="text-gray-500 hover:text-brand-blue transition-colors" aria-label="<?= e($social['label']) ?>">
                                    <i class="<?= e($social['icon']) ?> text-lg"></i>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty(trim($clientAreaText ?? ''))): ?>
                        <a href="<?= e($clientAreaLink) ?>" target="_blank" class="bg-brand-blue text-white px-5 py-2.5 rounded-md font-medium hover:bg-blue-700 transition-colors shadow-sm flex items-center gap-2">
                            <i class="fa-solid fa-user"></i> <?= e($clientAreaText) ?>
                        </a>
                    <?php endif; ?>
                </nav>

                <div class="md:hidden flex items-center">
                    <button id="mobile-menu-btn" class="text-gray-600 hover:text-brand-blue focus:outline-none p-2" aria-label="Abrir menu">
                        <i class="fa-solid fa-bars text-2xl"></i>
                    </button>
                </div>
            </div>
        </div>

        <div id="mobile-menu" class="hidden md:hidden bg-white border-t">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3 shadow-inner">
                <a href="<?= url() ?>" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-brand-blue hover:bg-gray-50">Início</a>
                <a href="<?= url('materias') ?>" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-brand-blue hover:bg-gray-50">Matérias</a>
                <a href="<?= url('suporte') ?>" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-brand-blue hover:bg-gray-50">Suporte</a>
                <?php foreach ($menuPages as $mp): ?>
                    <a href="<?= url('pagina', ['slug' => $mp['slug']]) ?>" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-brand-blue hover:bg-gray-50"><?= e($mp['title']) ?></a>
                <?php endforeach; ?>
                <?php if (!empty($socialLinks)): ?>
                    <div class="flex flex-wrap gap-4 px-3 py-3">
                        <?php foreach ($socialLinks as $social): ?>
                            <?php if (($social['key'] ?? '') === 'whatsapp') continue; ?>
                            <a href="<?= e($social['url']) ?>" target="_blank" rel="noopener noreferrer" class="text-gray-600 hover:text-brand-blue" aria-label="<?= e($social['label']) ?>">
                                <i class="<?= e($social['icon']) ?> text-xl"></i>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <?php if (!empty(trim($clientAreaText ?? ''))): ?>
                    <a href="<?= e($clientAreaLink) ?>" target="_blank" class="block mt-4 px-3 py-2 text-center rounded-md text-base font-medium bg-brand-blue text-white hover:bg-blue-700">
                        <i class="fa-solid fa-user mr-2"></i> <?= e($clientAreaText) ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <main class="flex-grow">
