<?php

namespace Controllers;

use Config\App;
use Core\Controller;
use Core\Input;
use Core\Services\FileUploadService;
use Core\Services\SiteContextService;
use Core\SupportPageCopy;
use Models\Download;
use Models\Setting;
use Throwable;

class SupportController extends Controller
{
    private SiteContextService $siteContext;

    public function __construct()
    {
        $this->siteContext = new SiteContextService();
    }

    public function index(): void
    {
        $files = (new Download())->getPublished();
        $copy = SupportPageCopy::fromMap([]);
        $company = 'CONINFOMS';
        try {
            $settings = new Setting();
            $copy = SupportPageCopy::fromMap($settings->getAll());
            $companyName = trim($settings->get('company_name', $company));
            if ($companyName !== '') {
                $company = $companyName;
            }
        } catch (Throwable $e) {
            error_log('Página de downloads: ' . $e->getMessage());
        }

        $description = $copy['text'] !== ''
            ? $copy['text']
            : 'Arquivos disponíveis para download.';

        $this->render('home/suporte', array_merge($this->siteContext->layoutData(), [
            'title' => $copy['title'],
            'seo' => [
                'title' => $copy['title'] . ' - ' . $company,
                'description' => $description,
                'keywords' => 'suporte, download, remoto',
            ],
            'canonicalUrl' => App::absoluteUrl('suporte'),
            'downloads' => $files,
            'supportPageTitle' => $copy['title'],
            'supportPageText' => $copy['text'],
            'supportLinkLabel' => $copy['link'],
        ]));
    }

    public function download(): void
    {
        $id = Input::int($_GET['id'] ?? null);
        if (!$id) {
            http_response_code(404);
            $this->render('errors/404', ['requested' => '/download']);
            return;
        }

        $item = (new Download())->findPublishedById($id);
        if (!$item) {
            http_response_code(404);
            $this->render('errors/404', ['requested' => '/download']);
            return;
        }

        $path = (new FileUploadService())->absolutePath($item['stored_name'] ?? null);
        if ($path === null || !is_readable($path)) {
            http_response_code(404);
            $this->render('errors/404', ['requested' => '/download']);
            return;
        }

        $downloadName = $item['original_name'] !== '' ? $item['original_name'] : ($item['display_name'] . '.bin');
        $downloadName = str_replace(["\r", "\n", '"'], '', $downloadName);

        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        header('Content-Type: application/octet-stream');
        header('X-Content-Type-Options: nosniff');
        header('Content-Length: ' . (string) filesize($path));
        header('Content-Disposition: attachment; filename="' . $downloadName . '"; filename*=UTF-8\'\'' . rawurlencode($downloadName));
        header('Cache-Control: private, no-store');
        readfile($path);
        exit;
    }
}
