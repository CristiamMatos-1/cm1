<?php
use Models\Setting;

$currentNav = $currentNav ?? '';
$pageHeading = $pageHeading ?? ($title ?? 'Admin');
$user_name = $user_name ?? ($_SESSION['user_name'] ?? '');
$useTinymce = $useTinymce ?? false;
$adminCompanyName = 'Admin';
$adminCompanyLogo = '';
try {
    $adminSettings = new Setting();
    $adminCompanyName = $adminSettings->get('company_name', 'Admin');
    $adminCompanyLogo = $adminSettings->get('company_logo', '');
    if ($adminCompanyName === '') {
        $adminCompanyName = 'Admin';
    }
} catch (Throwable $e) {
    $adminCompanyName = 'Admin';
}
$navClass = static function (string $key, string $current): string {
    return $key === $current
        ? 'flex items-center gap-3 bg-gray-700 text-white px-4 py-3 rounded-lg'
        : 'flex items-center gap-3 text-gray-300 hover:bg-gray-700 hover:text-white px-4 py-3 rounded-lg transition';
};
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? ($adminCompanyName . ' - Admin')) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { brand: { blue: '#004aad', dark: '#1e293b' } }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <?php if ($useTinymce): ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
      tinymce.init({
        selector: '#content-editor',
        plugins: 'advlist autolink lists link image charmap preview anchor pagebreak',
        toolbar: 'undo redo | styles | bold italic underline | alignleft aligncenter alignright | bullist numlist | link image | removeformat',
        toolbar_mode: 'floating',
        height: 500,
        menubar: false,
        branding: false,
        // UTF-8 nativo: evita &ccedil; / &atilde; no HTML salvo
        entity_encoding: 'raw',
        encoding: 'html',
        convert_urls: false,
        verify_html: true,
        content_style: 'body { font-family: system-ui, sans-serif; font-size: 16px; }'
      });
    </script>
    <?php endif; ?>
</head>
<body class="bg-gray-100 flex h-screen overflow-hidden">

    <aside id="sidebar" class="bg-gray-800 text-white w-64 flex-shrink-0 absolute inset-y-0 left-0 transform -translate-x-full md:relative md:translate-x-0 transition duration-200 ease-in-out z-20 flex flex-col">
        <div class="h-16 flex items-center px-4 bg-gray-900 justify-between gap-2">
            <a href="<?= url('admin') ?>" class="flex items-center gap-2 min-w-0 font-bold text-lg tracking-wide text-white">
                <?php if (!empty($adminCompanyLogo)): ?>
                    <img src="<?= asset($adminCompanyLogo) ?>" alt="<?= e($adminCompanyName) ?>" class="h-8 w-auto max-w-[140px] object-contain bg-white/10 rounded px-1">
                <?php else: ?>
                    <span class="truncate"><?= e($adminCompanyName) ?></span>
                <?php endif; ?>
            </a>
            <button id="close-sidebar" class="md:hidden text-gray-300 hover:text-white flex-shrink-0" aria-label="Fechar menu">
                <i class="fa-solid fa-times text-xl"></i>
            </button>
        </div>
        <nav class="flex-1 px-4 py-6 space-y-2">
            <a href="<?= url('admin') ?>" class="<?= $navClass('dashboard', $currentNav) ?>">
                <i class="fa-solid fa-chart-line"></i> Dashboard
            </a>
            <a href="<?= url('admin/articles') ?>" class="<?= $navClass('articles', $currentNav) ?>">
                <i class="fa-solid fa-newspaper"></i> Artigos / Matérias
            </a>
            <a href="<?= url('admin/pages') ?>" class="<?= $navClass('pages', $currentNav) ?>">
                <i class="fa-solid fa-file-alt"></i> Páginas Institucionais
            </a>
            <a href="<?= url('admin/downloads') ?>" class="<?= $navClass('downloads', $currentNav) ?>">
                <i class="fa-solid fa-headset"></i> Suporte e Downloads
            </a>
            <a href="<?= url('admin/banners') ?>" class="<?= $navClass('banners', $currentNav) ?>">
                <i class="fa-solid fa-images"></i> Banners
            </a>
            <a href="<?= url('admin/announcements') ?>" class="<?= $navClass('announcements', $currentNav) ?>">
                <i class="fa-solid fa-bullhorn"></i> Avisos
            </a>
            <a href="<?= url('admin/users') ?>" class="<?= $navClass('users', $currentNav) ?>">
                <i class="fa-solid fa-users"></i> Usuários
            </a>
            <a href="<?= url('admin/audit') ?>" class="<?= $navClass('audit', $currentNav) ?>">
                <i class="fa-solid fa-clipboard-list"></i> Auditoria
            </a>
            <a href="<?= url('admin/settings') ?>" class="<?= $navClass('settings', $currentNav) ?>">
                <i class="fa-solid fa-cog"></i> Configurações
            </a>
        </nav>
        <div class="p-4 bg-gray-900 text-sm">
            Logado como:<br>
            <span class="font-bold text-blue-400"><?= e($user_name) ?></span>
        </div>
    </aside>

    <div class="flex-1 flex flex-col h-screen overflow-hidden">
        <header class="h-16 bg-white shadow-sm flex items-center justify-between px-6 z-10">
            <div class="flex items-center gap-4 min-w-0">
                <button id="open-sidebar" class="md:hidden text-gray-600 hover:text-gray-900" aria-label="Abrir menu">
                    <i class="fa-solid fa-bars text-xl"></i>
                </button>
                <?php if (!empty($backUrl)): ?>
                    <a href="<?= e($backUrl) ?>" class="text-gray-500 hover:text-gray-800"><i class="fa-solid fa-arrow-left"></i> Voltar</a>
                <?php endif; ?>
                <h2 class="text-xl font-semibold text-gray-800 truncate"><?= e($pageHeading) ?></h2>
            </div>
            <div class="flex items-center gap-4">
                <a href="<?= url() ?>" target="_blank" class="text-sm text-gray-500 hover:text-blue-600 flex items-center gap-1">
                    <i class="fa-solid fa-external-link-alt"></i> Ver Site
                </a>
                <a href="<?= url('logout') ?>" class="bg-red-50 text-red-600 hover:bg-red-100 px-4 py-2 rounded-md text-sm font-medium transition flex items-center gap-2">
                    <i class="fa-solid fa-sign-out-alt"></i> Sair
                </a>
            </div>
        </header>
        <main class="flex-1 overflow-y-auto p-6 bg-gray-50">
