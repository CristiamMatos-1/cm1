<?php

$root = dirname(__DIR__);
require $root . '/Config/App.php';

use Config\App;
use Core\Services\SocialLinksService;
use Models\Setting;

App::boot($root);

spl_autoload_register(function (string $class) use ($root): void {
    $file = $root . '/' . str_replace('\\', '/', $class) . '.php';
    if (is_file($file)) {
        require $file;
    }
});

require $root . '/Core/helpers.php';

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

final class FakeSetting extends Setting
{
    public function __construct(private array $values = [])
    {
    }

    public function get(string $keyName, string $default = ''): string
    {
        return array_key_exists($keyName, $this->values) ? (string) $this->values[$keyName] : $default;
    }
}

$empty = (new SocialLinksService(new FakeSetting([])))->links();
assertSame('sem redes = lista vazia', 0, count($empty));

$filled = (new SocialLinksService(new FakeSetting([
    'social_instagram_url' => 'https://instagram.com/coninfoms',
    'social_facebook_url' => 'https://facebook.com/coninfoms',
    'whatsapp_number' => '556799999999',
])))->links();

assertSame('três links preenchidos', 3, count($filled));
assertSame('primeiro é instagram', 'instagram', $filled[0]['key']);
assertSame('tem facebook', 'facebook', $filled[1]['key']);
assertSame('whatsapp derivado do número', 'whatsapp', $filled[2]['key']);
assertSame('wa.me no whatsapp', true, str_contains($filled[2]['url'], 'wa.me/556799999999'));

$keys = SocialLinksService::settingKeys();
assertSame('tem chave instagram', true, in_array('social_instagram_url', $keys, true));
assertSame('tem chave linkedin', true, in_array('social_linkedin_url', $keys, true));

exit($failed === 0 ? 0 : 1);
