<?php
// Busca as configurações globais de logo e nome
$globalSetting = new \Models\Setting();
$globalCompanyName = $globalSetting->get('company_name', 'CONINFOMS');
$globalCompanyLogo = $globalSetting->get('company_logo', '');

// Busca as páginas dinâmicas para o menu
$pageModel = new \Models\Page();
$menuPages = $pageModel->getPublished();

$clientAreaText = $globalSetting->get('client_area_text', 'Área do Cliente');
$clientAreaLink = $globalSetting->get('client_area_link', 'https://coninfoms.com.br/cm/');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? $globalCompanyName) ?></title>
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Configuração de cores personalizadas do Tailwind -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            blue: '#004aad', // Cor principal (ajustável)
                            dark: '#1e293b',
                            light: '#f8fafc'
                        }
                    }
                }
            }
        }
    </script>
    <!-- FontAwesome (Ícones) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 text-gray-800 font-sans flex flex-col min-h-screen">

    <!-- Navbar -->
    <header class="bg-white shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center">
                    <a href="/cm1/" class="flex items-center gap-2">
                        <?php if(!empty($globalCompanyLogo)): ?>
                            <img src="/cm1/Public<?= htmlspecialchars($globalCompanyLogo) ?>" alt="<?= htmlspecialchars($globalCompanyName) ?>" class="h-10 w-auto object-contain">
                        <?php else: ?>
                            <div class="w-10 h-10 bg-brand-blue rounded-lg flex items-center justify-center text-white font-bold text-xl">
                                <?= substr($globalCompanyName, 0, 1) ?>
                            </div>
                        <?php endif; ?>
                        <span class="font-bold text-2xl text-brand-dark tracking-tight"><?= htmlspecialchars($globalCompanyName) ?></span>
                    </a>
                </div>

                <!-- Desktop Menu -->
                <nav class="hidden md:flex space-x-8 items-center">
                    <a href="/cm1/" class="text-gray-600 hover:text-brand-blue font-medium transition-colors">Início</a>
                    <a href="/cm1/materias" class="text-gray-600 hover:text-brand-blue font-medium transition-colors">Matérias</a>
                    <?php foreach($menuPages as $mp): ?>
                        <a href="/cm1/pagina?slug=<?= htmlspecialchars($mp['slug']) ?>" class="text-gray-600 hover:text-brand-blue font-medium transition-colors"><?= htmlspecialchars($mp['title']) ?></a>
                    <?php endforeach; ?>
                    <!-- Área do Cliente (Link Externo) -->
                    <?php if (!empty(trim($clientAreaText ?? ''))): ?>
                        <a href="<?= htmlspecialchars($clientAreaLink) ?>" target="_blank" class="bg-brand-blue text-white px-5 py-2.5 rounded-md font-medium hover:bg-blue-700 transition-colors shadow-sm flex items-center gap-2">
                            <i class="fa-solid fa-user"></i> <?= htmlspecialchars($clientAreaText) ?>
                        </a>
                    <?php endif; ?>
                </nav>

                <!-- Mobile Menu Button -->
                <div class="md:hidden flex items-center">
                    <button id="mobile-menu-btn" class="text-gray-600 hover:text-brand-blue focus:outline-none p-2">
                        <i class="fa-solid fa-bars text-2xl"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu (Escondido por padrão) -->
        <div id="mobile-menu" class="hidden md:hidden bg-white border-t">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3 shadow-inner">
                <a href="/cm1/" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-brand-blue hover:bg-gray-50">Início</a>
                <a href="/cm1/materias" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-brand-blue hover:bg-gray-50">Matérias</a>
                <?php foreach($menuPages as $mp): ?>
                    <a href="/cm1/pagina?slug=<?= htmlspecialchars($mp['slug']) ?>" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-brand-blue hover:bg-gray-50"><?= htmlspecialchars($mp['title']) ?></a>
                <?php endforeach; ?>
                
                <?php if (!empty(trim($clientAreaText ?? ''))): ?>
                    <a href="<?= htmlspecialchars($clientAreaLink) ?>" target="_blank" class="block mt-4 px-3 py-2 text-center rounded-md text-base font-medium bg-brand-blue text-white hover:bg-blue-700">
                        <i class="fa-solid fa-user mr-2"></i> <?= htmlspecialchars($clientAreaText) ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow">
