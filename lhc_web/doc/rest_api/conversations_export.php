<?php

/**
 * Mail Conversations Export Script
 *
 * Exports mail conversations incrementally using the restapi/conversations endpoint.
 * Uses id_gt for pagination and timetots to skip recent conversations.
 * Outputs JSONL format (one JSON object per line).
 *
 * Usage:
 *   php conversations_export.php
 *
 * The script tracks progress in $progressFile so it can resume.
 * Set $startConversationId to override and restart from a specific ID.
 *
 * Uses
 * https://api.livehelperchat.com/#/mail/get_restapi_conversations
 *
 * Database structure definition
 * https://dbdiagram.io/d/Live-Helper-Chat-67e68aa04f7afba184913d62
 */

include 'lhrestapi.php';

// ─── Configuration ──────────────────────────────────────────────────────────
$host        = 'https://demo.livehelperchat.com';     // e.g. https://demo.livehelperchat.com
$username    = 'admin';
$apiKey      = 'demo';

$startConversationId = 0;     // Override to restart from a specific ID (0 = resume from progress)
$appendMode  = false;         // false = truncate output file on each run; true = append to existing
$limit       = 100;           // Conversations per API call (max batch)
$delaySec    = 1800;          // 30 minutes – skip conversations newer than this (seconds)
$apiPause    = 500000;        // Microseconds between API calls (0.5 sec)

/*
 * Prefill fields supported by the conversations endpoint:
 *   department_name, plain_user_name, mailbox, subject, messages_statistic,
 *   mailbox_name, match_rule_name
 * Set to empty string to disable prefill.
 */
$prefillFields = 'mailbox,mail_variables_array';

/*
 * Set to true to include all messages of each conversation in the export.
 * Warning: this significantly increases payload size.
 */
$includeMessages = true;

$outputFile  = __DIR__ . '/conversations_export.jsonl';
$progressFile = __DIR__ . '/conversations_export_progress.json';

// ─── Resume logic ───────────────────────────────────────────────────────────
$lastId = $startConversationId > 0 ? $startConversationId : 0;
$totalExported = 0;

if ($startConversationId <= 0 && file_exists($progressFile)) {
    $progress = json_decode(file_get_contents($progressFile), true);
    if ($progress && isset($progress['last_id'])) {
        $lastId = (int)$progress['last_id'];
        $totalExported = (int)$progress['total_exported'];
        echo "Resuming from conversation ID {$lastId} (already exported {$totalExported})\n";
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

echo "Exporting conversations to {$outputFile}\n";
echo str_repeat('-', 60) . "\n";

$page = 0;

do {
    $params = [
        'id_gt'          => $lastId,
        'limit'          => $limit,
        'timetots'       => time() - $delaySec,
        'sort'           => 'id_asc',
        'count_records'  => 'false',
    ];

    if ($prefillFields !== '') {
        $params['prefill_fields'] = $prefillFields;
    }

    if ($includeMessages) {
        $params['include_messages'] = 'true';
    }

    echo sprintf("[Page %3d] Fetching conversations id > %d ... ", ++$page, $lastId);

    try {
        $response = $LHCRestAPI->execute('conversations', $params, [], 'GET', true);
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
    echo "got {$count} conversations\n";

    if ($count === 0) {
        break; // No more conversations
    }

    // Write each conversation as a JSON line
    foreach ($response->list as $conv) {
        fwrite($fh, json_encode($conv, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n");
        $lastId = max($conv->id, $lastId); // Track the highest ID seen
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
echo "Done. Exported {$totalExported} conversations to {$outputFile}\n";
