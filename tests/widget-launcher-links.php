<?php
// Run with: php tests/widget-launcher-links.php
require __DIR__ . '/../lhc_web/lib/core/lhcore/lhdbtrait.php';
require __DIR__ . '/../lhc_web/lib/models/lhabstract/erlhabstractmodelwidgettheme.php';

$legacy = ['label' => 'Help', 'url' => 'https://example.org/help'];
$online = ['label' => 'Call', 'url' => 'https://example.org/call', 'visibility' => 'online'];
$offline = ['label' => 'Request support', 'url' => 'https://example.org/requests', 'visibility' => 'offline'];
$always = ['label' => 'Forum', 'url' => 'https://example.org/forum'];
$theme = new erLhAbstractModelWidgetTheme;
$theme->setState(['bot_configuration' => json_encode(['launcher_links' => [
    $legacy, $online, $offline, $always + ['visibility' => 'always']
]])]);

$expected = [$legacy, $online, $offline, $always];
if ($theme->getLauncherLinks($invalid) !== $expected || $invalid) {
    throw new RuntimeException('Legacy links and explicit visibility options should retain their order.');
}

foreach (['unrecognized', ['offline']] as $invalidOption) {
    $theme->bot_configuration_array = ['launcher_links' => array_merge($expected, [$legacy + ['visibility' => $invalidOption]])];
    if ($theme->getLauncherLinks($invalid) !== $expected || !$invalid) {
        throw new RuntimeException('An invalid visibility option should not broaden link visibility.');
    }
    $theme->beforeSave();
    if (json_decode($theme->bot_configuration, true)['launcher_links'] !== $expected) {
        throw new RuntimeException('Saving an imported theme should preserve valid rows and filter invalid rows.');
    }
}

echo "Widget launcher link visibility tests passed.\n";
