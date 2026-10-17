<?php

namespace Core\Services;

class SocialMediaService
{
    private string $metaAccessToken;
    private string $instagramAccountId;
    private string $facebookPageId;

    public function __construct()
    {
        // Em produção, deve vir do .env
        $this->metaAccessToken = 'COLOQUE_SEU_ACCESS_TOKEN_AQUI';
        $this->instagramAccountId = 'COLOQUE_SEU_IG_ACCOUNT_ID';
        $this->facebookPageId = 'COLOQUE_SEU_FB_PAGE_ID';
    }

    /**
     * Publica na Meta (Facebook e Instagram)
     * Obs: Este é um mock/esqueleto estrutural para o escopo do projeto.
     * Na prática, exige fluxo de OAuth, aprovação de app na Meta e tratamento de webhooks para imagens locais.
     */
    public function postToMeta(string $title, string $link, ?string $imageUrl = null): bool
    {
        if ($this->metaAccessToken === 'COLOQUE_SEU_ACCESS_TOKEN_AQUI') {
            // Log apenas para depuração, indicando que o serviço foi acionado
            error_log("SocialMediaService: Post não enviado. Credenciais da Meta não configuradas. Título: $title");
            return false;
        }

        $message = "Nova matéria no ar: {$title}\n\nLeia mais em: {$link}";

        // Simulação do disparo para Facebook
        $this->postToFacebook($message, $link);

        // Simulação do disparo para Instagram (Requer imagem hospedada publicamente)
        if ($imageUrl) {
            // Em dev local a Meta não acessa localhost, a imagem precisa ser pública
            // $this->postToInstagram($message, $imageUrl);
        }

        return true;
    }

    private function postToFacebook(string $message, string $link): void
    {
        $url = "https://graph.facebook.com/v18.0/{$this->facebookPageId}/feed";
        
        $data = [
            'message' => $message,
            'link' => $link,
            'access_token' => $this->metaAccessToken
        ];

        // Chamada cURL omitida para brevidade (estrutura idêntica ao do Gemini)
    }

    private function postToInstagram(string $caption, string $imageUrl): void
    {
        // 1. Criar o contêiner de mídia
        // 2. Publicar o contêiner
    }
}
