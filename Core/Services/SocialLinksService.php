<?php

namespace Core\Services;

use Models\Setting;

/**
 * Monta a lista de redes sociais preenchidas (SRP).
 * Campos vazios não entram no array — o layout só renderiza o que existir.
 */
class SocialLinksService
{
    /** @var array<string, array{label: string, icon: string, setting: string}> */
    private const NETWORKS = [
        'instagram' => [
            'label' => 'Instagram',
            'icon' => 'fa-brands fa-instagram',
            'setting' => 'social_instagram_url',
        ],
        'facebook' => [
            'label' => 'Facebook',
            'icon' => 'fa-brands fa-facebook',
            'setting' => 'social_facebook_url',
        ],
        'linkedin' => [
            'label' => 'LinkedIn',
            'icon' => 'fa-brands fa-linkedin',
            'setting' => 'social_linkedin_url',
        ],
        'youtube' => [
            'label' => 'YouTube',
            'icon' => 'fa-brands fa-youtube',
            'setting' => 'social_youtube_url',
        ],
        'x' => [
            'label' => 'X',
            'icon' => 'fa-brands fa-x-twitter',
            'setting' => 'social_x_url',
        ],
        'tiktok' => [
            'label' => 'TikTok',
            'icon' => 'fa-brands fa-tiktok',
            'setting' => 'social_tiktok_url',
        ],
    ];

    public function __construct(private ?Setting $settings = null)
    {
        $this->settings = $settings ?? new Setting();
    }

    /**
     * @return list<array{key: string, label: string, icon: string, url: string}>
     */
    public function links(): array
    {
        $items = [];
        foreach (self::NETWORKS as $key => $meta) {
            $url = trim($this->settings->get($meta['setting'], ''));
            if ($url === '') {
                continue;
            }
            $items[] = [
                'key' => $key,
                'label' => $meta['label'],
                'icon' => $meta['icon'],
                'url' => $url,
            ];
        }

        $whatsappUrl = $this->whatsappProfileUrl();
        if ($whatsappUrl !== '') {
            $items[] = [
                'key' => 'whatsapp',
                'label' => 'WhatsApp',
                'icon' => 'fa-brands fa-whatsapp',
                'url' => $whatsappUrl,
            ];
        }

        return $items;
    }

    public static function settingKeys(): array
    {
        $keys = [];
        foreach (self::NETWORKS as $meta) {
            $keys[] = $meta['setting'];
        }
        return $keys;
    }

    private function whatsappProfileUrl(): string
    {
        $explicit = trim($this->settings->get('social_whatsapp_url', ''));
        if ($explicit !== '') {
            return $explicit;
        }

        $number = preg_replace('/\D+/', '', $this->settings->get('whatsapp_number', '')) ?? '';
        if ($number === '') {
            return '';
        }

        return 'https://wa.me/' . $number;
    }
}
