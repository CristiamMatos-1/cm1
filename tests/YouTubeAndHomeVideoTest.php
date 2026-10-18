<?php

$root = dirname(__DIR__);
require $root . '/Config/App.php';

use Config\App;
use Core\Services\HomeVideoService;
use Core\Services\YouTubeService;
use Models\Setting;

App::boot($root);
putenv('APP_BASE_PATH=/cm1');
$_ENV['APP_BASE_PATH'] = '/cm1';

spl_autoload_register(function (string $class) use ($root): void {
    $file = $root . '/' . str_replace('\\', '/', $class) . '.php';
    if (is_file($file)) {
        require $file;
    }
});

require $root . '/Core/helpers.php';

final class ArraySetting extends Setting
{
    public function __construct(private array $values = [])
    {
    }

    public function get(string $keyName, string $default = ''): string
    {
        return array_key_exists($keyName, $this->values) ? (string) $this->values[$keyName] : $default;
    }
}

$failed = 0;

function assertSame(string $label, mixed $expected, mixed $actual): void
{
    global $failed;
    if ($expected !== $actual) {
        fwrite(STDERR, "FAIL {$label}: esperado " . var_export($expected, true) . ' obteve ' . var_export($actual, true) . "\n");
        $failed++;
        return;
    }
    echo "OK   {$label}\n";
}

$videoCases = [
    ['dQw4w9WgXcQ', 'dQw4w9WgXcQ'],
    ['https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'dQw4w9WgXcQ'],
    ['https://youtu.be/dQw4w9WgXcQ', 'dQw4w9WgXcQ'],
    ['https://www.youtube.com/embed/dQw4w9WgXcQ', 'dQw4w9WgXcQ'],
    ['https://www.youtube.com/shorts/dQw4w9WgXcQ', 'dQw4w9WgXcQ'],
    ['https://www.youtube.com/live/dQw4w9WgXcQ', 'dQw4w9WgXcQ'],
    ['https://www.youtube.com/watch?v=dQw4w9WgXcQ&t=30s', 'dQw4w9WgXcQ'],
    ['', null],
    ['https://example.com/video', null],
];

foreach ($videoCases as [$input, $expected]) {
    assertSame('video ' . ($input === '' ? '(vazio)' : $input), $expected, YouTubeService::extractVideoId($input));
}

$channelId = 'UCXuqSBlHAE6Xw-yeJA0Tunw';
$channelCases = [
    [$channelId, $channelId],
    ['https://www.youtube.com/channel/' . $channelId, $channelId],
    ['https://www.youtube.com/@algumcanal', null],
    ['', null],
];

foreach ($channelCases as [$input, $expected]) {
    assertSame('channel ' . ($input === '' ? '(vazio)' : $input), $expected, YouTubeService::extractChannelId($input));
}

assertSame(
    'normalize @handle',
    'https://www.youtube.com/@coninfoms',
    YouTubeService::normalizeChannelUrl('@coninfoms')
);
assertSame(
    'normalize UC id',
    'https://www.youtube.com/channel/' . $channelId,
    YouTubeService::normalizeChannelUrl($channelId)
);

$empty = (new HomeVideoService(new ArraySetting([])))->forHomepage();
assertSame('home vazia oculta', false, $empty['visible']);
assertSame('home vazia sem player', 'none', $empty['player']);

$mp4 = (new HomeVideoService(new ArraySetting([
    'home_video_mode' => 'mp4',
    'home_video_file' => '/uploads/videos/home.mp4',
])))->forHomepage();
assertSame('mp4 visível', true, $mp4['visible']);
assertSame('mp4 player', 'mp4', $mp4['player']);
assertSame('mp4 url contém arquivo', true, str_contains($mp4['mp4Url'], '/uploads/videos/home.mp4'));

$ytVideo = (new HomeVideoService(new ArraySetting([
    'home_video_mode' => 'youtube_video',
    'youtube_video_url' => 'https://youtu.be/dQw4w9WgXcQ',
])))->forHomepage();
assertSame('youtube vídeo visível', true, $ytVideo['visible']);
assertSame('youtube vídeo player', 'youtube', $ytVideo['player']);
assertSame('youtube vídeo id', 'dQw4w9WgXcQ', $ytVideo['youtubeVideoId']);

$channel = (new HomeVideoService(new ArraySetting([
    'home_video_mode' => 'youtube_channel',
    'youtube_channel_url' => 'https://www.youtube.com/channel/' . $channelId,
])))->forHomepage();
assertSame('canal visível', true, $channel['visible']);
assertSame('canal player', 'youtube_channel', $channel['player']);
assertSame('canal id', $channelId, $channel['youtubeChannelId']);

$titleOnly = (new HomeVideoService(new ArraySetting([
    'youtube_section_title' => 'Nosso canal',
])))->forHomepage();
assertSame('só título visível', true, $titleOnly['visible']);
assertSame('só título sem player', 'none', $titleOnly['player']);

$hiddenPlayer = (new HomeVideoService(new ArraySetting([
    'home_video_mode' => 'none',
    'home_video_file' => '/uploads/videos/home.mp4',
    'youtube_video_url' => 'https://youtu.be/dQw4w9WgXcQ',
])))->forHomepage();
assertSame('modo none oculta player', 'none', $hiddenPlayer['player']);
assertSame('modo none sem título fica oculto', false, $hiddenPlayer['visible']);

$mp4IgnoresLeftoverChannel = (new HomeVideoService(new ArraySetting([
    'home_video_mode' => 'mp4',
    'home_video_file' => '/uploads/videos/home.mp4',
    'youtube_channel_id' => $channelId,
])))->forHomepage();
assertSame('mp4 não herda inscrição do canal antigo', '', $mp4IgnoresLeftoverChannel['channelUrl']);

exit($failed === 0 ? 0 : 1);
