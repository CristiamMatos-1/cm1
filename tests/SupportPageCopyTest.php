<?php

$root = dirname(__DIR__);
require $root . '/Core/SupportPageCopy.php';

use Core\SupportPageCopy;

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

assertSame('link vazio usa padrão', 'Suporte', SupportPageCopy::linkLabel('   '));
assertSame('link remove html', 'Downloads', SupportPageCopy::linkLabel(' <b>Downloads</b> '));
assertSame('título vazio usa padrão', 'Suporte Remoto', SupportPageCopy::title(''));
assertSame('título customizado', 'Arquivos', SupportPageCopy::title('Arquivos'));
assertSame('texto vazio permanece vazio', '', SupportPageCopy::text('  '));
assertSame('texto remove html', 'Linha 1', SupportPageCopy::text('<script>Linha 1</script>'));

$defaults = SupportPageCopy::fromMap([]);
assertSame('mapa vazio mantém o texto padrão', SupportPageCopy::DEFAULT_TEXT, $defaults['text']);
assertSame('mapa vazio mantém o link padrão', 'Suporte', $defaults['link']);

$cleared = SupportPageCopy::fromMap([
    SupportPageCopy::KEY_LINK => 'Central',
    SupportPageCopy::KEY_TITLE => 'Baixar programas',
    SupportPageCopy::KEY_TEXT => '',
]);
assertSame('mapa usa o link salvo', 'Central', $cleared['link']);
assertSame('mapa usa o título salvo', 'Baixar programas', $cleared['title']);
assertSame('mapa respeita texto apagado', '', $cleared['text']);

exit($failed === 0 ? 0 : 1);
