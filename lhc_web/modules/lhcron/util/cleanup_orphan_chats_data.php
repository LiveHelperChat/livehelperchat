<?php

/**
 * Cleans up orphaned chat data for chats that were closed without
 * running erLhcoreClassChatHelper::cleanupOnClose().
 *
 * Iterates over chats from newest to oldest and executes the cleanup
 * function for every chat that is already closed (status = 2).
 *
 * Usage:
 *   php cron.php -s site_admin -c cron/util/cleanup_orphan_chats_data
 *   php cron.php -s site_admin -c cron/util/cleanup_orphan_chats_data -p <start_chat_id>
 *
 * When <start_chat_id> is not provided, the loop starts from the newest chat.
 * When provided, the loop starts from that chat id (inclusive) and goes down.
 * 
 * DIRECT queries if you don't care about orhphan files
 * 
 * DELETE FROM lh_abstract_auto_responder_chat WHERE chat_id IN (SELECT id FROM lh_chat WHERE status = 2);
 * DELETE FROM lh_generic_bot_repeat_restrict WHERE chat_id IN (SELECT id FROM lh_chat WHERE status = 2);
 * DELETE FROM lh_generic_bot_chat_event WHERE chat_id IN (SELECT id FROM lh_chat WHERE status = 2);
 * DELETE FROM lh_generic_bot_pending_event WHERE chat_id IN (SELECT id FROM lh_chat WHERE status = 2);
 * DELETE FROM lh_chat_voice_video WHERE chat_id IN (SELECT id FROM lh_chat WHERE status = 2);
 * DELETE FROM lh_transfer WHERE transfer_scope = 0 AND chat_id IN (SELECT id FROM lh_chat WHERE status = 2);
 */

/* To preview whcih data will be deleted

SELECT 'auto_responder_chat' AS t, COUNT(*) AS c FROM lh_abstract_auto_responder_chat r JOIN lh_chat c ON c.id = r.chat_id AND c.status = 2
UNION ALL SELECT 'bot_repeat_restrict', COUNT(*) FROM lh_generic_bot_repeat_restrict r JOIN lh_chat c ON c.id = r.chat_id AND c.status = 2
UNION ALL SELECT 'bot_chat_event', COUNT(*) FROM lh_generic_bot_chat_event r JOIN lh_chat c ON c.id = r.chat_id AND c.status = 2
UNION ALL SELECT 'bot_pending_event', COUNT(*) FROM lh_generic_bot_pending_event r JOIN lh_chat c ON c.id = r.chat_id AND c.status = 2
UNION ALL SELECT 'chat_voice_video', COUNT(*) FROM lh_chat_voice_video r JOIN lh_chat c ON c.id = r.chat_id AND c.status = 2
UNION ALL SELECT 'transfer (scope=0)', COUNT(*) FROM lh_transfer r JOIN lh_chat c ON c.id = r.chat_id AND c.status = 2 AND r.transfer_scope = 0
UNION ALL SELECT 'chat_file (tmp)', COUNT(*) FROM lh_chat_file r JOIN lh_chat c ON c.id = r.chat_id AND c.status = 2 AND r.tmp = 1 AND r.user_id = 0;

*/

$startId = null;

if (isset($cronjobPathOption->value) && trim((string)$cronjobPathOption->value) !== '') {
    $value = (int)$cronjobPathOption->value;
    if ($value > 0) {
        $startId = $value;
    }
}

echo "Starting orphan chat data cleanup\n";
echo "  start_chat_id : " . ($startId !== null ? $startId : 'newest') . "\n";

$limit = 200;
$upperBound = $startId;
$cleaned = 0;
$processed = 0;

do {
    $params = array(
        'limit' => $limit,
        'sort'  => 'id DESC',
    );

    if ($upperBound !== null) {
        $params['filterlte'] = array('id' => $upperBound);
    }

    $chats = erLhcoreClassModelChat::getList($params);

    if (empty($chats)) {
        break;
    }

    $batchMin = null;

    foreach ($chats as $chat) {
        $processed++;

        if ($chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT) {
            erLhcoreClassChatHelper::cleanupOnClose($chat->id);
            $cleaned++;
            echo "Cleaned closed chat ID {$chat->id}\n";
        }

        // Sorted by id DESC, the last assigned id is the lowest in this batch
        $batchMin = $chat->id;
    }

    // Continue with ids strictly lower than the lowest seen in this batch
    $upperBound = $batchMin - 1;

} while (count($chats) === $limit);

echo "Done. Processed {$processed} chats, cleaned up {$cleaned} closed chats.\n";

?>
