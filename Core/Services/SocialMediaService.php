<?php

namespace Core\Services;

use Models\Setting;

class SocialMediaService
{
    private string $metaAccessToken;
    private string $instagramAccountId;
    private string $facebookPageId;

    public function __construct(?Setting $settings = null)
    {
        $settings = $settings ?? new Setting();
        $this->metaAccessToken = $settings->get('facebook_access_token', $settings->get('meta_access_token', ''));
        $this->instagramAccountId = $settings->get('instagram_account_id', '');
        $this->facebookPageId = $settings->get('facebook_page_id', $settings->get('meta_page_id', ''));
    }

    public function postToMeta(string $title, string $link, ?string $imageUrl = null): bool
    {
        if ($this->metaAccessToken === '' || $this->facebookPageId === '') {
            error_log("SocialMediaService: credenciais da Meta não configuradas. Título: {$title}");
            return false;
        }

        $message = "Nova matéria no ar: {$title}\n\nLeia mais em: {$link}";
        $this->postToFacebook($message, $link);

        if ($imageUrl && $this->instagramAccountId !== '') {
            $this->postToInstagram($message, $imageUrl);
        }

        return true;
    }

    private function postToFacebook(string $message, string $link): void
    {
        $url = "https://graph.facebook.com/v18.0/{$this->facebookPageId}/feed";
        $payload = http_build_query([
            'message' => $message,
            'link' => $link,
            'access_token' => $this->metaAccessToken,
        ]);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_TIMEOUT => 10,
        ]);
        curl_exec($ch);
        curl_close($ch);
    }

    private function postToInstagram(string $caption, string $imageUrl): void
    {
        error_log('SocialMediaService: Instagram requer URL pública da imagem. Caption omitido no log.');
        unset($caption, $imageUrl);
    }
}
