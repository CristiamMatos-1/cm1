<?php

namespace Controllers;

use Core\Input;
use Core\Services\ImageUploadService;
use Core\Services\SocialLinksService;
use Core\Services\VideoUploadService;
use Core\Services\YouTubeService;
use Models\Setting;

class SettingsController extends BaseAdminController
{
    private Setting $settingModel;

    public function __construct()
    {
        parent::__construct();
        $this->settingModel = new Setting();
    }

    public function index(): void
    {
        $msg = $_SESSION['settings_msg'] ?? null;
        $error = $_SESSION['settings_error'] ?? null;
        unset($_SESSION['settings_msg'], $_SESSION['settings_error']);

        $this->render('admin/settings', [
            'title' => 'Configurações - Admin',
            'settings' => $this->settingModel->getAll(),
            'msg' => $msg,
            'error' => $error,
        ]);
    }

    public function store(): void
    {
        $this->assertCsrf();

        $socialKeys = array_merge(SocialLinksService::settingKeys(), ['social_whatsapp_url']);

        $allowedKeys = array_merge([
            'company_name', 'whatsapp_number', 'whatsapp_message',
            'youtube_api_key', 'youtube_channel_id', 'youtube_channel_url',
            'youtube_video_url', 'home_video_mode', 'meta_access_token',
            'meta_page_id', 'contact_email', 'contact_phone', 'hero_title',
            'hero_subtitle', 'hero_btn1_text', 'hero_btn1_link', 'hero_btn2_text',
            'hero_btn2_link', 'client_area_text', 'client_area_link',
            'articles_section_title', 'news_section_title', 'youtube_section_title',
            'youtube_section_subtitle', 'footer_about_text', 'facebook_page_id',
            'facebook_access_token', 'instagram_account_id', 'instagram_access_token',
            'gemini_api_key',
        ], $socialKeys);

        foreach ($allowedKeys as $key) {
            if (!array_key_exists($key, $_POST)) {
                continue;
            }
            $value = trim((string) $_POST[$key]);
            if (in_array($key, $socialKeys, true)) {
                $value = Input::href($value);
            }
            if ($key === 'company_name') {
                $value = Input::string($value);
                if ($value === '') {
                    $value = 'CONINFOMS';
                }
            }
            $this->settingModel->set($key, $value);
        }

        if (isset($_FILES['company_logo']) && $_FILES['company_logo']['error'] === UPLOAD_ERR_OK) {
            $logoPath = (new ImageUploadService())->uploadLogo($_FILES['company_logo']);
            if ($logoPath) {
                $this->settingModel->set('company_logo', $logoPath);
            }
        }

        if (isset($_POST['remove_logo']) && $_POST['remove_logo'] === '1') {
            $this->settingModel->set('company_logo', '');
        }

        $mode = trim((string) ($_POST['home_video_mode'] ?? ''));
        if (!in_array($mode, ['mp4', 'youtube_video', 'youtube_channel', 'none'], true)) {
            $this->settingModel->set('home_video_mode', '');
        }

        $channelUrl = trim((string) ($_POST['youtube_channel_url'] ?? ''));
        if ($channelUrl !== '') {
            $parsedChannel = YouTubeService::extractChannelId($channelUrl);
            if ($parsedChannel !== null) {
                $this->settingModel->set('youtube_channel_id', $parsedChannel);
            }
        } else {
            $this->settingModel->set('youtube_channel_id', '');
        }

        $videoUpload = new VideoUploadService();
        if (isset($_POST['remove_home_video']) && $_POST['remove_home_video'] === '1') {
            $videoUpload->delete($this->settingModel->get('home_video_file', ''));
            $this->settingModel->set('home_video_file', '');
        }

        if (isset($_FILES['home_video_file']) && $_FILES['home_video_file']['error'] !== UPLOAD_ERR_NO_FILE) {
            $path = $videoUpload->upload($_FILES['home_video_file']);
            if ($path) {
                $old = $this->settingModel->get('home_video_file', '');
                if ($old !== '' && $old !== $path) {
                    $videoUpload->delete($old);
                }
                $this->settingModel->set('home_video_file', $path);
                $this->settingModel->set('home_video_mode', 'mp4');
            }
        }

        if (empty($_SESSION['settings_error'])) {
            $_SESSION['settings_msg'] = 'Configurações atualizadas com sucesso!';
        }
        $this->redirect('admin/settings');
    }
}
