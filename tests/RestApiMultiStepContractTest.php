<?php

require_once __DIR__ . '/../lhc_web/lib/core/lhgenericbot/actionTypes/lhgenericbotactionrestapi.php';

function contract_expect($condition, $message)
{
    if (!$condition) {
        fwrite(STDERR, "FAIL: {$message}\n");
        exit(1);
    }
}

$class = 'erLhcoreClassGenericBotActionRestapi';

$nested = $class::extractAttribute(array('data' => array('upload' => array('url' => 'https://1.1.1.1/upload'))), 'data.upload.url', '.');
contract_expect($nested['found'] === true && $nested['value'] === 'https://1.1.1.1/upload', 'nested response path');

$reflection = new ReflectionClass($class);
$responseValue = $reflection->getMethod('multiStepResponseValue');
$responseValue->setAccessible(true);
$found = false;
$value = $responseValue->invokeArgs($reflection->newInstanceWithoutConstructor(), array(
    array('data' => array('token' => 'abc')),
    'data.token',
    array('token'),
    &$found
));
contract_expect($found === true && $value === 'abc', 'configured response path and fallback');

$resolveUrl = $reflection->getMethod('multiStepResolveUrl');
$resolveUrl->setAccessible(true);
contract_expect($resolveUrl->invoke(null, 'https://example.test/api/init', 'upload') === 'https://example.test/api/upload', 'relative upload URL resolution');
contract_expect($resolveUrl->invoke(null, 'https://example.test/api/init?x=1', '?token=2') === 'https://example.test/api/init?token=2', 'query-only URL resolution');

$mergeQuery = $reflection->getMethod('multiStepMergeQuery');
$mergeQuery->setAccessible(true);
contract_expect($mergeQuery->invoke(null, 'https://example.test/upload?existing=1', array('token' => 'a b')) === 'https://example.test/upload?existing=1&token=a+b', 'query merge');

$renderBody = $reflection->getMethod('multiStepRenderBody');
$renderBody->setAccessible(true);
$body = $renderBody->invoke(null, '{"name":"{{file_name}}","size":{{file_size}}}', array(
    '{{file_name}}' => 'photo.jpg',
    '{{file_size}}' => '12'
));
contract_expect($body === '{"name":"photo.jpg","size":12}', 'typed placeholder body rendering');

foreach (array(
    'http://127.0.0.1/',
    'http://100.64.0.1/',
    'http://192.0.2.1/',
    'http://[fc00::1]/',
    'http://user@example.com/',
    'http://example.com/#fragment'
) as $blockedUrl) {
    $blocked = false;
    try {
        $class::validateUrlSSRF($blockedUrl);
    } catch (Throwable $e) {
        $blocked = true;
    }
    contract_expect($blocked, 'SSRF blocked: ' . $blockedUrl);
}

$public = $class::validateUrlSSRF('https://1.1.1.1/');
contract_expect($public[0] === '1.1.1.1' && $public[2] === '1.1.1.1', 'public IPv4 accepted and pinned');

$media = (object)array('remote_file' => true, 'file_path_server' => '', 'type' => 'image/png', 'extension' => 'png', 'upload_name' => 'x.png', 'size' => 1);
$settings = array('multi_step_upload' => array('enabled' => true));
$headers = array();
$replace = array();
$replaceJson = array();
$query = array();
$error = '';
$details = array();
$ok = $class::processMultiStepUpload($media, $settings, 'https://1.1.1.1', $headers, $replace, $replaceJson, $query, $error, $details);
contract_expect($ok === false && strpos($error, 'local media file') !== false && $details['http_error'] === 'multi_step_file', 'local-file validation error');

echo "OK\n";
