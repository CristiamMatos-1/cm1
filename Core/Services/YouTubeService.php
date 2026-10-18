<?php

namespace Core\Services;

use Models\Setting;
use Throwable;

class YouTubeService
{
    private string $apiKey;
    private string $channelId;
    private Setting $settings;

    public function __construct(?Setting $settings = null)
    {
        $this->settings = $settings ?? new Setting();
        $this->apiKey = $this->settings->get('youtube_api_key', '');
        $this->channelId = self::extractChannelId(
            $this->settings->get('youtube_channel_url', '') ?: $this->settings->get('youtube_channel_id', '')
        ) ?? '';
    }

    public static function extractVideoId(string $input): ?string
    {
        $input = trim($input);
        if ($input === '') {
            return null;
        }
        if (preg_match('/^[A-Za-z0-9_-]{11}$/', $input) === 1) {
            return $input;
        }

        $query = [];
        parse_str((string) parse_url($input, PHP_URL_QUERY), $query);
        if (isset($query['v']) && preg_match('/^[A-Za-z0-9_-]{11}$/', (string) $query['v']) === 1) {
            return (string) $query['v'];
        }

        if (preg_match('~(?:youtu\.be/|youtube(?:-nocookie)?\.com/(?:embed|shorts|live|v)/)([A-Za-z0-9_-]{11})~i', $input, $matches) === 1) {
            return $matches[1];
        }

        return null;
    }

    public static function extractChannelId(string $input): ?string
    {
        $input = trim($input);
        if ($input === '') {
            return null;
        }
        if (preg_match('/^UC[A-Za-z0-9_-]{20,}$/', $input) === 1) {
            return $input;
        }
        if (preg_match('~youtube\.com/channel/(UC[A-Za-z0-9_-]{20,})~i', $input, $matches) === 1) {
            return $matches[1];
        }
        return null;
    }

    public static function normalizeChannelUrl(string $input): string
    {
        $input = trim($input);
        if ($input === '') {
            return '';
        }
        if (preg_match('~^https?://~i', $input) === 1) {
            return $input;
        }
        if (str_starts_with($input, '@')) {
            return 'https://www.youtube.com/' . $input;
        }
        if (preg_match('/^UC[A-Za-z0-9_-]{20,}$/', $input) === 1) {
            return 'https://www.youtube.com/channel/' . $input;
        }
        return 'https://www.youtube.com/' . ltrim($input, '/');
    }

    public function getLatestVideoId(): ?string
    {
        $cached = $this->cachedId();
        $cachedAt = (int) $this->settings->get('youtube_last_video_at', '0');
        if ($cached && (time() - $cachedAt) < 21600) {
            return $cached;
        }

        if ($this->apiKey === '' || $this->channelId === '') {
            return $cached;
        }

        if (!function_exists('curl_init')) {
            error_log('YouTube API: cURL indisponível');
            return $cached;
        }

        $liveId = $this->searchVideo('eventType=live');
        if ($liveId) {
            $this->storeCache($liveId);
            return $liveId;
        }

        $latestId = $this->searchVideo('order=date');
        if ($latestId) {
            $this->storeCache($latestId);
            return $latestId;
        }

        return $cached;
    }

    private function searchVideo(string $extraQuery): ?string
    {
        $url = "https://www.googleapis.com/youtube/v3/search?key=" . urlencode($this->apiKey)
            . "&channelId=" . urlencode($this->channelId)
            . "&part=snippet,id&type=video&maxResults=1&{$extraQuery}";

        try {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 5,
                CURLOPT_CONNECTTIMEOUT => 3,
            ]);
            $response = curl_exec($ch);
            $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 403) {
                error_log('YouTube API: cota excedida ou chave inválida.');
                return null;
            }

            if ($httpCode !== 200 || !is_string($response)) {
                return null;
            }

            $data = json_decode($response, true);
            return $data['items'][0]['id']['videoId'] ?? null;
        } catch (Throwable $e) {
            error_log('YouTube API: ' . $e->getMessage());
            return null;
        }
    }

    private function cachedId(): ?string
    {
        $cached = $this->settings->get('youtube_last_video_id', '');
        return $cached === '' ? null : $cached;
    }

    private function storeCache(string $videoId): void
    {
        $this->settings->set('youtube_last_video_id', $videoId);
        $this->settings->set('youtube_last_video_at', (string) time());
    }
}
