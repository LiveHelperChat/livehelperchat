<?php

/**
 * Streams call recording. Supports HTTP range requests for seeking in the player.
 */

$recording = is_numeric($Params['user_parameters']['id']) ? erLhcoreClassModelChatVoiceVideoRecording::fetch((int)$Params['user_parameters']['id'], false) : false;

if (!($recording instanceof erLhcoreClassModelChatVoiceVideoRecording)) {
    http_response_code(404);
    exit;
}

$chat = erLhcoreClassModelChat::fetch($recording->chat_id, false);

// Access follows chat access (department limitations). Recordings of removed chats are removed as well.
if (!($chat instanceof erLhcoreClassModelChat) || !erLhcoreClassChat::hasAccessToRead($chat)) {
    http_response_code(403);
    exit;
}

$file = $recording->local_file;

if ($file === false || !is_file($file) || !is_readable($file)) {
    http_response_code(404);
    echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/voice_video', 'Recording file is not available');
    exit;
}

erLhcoreClassLog::write('Call recording ' . $recording->id . ' of chat ' . $chat->id . ' was ' . ($Params['user_parameters_unordered']['download'] == 1 ? 'downloaded' : 'played') . ' by user ' . $currentUser->getUserID(),
    ezcLog::SUCCESS_AUDIT,
    array(
        'source' => 'lhc',
        'category' => 'voice_call_recording',
        'line' => __LINE__,
        'file' => __FILE__,
        'object_id' => $chat->id
    )
);

$size = filesize($file);
$start = 0;
$end = $size - 1;

header('Content-Type: ' . (substr($file, -4) == '.ogg' ? 'audio/ogg' : 'video/mp4'));
header('Accept-Ranges: bytes');
header('Cache-Control: private, no-store');
header('X-Content-Type-Options: nosniff');

if ($Params['user_parameters_unordered']['download'] == 1) {
    header('Content-Disposition: attachment; filename="' . basename($file) . '"');
}

if (isset($_SERVER['HTTP_RANGE']) && preg_match('/bytes=(\d*)-(\d*)/', $_SERVER['HTTP_RANGE'], $matches)) {
    if ($matches[1] !== '') {
        $start = (int)$matches[1];
        $end = $matches[2] !== '' ? min((int)$matches[2], $size - 1) : $size - 1;
    } elseif ($matches[2] !== '') {
        $start = max(0, $size - (int)$matches[2]);
    }

    if ($start > $end || $start >= $size) {
        http_response_code(416);
        header('Content-Range: bytes */' . $size);
        exit;
    }

    http_response_code(206);
    header('Content-Range: bytes ' . $start . '-' . $end . '/' . $size);
}

header('Content-Length: ' . ($end - $start + 1));

while (ob_get_level() > 0) {
    ob_end_clean();
}

$fp = fopen($file, 'rb');
fseek($fp, $start);
$remaining = $end - $start + 1;
while ($remaining > 0 && !feof($fp)) {
    $chunk = fread($fp, min(8192, $remaining));
    echo $chunk;
    $remaining -= strlen($chunk);
    flush();
}
fclose($fp);
exit;

?>
