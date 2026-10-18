<?php

namespace Core\Services;

use Models\Setting;

/**
 * Decide o player da home (MP4, vídeo ou canal) e o que pode aparecer.
 * Campo vazio = oculto na view.
 */
class HomeVideoService
{
    public function __construct(private ?Setting $settings = null)
    {
        $this->settings = $settings ?? new Setting();
    }

    /**
     * @return array{
     *   visible: bool,
     *   title: string,
     *   subtitle: string,
     *   player: string,
     *   mp4Url: string,
     *   youtubeVideoId: string,
     *   youtubeChannelId: string,
     *   channelUrl: string
     * }
     */
    public function forHomepage(): array
    {
        $title = trim($this->settings->get('youtube_section_title', ''));
        $subtitle = trim($this->settings->get('youtube_section_subtitle', ''));
        $mode = $this->resolvedMode();

        $mp4Path = trim($this->settings->get('home_video_file', ''));
        $mp4Url = $mp4Path !== '' ? asset($mp4Path) : '';
        $youtubeVideoId = YouTubeService::extractVideoId($this->settings->get('youtube_video_url', '')) ?? '';
        $explicitChannel = trim($this->settings->get('youtube_channel_url', ''));
        $storedChannelId = trim($this->settings->get('youtube_channel_id', ''));
        $rawChannel = $explicitChannel !== '' ? $explicitChannel : $storedChannelId;
        $channelId = YouTubeService::extractChannelId($rawChannel) ?? '';

        $channelUrl = '';
        if ($explicitChannel !== '') {
            $channelUrl = YouTubeService::normalizeChannelUrl($explicitChannel);
        } elseif ($mode === 'youtube_channel' && $rawChannel !== '') {
            $channelUrl = YouTubeService::normalizeChannelUrl($rawChannel);
        }

        $player = 'none';
        if ($mode === 'mp4' && $mp4Url !== '') {
            $player = 'mp4';
        } elseif ($mode === 'youtube_video' && $youtubeVideoId !== '') {
            $player = 'youtube';
        } elseif ($mode === 'youtube_channel' && $channelId !== '' && str_starts_with($channelId, 'UC')) {
            $player = 'youtube_channel';
        }

        return [
            'visible' => $title !== '' || $subtitle !== '' || $player !== 'none' || $channelUrl !== '',
            'title' => $title,
            'subtitle' => $subtitle,
            'player' => $player,
            'mp4Url' => $mp4Url,
            'youtubeVideoId' => $youtubeVideoId,
            'youtubeChannelId' => $channelId,
            'channelUrl' => $channelUrl,
        ];
    }

    private function resolvedMode(): string
    {
        $mode = trim($this->settings->get('home_video_mode', ''));
        if (in_array($mode, ['mp4', 'youtube_video', 'youtube_channel'], true)) {
            return $mode;
        }
        if ($mode === 'none') {
            return 'none';
        }

        if (trim($this->settings->get('home_video_file', '')) !== '') {
            return 'mp4';
        }
        if (YouTubeService::extractVideoId($this->settings->get('youtube_video_url', '')) !== null) {
            return 'youtube_video';
        }
        if (trim($this->settings->get('youtube_channel_url', '') . $this->settings->get('youtube_channel_id', '')) !== '') {
            return 'youtube_channel';
        }

        return '';
    }
}
