<?php

namespace Core\Services;

class GeminiAIService
{
    private string $apiKey;
    private string $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent';

    public function __construct()
    {
        // Em produção, isso deve vir de variáveis de ambiente (.env) ou banco de dados
        $this->apiKey = 'COLOQUE_SUA_API_KEY_AQUI'; 
    }

    /**
     * Envia um prompt para o Gemini e retorna o texto gerado (formatado em HTML)
     */
    public function generateArticleContent(string $topic): ?string
    {
        if ($this->apiKey === 'COLOQUE_SUA_API_KEY_AQUI') {
            return "<p><em>Erro: Chave de API do Gemini não configurada no sistema.</em></p><p>Tema solicitado: <strong>" . htmlspecialchars($topic) . "</strong></p>";
        }

        $prompt = "Aja como um redator especialista em tecnologia. Escreva um artigo completo, profissional e engajador sobre o tema: '{$topic}'. "
                . "Retorne o texto formatado exclusivamente em HTML (use <h2>, <h3>, <p>, <ul>, <li>, <strong>, etc.). "
                . "Não inclua a tag <h1> nem as tags <html> ou <body>. Apenas o conteúdo interno. "
                . "Não adicione blocos de markdown ```html antes ou depois do texto.";

        $data = [
            "contents" => [
                [
                    "parts" => [
                        ["text" => $prompt]
                    ]
                ]
            ],
            "generationConfig" => [
                "temperature" => 0.7,
                "maxOutputTokens" => 2048,
            ]
        ];

        $ch = curl_init("{$this->apiUrl}?key={$this->apiKey}");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200 && $response) {
            $result = json_decode($response, true);
            if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                return $result['candidates'][0]['content']['parts'][0]['text'];
            }
        }

        return null;
    }
}
