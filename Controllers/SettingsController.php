<?php

namespace Controllers;

use Core\Services\ImageUploadService;
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
        unset($_SESSION['settings_msg']);

        $this->render('admin/settings', [
            'title' => 'Configurações - Admin',
            'settings' => $this->settingModel->getAll(),
            'msg' => $msg,
        ]);
    }

    public function store(): void
    {
        $this->assertCsrf();

        $allowedKeys = [
            'company_name', 'whatsapp_number', 'whatsapp_message',
            'youtube_api_key', 'youtube_channel_id', 'meta_access_token',
            'meta_page_id', 'contact_email', 'contact_phone', 'hero_title',
            'hero_subtitle', 'hero_btn1_text', 'hero_btn1_link', 'hero_btn2_text',
            'hero_btn2_link', 'client_area_text', 'client_area_link',
            'articles_section_title', 'news_section_title', 'youtube_section_title',
            'youtube_section_subtitle', 'footer_about_text', 'facebook_page_id',
            'facebook_access_token', 'instagram_account_id', 'instagram_access_token',
            'gemini_api_key',
        ];

        foreach ($allowedKeys as $key) {
            if (array_key_exists($key, $_POST)) {
                $this->settingModel->set($key, trim((string) $_POST[$key]));
            }
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

        $_SESSION['settings_msg'] = 'Configurações atualizadas com sucesso!';
        $this->redirect('admin/settings');
    }
}
