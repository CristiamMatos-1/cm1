<?php
// Busca as configurações globais de logo e nome
$globalSetting = new \Models\Setting();
$globalCompanyName = \Config\App::fixBrand($globalSetting->get('company_name', \Config\App::BRAND_NAME));
$globalCompanyLogo = $globalSetting->get('company_logo', '');

// Busca as páginas dinâmicas para o menu
$pageModel = new \Models\Page();
$menuPages = array_values(array_filter(
    $pageModel->getPublished(),
    fn ($mp) => strtolower($mp['slug'] ?? '') !== 'contato' // "Contato" é uma rota própria (WhatsApp)
));

$clientAreaText = $globalSetting->get('client_area_text', 'Área do Cliente');
$clientAreaLink = $globalSetting->get('client_area_link', \Config\App::SITE_URL . '/cm/');
$canonicalUrl = \Config\App::SITE_URL . (parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/')
    . (!empty($_GET['slug']) ? '?slug=' . rawurlencode($_GET['slug']) : '');
$documentTitle = $title ?? $globalCompanyName;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($documentTitle) ?></title>
    <link rel="canonical" href="<?= htmlspecialchars($canonicalUrl) ?>">
    <meta property="og:site_name" content="<?= e($globalCompanyName) ?>">
    <meta property="og:title" content="<?= e($documentTitle) ?>">
    <meta property="og:url" content="<?= htmlspecialchars($canonicalUrl) ?>">
    <meta name="theme-color" content="#182C48">
    <?php require __DIR__ . '/brand-head.php'; ?>
</head>
<body class="bg-brand-light text-brand-slate font-sans flex flex-col min-h-screen">

    <!-- Navbar -->
    <header class="bg-white shadow-md sticky top-0 z-50 border-b-2 border-brand-accent">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center">
                    <a href="<?= htmlspecialchars(url()) ?>" class="flex items-center gap-2">
                        <?php if(!empty($globalCompanyLogo)): ?>
                            <img src="<?= htmlspecialchars(asset($globalCompanyLogo)) ?>" alt="<?= e($globalCompanyName) ?>" class="h-10 w-auto object-contain">
                        <?php else: ?>
                            <div class="w-10 h-10 bg-brand-accent rounded-lg flex items-center justify-center text-white font-bold text-xl">
                                <?= e(mb_substr($globalCompanyName, 0, 1)) ?>
                            </div>
                        <?php endif; ?>
                        <span class="font-bold text-2xl text-brand-dark tracking-tight"><?= e($globalCompanyName) ?></span>
                    </a>
                </div>

                <!-- Desktop Menu -->
                <nav class="hidden md:flex space-x-8 items-center">
                    <a href="<?= htmlspecialchars(url()) ?>" class="text-brand-slate hover:text-brand-accent font-medium transition-colors">Início</a>
                    <a href="<?= htmlspecialchars(url('materias')) ?>" class="text-brand-slate hover:text-brand-accent font-medium transition-colors">Matérias</a>
                    <?php foreach($menuPages as $mp): ?>
                        <a href="<?= htmlspecialchars(url('pagina?slug=' . rawurlencode($mp['slug']))) ?>" class="text-brand-slate hover:text-brand-accent font-medium transition-colors"><?= e($mp['title']) ?></a>
                    <?php endforeach; ?>
                    <a href="<?= htmlspecialchars(url('contato')) ?>" class="text-brand-slate hover:text-brand-accent font-medium transition-colors">Contato</a>
                    <!-- Área do Cliente (Link Externo) -->
                    <?php if (!empty(trim($clientAreaText ?? ''))): ?>
                        <a href="<?= htmlspecialchars(resolve_link($clientAreaLink)) ?>" target="_blank" rel="noopener" class="bg-brand-accent text-white px-5 py-2.5 rounded-md font-medium hover:bg-blue-700 transition-colors shadow-sm flex items-center gap-2">
                            <i class="fa-solid fa-user"></i> <?= e($clientAreaText) ?>
                        </a>
                    <?php endif; ?>
                </nav>

                <!-- Mobile Menu Button -->
                <div class="md:hidden flex items-center">
                    <button id="mobile-menu-btn" class="text-brand-slate hover:text-brand-accent focus:outline-none p-2">
                        <i class="fa-solid fa-bars text-2xl"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu (Escondido por padrão) -->
        <div id="mobile-menu" class="hidden md:hidden bg-white border-t">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3 shadow-inner">
                <a href="<?= htmlspecialchars(url()) ?>" class="block px-3 py-2 rounded-md text-base font-medium text-brand-slate hover:text-brand-accent hover:bg-brand-light">Início</a>
                <a href="<?= htmlspecialchars(url('materias')) ?>" class="block px-3 py-2 rounded-md text-base font-medium text-brand-slate hover:text-brand-accent hover:bg-brand-light">Matérias</a>
                <?php foreach($menuPages as $mp): ?>
                    <a href="<?= htmlspecialchars(url('pagina?slug=' . rawurlencode($mp['slug']))) ?>" class="block px-3 py-2 rounded-md text-base font-medium text-brand-slate hover:text-brand-accent hover:bg-brand-light"><?= e($mp['title']) ?></a>
                <?php endforeach; ?>
                <a href="<?= htmlspecialchars(url('contato')) ?>" class="block px-3 py-2 rounded-md text-base font-medium text-brand-slate hover:text-brand-accent hover:bg-brand-light">Contato</a>
                
                <?php if (!empty(trim($clientAreaText ?? ''))): ?>
                    <a href="<?= htmlspecialchars(resolve_link($clientAreaLink)) ?>" target="_blank" rel="noopener" class="block mt-4 px-3 py-2 text-center rounded-md text-base font-medium bg-brand-accent text-white hover:bg-blue-700">
                        <i class="fa-solid fa-user mr-2"></i> <?= e($clientAreaText) ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow">
