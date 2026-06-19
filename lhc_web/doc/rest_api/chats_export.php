<?php

/**
 * Chat Export Script
 *
 * Exports chats incrementally using the restapi/chats endpoint.
 * Uses id_gt for pagination and delay to skip recent chats.
 * Outputs JSONL format (one JSON object per line).
 *
 * Usage:
 *   php chats_export.php
 *
 * The script tracks progress in $progressFile so it can resume.
 * Set $startChatId to override and restart from a specific ID.
 *
 * Uses
 * https://api.livehelperchat.com/#/chat/get_restapi_chats
 *
 * Database structure definition
 * https://dbdiagram.io/d/Live-Helper-Chat-67e68aa04f7afba184913d62
 */

include 'lhrestapi.php';

// ─── Configuration ──────────────────────────────────────────────────────────
$host        = 'https://demo.livehelperchat.com';     // e.g. https://demo.livehelperchat.com
$username    = 'admin';
$apiKey      = 'demo';

$startChatId = 0;               // Override to restart from a specific ID (0 = resume from progress)
$appendMode  = false;           // false = truncate output file on each run; true = append to existing
$limit       = 500;             // Chats per API call (max batch)
$delaySec    = 1800;            // 30 minutes – skip chats newer than this (seconds from chat start)
$apiPause    = 500000;          // Microseconds between API calls (0.5 sec)

/*
 * You can set any of those
 * https://github.com/LiveHelperChat/livehelperchat/blob/85b64a8cbb29592e09aa0ff3510d38c9d442a6fb/lhc_web/lib/models/lhchat/erlhcoreclassmodelchat.php#L209
 *
 * What fields to prefill E.g messages_statistic, subject, link, time_created_front, department_name, plain_user_name, product_name, n_official, n_off_full, wait_time_pending, wait_time_seconds, cls_time_front, tatus_sub_sub, can_edit_chat, unread_time, chat_actions
 * */
$prefillFields = 'messages_statistic,chat_variables_array';

$outputFile  = __DIR__ . '/chats_export.jsonl';
$progressFile = __DIR__ . '/chats_export_progress.json';

// ─── Resume logic ───────────────────────────────────────────────────────────
$lastId = $startChatId > 0 ? $startChatId : 0;
$totalExported = 0;

if ($startChatId <= 0 && file_exists($progressFile)) {
    $progress = json_decode(file_get_contents($progressFile), true);
    if ($progress && isset($progress['last_id'])) {
        $lastId = (int)$progress['last_id'];
        $totalExported = (int)$progress['total_exported'];
        echo "Resuming from chat ID {$lastId} (already exported {$totalExported})\n";
    }
}

$LHCRestAPI = new LHCRestAPI($host, $username, $apiKey);

// Open output file – truncate by default, append if $appendMode is true
$mode = $appendMode ? 'a' : 'w';
$fh = fopen($outputFile, $mode);
if (!$fh) {
    die("Cannot open output file: {$outputFile}\n");
}

if (!$appendMode && $totalExported === 0) {
    // Truncate progress when starting fresh
    file_put_contents($progressFile, json_encode([
        'last_id'         => 0,
        'total_exported'  => 0,
        'exported_at'     => time(),
    ], JSON_PRETTY_PRINT));
}

echo "Exporting chats to {$outputFile}\n";
echo str_repeat('-', 60) . "\n";

$page = 0;

do {
    $params = [
        'id_gt' => $lastId,
        'limit' => $limit,
        'delay' => $delaySec,
        'sort'  => 'id_asc',
        'count_records'  => 'false',
        'prefill_fields' => $prefillFields
    ];

    echo sprintf("[Page %3d] Fetching chats id > %d ... ", ++$page, $lastId);

    try {
        $response = $LHCRestAPI->execute('chats', $params, [], 'GET', true);
    } catch (Exception $e) {
        echo "ERROR: " . $e->getMessage() . "\n";
        echo "       URL: {$LHCRestAPI->lastRequestUrl}\n";
        if ($LHCRestAPI->lastCurlError !== '') {
            echo "       cURL error: {$LHCRestAPI->lastCurlError}\n";
        }
        echo "       HTTP status: {$LHCRestAPI->lastHttpCode}\n";
        if ($LHCRestAPI->lastRawResponse !== '') {
            $preview = strlen($LHCRestAPI->lastRawResponse) > 300
                ? substr($LHCRestAPI->lastRawResponse, 0, 300) . '...'
                : $LHCRestAPI->lastRawResponse;
            echo "       Raw response: {$preview}\n";
        }
        break;
    }

    // Check if API returned an error envelope
    if (isset($response->error) && $response->error === true) {
        $errorMsg = isset($response->result) ? $response->result : 'Unknown API error';
        echo "API ERROR: {$errorMsg}\n";
        break;
    }

    if (!isset($response->list) || !is_array($response->list)) {
        echo "No list in response or unexpected format.\n";
        break;
    }

    $count = count($response->list);
    echo "got {$count} chats\n";

    if ($count === 0) {
        break; // No more chats
    }

    // Write each chat as a JSON line
    foreach ($response->list as $chat) {
        fwrite($fh, json_encode($chat, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n");
        $lastId = max($chat->id,$lastId); // Track the highest ID seen
    }

    $totalExported += $count;

    // Save progress after every page
    file_put_contents($progressFile, json_encode([
        'last_id'         => $lastId,
        'total_exported'  => $totalExported,
        'exported_at'     => time(),
    ], JSON_PRETTY_PRINT));

    echo sprintf("       Last ID: %d  |  Total exported: %d\n", $lastId, $totalExported);

    // If we got fewer results than the limit, we're done
    if ($count < $limit) {
        break;
    }

    // Polite pause between API calls
    usleep($apiPause);

} while (true);

fclose($fh);

echo str_repeat('-', 60) . "\n";
echo "Done. Exported {$totalExported} chats to {$outputFile}\n";

