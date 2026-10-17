<?php

namespace Core\Services;

use Models\Setting;

class YouTubeService
{
    private string $apiKey;
    private string $channelId;

    public function __construct()
    {
        // Agora busca a chave e ID dinamicamente do banco de dados (configurações)
        $settingModel = new Setting();
        $this->apiKey = $settingModel->get('youtube_api_key', '');
        $this->channelId = $settingModel->get('youtube_channel_id', '');
    }

    /**
     * Busca o ID do último vídeo enviado pelo canal (Prioriza Lives)
     */
    public function getLatestVideoId(): ?string
    {
        $settingModel = new Setting();
        
        // Função auxiliar para buscar do cache
        $getCachedId = function() use ($settingModel) {
            $cached = $settingModel->get('youtube_last_video_id', '');
            return empty($cached) ? null : $cached;
        };

        // Se a API Key ou o Channel ID não foram configurados, aborta silenciosamente
        if (empty($this->apiKey) || empty($this->channelId)) {
            error_log("YouTube API: Falta apiKey ou channelId");
            return $getCachedId(); // Tenta retornar o último salvo
        }

        if (!function_exists('curl_init')) {
            error_log("YouTube API: Extensão cURL não está instalada ou ativada no PHP");
            return $getCachedId();
        }

        // TENTATIVA 1: Busca se há alguma Live acontecendo agora
        $liveUrl = "https://www.googleapis.com/youtube/v3/search?key={$this->apiKey}&channelId={$this->channelId}&part=snippet,id&type=video&eventType=live&maxResults=1";
        
        $ch = curl_init($liveUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $liveResponse = curl_exec($ch);
        $liveHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($liveHttpCode === 200 && $liveResponse) {
            $liveData = json_decode($liveResponse, true);
            if (!empty($liveData['items'][0]['id']['videoId'])) {
                $videoId = $liveData['items'][0]['id']['videoId'];
                $settingModel->set('youtube_last_video_id', $videoId); // Salva em cache
                return $videoId;
            }
        } else if ($liveHttpCode === 403) {
            // Cota excedida ou chave inválida
            error_log("YouTube API Quota Excedida ou Chave Inválida. Retornando cache.");
            return $getCachedId();
        }

        // TENTATIVA 2: Se não tem Live, busca o último vídeo normal enviado
        $url = "https://www.googleapis.com/youtube/v3/search?key={$this->apiKey}&channelId={$this->channelId}&part=snippet,id&order=date&maxResults=1&type=video";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200 && $response) {
            $data = json_decode($response, true);
            if (!empty($data['items'][0]['id']['videoId'])) {
                $videoId = $data['items'][0]['id']['videoId'];
                $settingModel->set('youtube_last_video_id', $videoId); // Salva em cache
                return $videoId;
            }
        } else {
            error_log("YouTube API Erro HTTP {$httpCode}: " . print_r($response, true));
        }

        // Se tudo falhar, tenta retornar o último vídeo que deu certo (Cache)
        return $getCachedId();
    }
}
