<?php

$tpl = erLhcoreClassTemplate::getInstance('lhvoicevideo/sessions.tpl.php');

$input = new stdClass();
$input->chat_id = isset($_GET['chat_id']) && is_numeric($_GET['chat_id']) ? (int)$_GET['chat_id'] : '';
$input->user_id = isset($_GET['user_id']) && is_numeric($_GET['user_id']) ? (int)$_GET['user_id'] : '';
$input->dep_id = isset($_GET['dep_id']) && is_numeric($_GET['dep_id']) ? (int)$_GET['dep_id'] : '';
$input->status = isset($_GET['status']) && is_numeric($_GET['status']) ? (int)$_GET['status'] : '';
$input->video = isset($_GET['video']) && is_numeric($_GET['video']) ? (int)$_GET['video'] : '';
$input->date_from = isset($_GET['date_from']) && DateTime::createFromFormat('Y-m-d', $_GET['date_from']) !== false ? $_GET['date_from'] : '';
$input->date_to = isset($_GET['date_to']) && DateTime::createFromFormat('Y-m-d', $_GET['date_to']) !== false ? $_GET['date_to'] : '';

$filter = array();

foreach (array('chat_id', 'user_id', 'dep_id', 'status', 'video') as $attr) {
    if ($input->{$attr} !== '') {
        $filter['filter']['lh_chat_voice_video_session.' . $attr] = $input->{$attr};
    }
}

if ($input->date_from != '') {
    $filter['filtergte']['lh_chat_voice_video_session.ctime'] = strtotime($input->date_from . ' 00:00:00');
}

if ($input->date_to != '') {
    $filter['filterlte']['lh_chat_voice_video_session.ctime'] = strtotime($input->date_to . ' 23:59:59');
}

// Operators see only calls from departments they have access to
$limitation = erLhcoreClassChat::getDepartmentLimitation('lh_chat_voice_video_session');

if ($limitation === false) {
    $filter['filter']['lh_chat_voice_video_session.id'] = -1;
} elseif ($limitation !== true) {
    $filter['customfilter'][] = $limitation;
}

$appendParams = array();
foreach ((array)$input as $key => $value) {
    if ($value !== '') {
        $appendParams[$key] = $value;
    }
}

$tpl->set('input', $input);
$tpl->set('appendQuery', http_build_query($appendParams));

try {

    if (isset($_GET['export']) && $_GET['export'] == 'csv') {

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="calls-' . date('Y-m-d') . '.csv"');

        $fp = fopen('php://output', 'w');
        fputcsv($fp, array('ID', 'Chat ID', 'Operator ID', 'Operator', 'Department ID', 'Department', 'Provider', 'Type', 'Initiator', 'Status', 'Created', 'Answered', 'Ended', 'Wait time (s)', 'Duration (s)', 'End reason'));

        $offset = 0;
        do {
            $items = erLhcoreClassModelChatVoiceVideoSession::getList(array_merge($filter, array('limit' => 500, 'offset' => $offset)));
            foreach ($items as $item) {
                fputcsv($fp, array(
                    $item->id,
                    $item->chat_id,
                    $item->user_id,
                    $item->user instanceof erLhcoreClassModelUser ? $item->user->name_official : '',
                    $item->dep_id,
                    $item->department instanceof erLhcoreClassModelDepartament ? $item->department->name : '',
                    $item->provider,
                    $item->video == 1 ? 'video' : 'voice',
                    $item->initiator == erLhcoreClassModelChatVoiceVideoSession::INITIATOR_OPERATOR ? 'operator' : 'visitor',
                    $item->status_front,
                    $item->ctime > 0 ? date('Y-m-d H:i:s', $item->ctime) : '',
                    $item->answered_at > 0 ? date('Y-m-d H:i:s', $item->answered_at) : '',
                    $item->ended_at > 0 ? date('Y-m-d H:i:s', $item->ended_at) : '',
                    $item->wait_time,
                    $item->duration,
                    $item->end_reason
                ));
            }
            $offset += 500;
        } while (count($items) == 500);

        fclose($fp);
        exit;
    }

    $stats = erLhcoreClassModelChatVoiceVideoSession::getCount($filter, '', false,
        'COUNT(`lh_chat_voice_video_session`.`id`) AS `total`,' .
        'SUM(`lh_chat_voice_video_session`.`status` = ' . erLhcoreClassModelChatVoiceVideoSession::STATUS_ENDED . ') AS `answered`,' .
        'SUM(`lh_chat_voice_video_session`.`status` = ' . erLhcoreClassModelChatVoiceVideoSession::STATUS_MISSED . ') AS `missed`,' .
        'SUM(`lh_chat_voice_video_session`.`duration`) AS `talk_time`,' .
        'AVG(CASE WHEN `lh_chat_voice_video_session`.`status` = ' . erLhcoreClassModelChatVoiceVideoSession::STATUS_ENDED . ' THEN `lh_chat_voice_video_session`.`duration` END) AS `avg_duration`,' .
        'AVG(CASE WHEN `lh_chat_voice_video_session`.`answered_at` > 0 THEN `lh_chat_voice_video_session`.`answered_at` - `lh_chat_voice_video_session`.`ctime` END) AS `avg_wait`',
        false
    );

    $tpl->set('stats', $stats);

    $pages = new lhPaginator();
    $pages->items_total = (int)$stats['total'];
    $pages->translationContext = 'chat/activechats';
    $pages->serverURL = erLhcoreClassDesign::baseurl('voicevideo/sessions');
    $pages->querystring = !empty($appendParams) ? '?' . http_build_query($appendParams) : '';
    $pages->paginate();
    $tpl->set('pages', $pages);

    if ($pages->items_total > 0) {
        $items = erLhcoreClassModelChatVoiceVideoSession::getList(array_merge($filter, array('limit' => $pages->items_per_page, 'offset' => $pages->low)));
        $tpl->set('items', $items);

        // Recordings of listed calls
        $recordings = array();
        if ($currentUser->hasAccessTo('lhvoicevideo', 'recordings') && !empty($items)) {
            try {
                foreach (erLhcoreClassModelChatVoiceVideoRecording::getList(array('limit' => false, 'filterin' => array('session_id' => array_keys($items)))) as $recording) {
                    $recordings[$recording->session_id][] = $recording;
                }
            } catch (Exception $e) {
                // Recordings table is missing, database update is required
            }
        }
        $tpl->set('recordings', $recordings);
    }

} catch (Exception $e) {
    $tpl->set('db_error', true);
}

$Result['content'] = $tpl->fetch();
$Result['path'] = array(
    array('url' => erLhcoreClassDesign::baseurl('system/configuration'), 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration', 'System configuration')),
    array('url' => erLhcoreClassDesign::baseurl('voicevideo/sessions'), 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/voice_video', 'Call history')));

?>
