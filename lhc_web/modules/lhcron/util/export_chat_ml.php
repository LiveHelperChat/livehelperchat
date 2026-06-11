<?php

/**
 * Export chats to ChatML (JSONL) format.
 *
 * php cron.php -s site_admin -c cron/util/export_chat_ml -p "<folder_path>[|dep_id:<id>][|bot_id:<id>][|since:<YYYY-MM-DD>][|last_messages:<n>][|system_prompt:<text>][|only_with_tool_calls:1][|exclude_operator_messages:1]"
 *
 * The first token is the output folder. The export file will be named automatically
 * using a timestamp: export_YYYY-MM-DD_HH-MM-SS.jsonl
 *
 * If a file named system.md exists in the folder, its contents are used as the
 * system prompt. The system_prompt parameter overrides system.md when both are present.
 *
 * Examples:
 *   php cron.php -s site_admin -c cron/util/export_chat_ml -p "/tmp/exports"
 *   php cron.php -s site_admin -c cron/util/export_chat_ml -p "/tmp/exports|dep_id:3|since:2026-01-01"
 *   php cron.php -s site_admin -c cron/util/export_chat_ml -p "/tmp/exports|dep_id:3|bot_id:5|since:2026-01-01|last_messages:20"
 */

$rawParam = isset($cronjobPathOption->value) ? (string)$cronjobPathOption->value : '';

if ($rawParam === '') {
    echo "ERROR: No parameters provided.\n";
    echo "Usage: -p \"<folder_path>[|dep_id:<id>][|bot_id:<id>][|since:<YYYY-MM-DD>][|last_messages:<n>][|system_prompt:<text>][|only_with_tool_calls:1][|exclude_operator_messages:1]\"\n";
    exit(1);
}

// Parse parameters – first token is the output folder, rest are key:value pairs separated by |
$tokens = explode('|', $rawParam);
$folderPath = rtrim(trim(array_shift($tokens)), '/\\');

if ($folderPath === '') {
    echo "ERROR: Folder path is required as the first parameter.\n";
    exit(1);
}

$depId       = null;
$botId       = null;
$since       = null;
$lastMessages = 15;
$systemPrompt = '';
$onlyWithToolCalls = false;
$excludeOperatorMessages = false;

foreach ($tokens as $token) {
    $token = trim($token);
    if ($token === '') {
        continue;
    }

    $colonPos = strpos($token, ':');
    if ($colonPos === false) {
        continue;
    }

    $key   = strtolower(substr($token, 0, $colonPos));
    $value = substr($token, $colonPos + 1);

    switch ($key) {
        case 'dep_id':
            $depId = (int)$value;
            break;
        case 'bot_id':
            $botId = (int)$value;
            break;
        case 'since':
            $ts = strtotime($value);
            if ($ts !== false) {
                $since = $ts;
            } else {
                echo "WARNING: Could not parse date '{$value}', ignoring 'since' filter.\n";
            }
            break;
        case 'last_messages':
            $lastMessages = (int)$value;
            break;
        case 'system_prompt':
            $systemPrompt = $value;
            break;
        case 'only_with_tool_calls':
            $onlyWithToolCalls = (bool)(int)$value;
            break;
        case 'exclude_operator_messages':
            $excludeOperatorMessages = (bool)(int)$value;
            break;
        default:
            echo "WARNING: Unknown parameter key '{$key}', ignoring.\n";
    }
}

// Validate output folder
if (!is_dir($folderPath)) {
    echo "ERROR: Directory does not exist: {$folderPath}\n";
    exit(1);
}

// Auto-generate timestamped file name
$filePath = $folderPath . DIRECTORY_SEPARATOR . 'export_' . date('Y-m-d_H-i-s') . '.jsonl';

// Load system prompt from system.md if present and not already set via parameter
$systemMdPath = $folderPath . DIRECTORY_SEPARATOR . 'system.md';
if ($systemPrompt === '' && is_file($systemMdPath)) {
    $systemPromptFromFile = trim(file_get_contents($systemMdPath));
    if ($systemPromptFromFile !== '') {
        $systemPrompt = $systemPromptFromFile;
        echo "  system.md : {$systemMdPath}\n";
    }
}

echo "Starting ChatML export\n";
echo "  Folder    : {$folderPath}\n";
echo "  File      : {$filePath}\n";
echo "  dep_id    : " . ($depId  !== null ? $depId  : 'any') . "\n";
echo "  bot_id    : " . ($botId  !== null ? $botId  : 'any') . "\n";
echo "  since     : " . ($since  !== null ? date('Y-m-d H:i:s', $since) : 'any') . "\n";
echo "  last_messages: {$lastMessages}\n";

// Build query filter
$queryParams = array(
    'filter'  => array('status' => erLhcoreClassModelChat::STATUS_CLOSED_CHAT),
    'sort'    => 'id ASC',
    'limit'   => 500,
    'offset'  => 0,
);

if ($depId !== null && $depId > 0) {
    $queryParams['filter']['dep_id'] = $depId;
}

if ($botId !== null && $botId > 0) {
    $queryParams['filter']['gbot_id'] = $botId;
}

if ($since !== null) {
    $queryParams['filtergte'] = array('time' => $since);
}

// ChatML export params
$exportParams = array(
    'last_messages'             => $lastMessages,
    'only_with_tool_calls'      => $onlyWithToolCalls,
    'exclude_operator_messages' => $excludeOperatorMessages,
);

if ($systemPrompt !== '') {
    $exportParams['system_prompt'] = $systemPrompt;
}

$fp = fopen($filePath, 'w');
if ($fp === false) {
    echo "ERROR: Cannot open file for writing: {$filePath}\n";
    exit(1);
}

$totalProcessed = 0;
$totalExported  = 0;
$lastId = 0;

while (true) {
    $currentParams = $queryParams;
    if ($lastId > 0) {
        $currentParams['filtergt'] = array('id' => $lastId);
    }

    $chats = erLhcoreClassModelChat::getList($currentParams);

    if (empty($chats)) {
        break;
    }

    foreach ($chats as $chat) {
        $lastId = $chat->id;
        $totalProcessed++;

        $payload = \LiveHelperChat\Helpers\Export\ChatML::fromChat($chat, $exportParams);

        if (empty($payload['messages'])) {
            continue;
        }

        fwrite($fp, json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL);
        $totalExported++;
    }

    echo "Processed {$totalProcessed} chats, exported {$totalExported} so far (last id: {$lastId})...\n";

    if (count($chats) < $queryParams['limit']) {
        break;
    }
}

fclose($fp);

echo "Done. Processed: {$totalProcessed}, Exported: {$totalExported}\n";
echo "Output saved to: {$filePath}\n";

?>
