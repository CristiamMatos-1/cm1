<?php

$root = dirname(__DIR__);
require $root . '/Config/App.php';

use Config\App;
use Core\Audit;
use Core\Schema;
use Models\AuditLog;
use Models\Page;
use Models\User;

App::boot($root);
$_SERVER['REMOTE_ADDR'] = '203.0.113.10';

spl_autoload_register(function (string $class) use ($root): void {
    $file = $root . '/' . str_replace('\\', '/', $class) . '.php';
    if (is_file($file)) {
        require $file;
    }
});

require $root . '/Core/helpers.php';
Schema::ensure();

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

function assertTrue(string $label, bool $cond): void
{
    assertSame($label, true, $cond);
}

// Política de privacidade seed
$page = new Page();
$privacy = $page->findBySlug('politica-de-privacidade');
assertTrue('política de privacidade existe', $privacy !== null);
assertSame('slug política', 'politica-de-privacidade', $privacy['slug'] ?? null);
assertTrue('política publicada', (int) ($privacy['is_published'] ?? 0) === 1);
assertTrue('política no rodapé', (int) ($privacy['show_in_footer'] ?? 0) === 1);

// Audit log
$before = (new AuditLog())->countAll();
Audit::log('create', 'articles', 99, 'teste auditoria');
$after = (new AuditLog())->countAll();
assertTrue('audit incrementou', $after === $before + 1);

$latest = (new AuditLog())->latest(1);
assertSame('audit action', 'create', $latest[0]['action'] ?? null);
assertSame('audit table', 'articles', $latest[0]['table_name'] ?? null);
assertSame('audit record', 99, (int) ($latest[0]['record_id'] ?? 0));
assertSame('audit ip', '203.0.113.10', $latest[0]['ip_address'] ?? null);
assertTrue('audit timestamp', !empty($latest[0]['created_at']));

// Anonimização LGPD
$users = new User();
$testEmail = 'lgpd-teste-' . uniqid('', true) . '@example.com';
assertTrue('cria usuário teste', $users->create([
    'name' => 'Pessoa Teste LGPD',
    'email' => $testEmail,
    'password' => 'senha-temporaria-123',
]));
$created = $users->findByEmail($testEmail);
assertTrue('usuário de teste criado', $created !== null);
$uid = (int) $created['id'];
assertTrue('não anonimizado ainda', !$users->isAnonymized($created));
assertTrue('anonymize ok', $users->anonymize($uid));
$anon = $users->findById($uid);
assertTrue('agora anonimizado', $users->isAnonymized($anon ?? []));
assertSame('nome anonimizado', 'Usuário anonimizado', $anon['name'] ?? null);
assertTrue('email anonimizado', str_starts_with((string) ($anon['email'] ?? ''), 'anonimizado-' . $uid));

// cleanup
$users->delete($uid);

// cookie banner markup present in footer file
$footer = (string) file_get_contents($root . '/Views/layout/footer.php');
assertTrue('banner cookie no footer', str_contains($footer, 'id="cookie-consent"'));
assertTrue('botão aceitar todos', str_contains($footer, 'cookie-accept-btn'));
assertTrue('botão gerenciar', str_contains($footer, 'cookie-manage-btn'));
assertTrue('link política no banner', str_contains($footer, 'politica-de-privacidade'));

exit($failed === 0 ? 0 : 1);
