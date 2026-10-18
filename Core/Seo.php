<?php

namespace Core;

/**
 * Meta tags SEO para entidades (artigos, páginas).
 */
class Seo
{
    /**
     * @return array{meta_title: string, meta_description: string, meta_keywords: string}
     */
    public static function fromPost(): array
    {
        return [
            'meta_title' => mb_substr(Input::string($_POST['meta_title'] ?? ''), 0, 255),
            'meta_description' => mb_substr(Input::string($_POST['meta_description'] ?? ''), 0, 320),
            'meta_keywords' => mb_substr(Input::string($_POST['meta_keywords'] ?? ''), 0, 500),
        ];
    }

    /**
     * Resolve title/description/keywords com fallbacks.
     *
     * @param array<string, mixed>|null $entity
     * @return array{title: string, description: string, keywords: string}
     */
    public static function resolve(?array $entity, string $fallbackTitle, string $fallbackDescription = ''): array
    {
        $title = trim((string) ($entity['meta_title'] ?? ''));
        $description = trim((string) ($entity['meta_description'] ?? ''));
        $keywords = trim((string) ($entity['meta_keywords'] ?? ''));

        if ($title === '') {
            $title = $fallbackTitle;
        }
        if ($description === '') {
            $description = $fallbackDescription;
        }

        return [
            'title' => $title,
            'description' => $description,
            'keywords' => $keywords,
        ];
    }
}
