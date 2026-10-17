<?php

namespace Controllers;

use Core\Controller;
use Models\Setting;

class SettingsController extends Controller
{
    private Setting $settingModel;

    public function __construct()
    {
        // Protege o controller: só acessa se estiver logado
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . url('login'));
            exit;
        }
        
        $this->settingModel = new Setting();
    }

    /**
     * Exibe o formulário de configurações
     */
    public function index(): void
    {
        // Busca todas as configurações atuais
        $settings = $this->settingModel->getAll();
        
        $msg = $_SESSION['settings_msg'] ?? null;
        unset($_SESSION['settings_msg']);

        $this->render('admin/settings', [
            'title' => 'Configurações - Admin',
            'settings' => $settings,
            'msg' => $msg
        ]);
    }

    /**
     * Salva as configurações submetidas
     */
    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Arrays com as chaves permitidas
            $allowedKeys = [
                'company_name',
                'whatsapp_number', 
                'whatsapp_message', 
                'youtube_api_key', 
                'youtube_channel_id',
                'meta_access_token',
                'meta_page_id',
                'hero_title',
                'hero_subtitle',
                'hero_btn1_text',
                'hero_btn1_link',
                'hero_btn2_text',
                'hero_btn2_link',
                'client_area_text',
                'client_area_link',
                'articles_section_title',
                'news_section_title',
                'youtube_section_title',
                'youtube_section_subtitle',
                'footer_about_text',
                'facebook_page_id',
                'facebook_access_token',
                'instagram_account_id',
                'instagram_access_token'
            ];

            foreach ($allowedKeys as $key) {
                // Removemos o htmlspecialchars aqui para permitir que aspas simples e aspas duplas sejam salvas normalmente.
                // O htmlspecialchars deve ser usado apenas na hora de EXIBIR (nas Views).
                // Verificamos com array_key_exists porque o campo pode ser enviado vazio ('') e precisamos salvar esse vazio no banco.
                if (array_key_exists($key, $_POST)) {
                    $value = trim($_POST[$key]);
                    $this->settingModel->set($key, $value);
                }
            }

            // Tratamento do Logo
            if (isset($_FILES['company_logo']) && $_FILES['company_logo']['error'] === UPLOAD_ERR_OK) {
                $uploadService = new \Core\Services\ImageUploadService();
                $logoPath = $uploadService->uploadLogo($_FILES['company_logo']);
                
                if ($logoPath) {
                    $this->settingModel->set('company_logo', $logoPath);
                }
            }

            // Excluir Logo
            if (isset($_POST['remove_logo']) && $_POST['remove_logo'] == '1') {
                $this->settingModel->set('company_logo', '');
            }

            $_SESSION['settings_msg'] = 'Configurações atualizadas com sucesso!';
        }

        header('Location: ' . url('admin/settings'));
        exit;
    }
}
