<?php

namespace Core\Services;

use Config\App;
use Models\Setting;

class GeminiAIService
{
    private string $apiKey;
    private string $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent';

    public function __construct(?Setting $settings = null)
    {
        $settings = $settings ?? new Setting();
        $fromSettings = $settings->get('gemini_api_key', '');
        $fromEnv = (string) App::env('GEMINI_API_KEY', '');
        $this->apiKey = $fromSettings !== '' ? $fromSettings : $fromEnv;
    }

    public function generateArticleContent(string $topic): ?string
    {
        if ($this->apiKey === '') {
            return '<p><em>Erro: Chave de API do Gemini não configurada (Configurações ou GEMINI_API_KEY).</em></p>'
                . '<p>Tema solicitado: <strong>' . htmlspecialchars($topic, ENT_QUOTES, 'UTF-8') . '</strong></p>';
        }

        $prompt = "Aja como um redator especialista em tecnologia. Escreva um artigo completo, profissional e engajador sobre o tema: '{$topic}'. "
            . "Retorne o texto formatado exclusivamente em HTML (use <h2>, <h3>, <p>, <ul>, <li>, <strong>, etc.). "
            . "Não inclua a tag <h1> nem as tags <html> ou <body>. Apenas o conteúdo interno. "
            . "Não adicione blocos de markdown ```html antes ou depois do texto.";

        $data = [
            'contents' => [
                ['parts' => [['text' => $prompt]]],
            ],
            'generationConfig' => [
                'temperature' => 0.7,
                'maxOutputTokens' => 2048,
            ],
        ];

        $ch = curl_init($this->apiUrl . '?key=' . urlencode($this->apiKey));
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_TIMEOUT => 30,
        ]);

        $response = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200 && is_string($response)) {
            $result = json_decode($response, true);
            return $result['candidates'][0]['content']['parts'][0]['text'] ?? null;
        }

        return null;
    }
}
