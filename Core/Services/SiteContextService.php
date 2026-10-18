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
            $number = preg_replace('/\D+/', '', $settings->get('whatsapp_number', '')) ?? '';
            $message = $settings->get('whatsapp_message', 'Olá! Gostaria de mais informações.');
            $whatsappUrl = $number !== ''
                ? 'https://wa.me/' . $number . '?text=' . urlencode($message)
                : '';

            return [
                'whatsappUrl' => $whatsappUrl,
                'contactEmail' => $settings->get('contact_email', 'contato@coninfoms.com.br'),
                'contactPhone' => $settings->get('contact_phone', '(00) 0000-0000'),
                'socialLinks' => (new SocialLinksService($settings))->links(),
            ];
        } catch (Throwable $e) {
            return [
                'whatsappUrl' => '',
                'contactEmail' => 'contato@coninfoms.com.br',
                'contactPhone' => '(00) 0000-0000',
                'socialLinks' => [],
            ];
        }
    }
}
