<?php

namespace Core\Services;

use Models\Setting;
use Throwable;

/**
 * Agrega dados de contato/layout usados em todas as páginas públicas (DRY).
 */
class SiteContextService
{
    public function layoutData(): array
    {
        try {
            $settings = new Setting();
            $number = $settings->get('whatsapp_number', '556799999999');
            $message = $settings->get('whatsapp_message', 'Olá! Gostaria de mais informações sobre os serviços da CONINFOMS.');

            return [
                'whatsappUrl' => 'https://wa.me/' . preg_replace('/\D+/', '', $number) . '?text=' . urlencode($message),
                'contactEmail' => $settings->get('contact_email', 'contato@coninfoms.com.br'),
                'contactPhone' => $settings->get('contact_phone', '(00) 0000-0000'),
            ];
        } catch (Throwable $e) {
            return [
                'whatsappUrl' => 'https://wa.me/556799999999',
                'contactEmail' => 'contato@coninfoms.com.br',
                'contactPhone' => '(00) 0000-0000',
            ];
        }
    }
}
