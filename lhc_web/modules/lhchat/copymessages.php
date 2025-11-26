<?php

$chat = erLhcoreClassModelChat::fetch($Params['user_parameters']['chat_id']);

if ( erLhcoreClassChat::hasAccessToRead($chat) )
{
    if (isset($_GET['system']) && $_GET['system'] == 'true') {
        $filter = array('limit' => 5000, 'filter' => array('chat_id' => $chat->id));
    } else {
        $filter = array('limit' => 5000, 'filternotin' => array('user_id' => array(-1)), 'filter' => array('chat_id' => $chat->id));
    }

    if (!isset($_GET['bot']) || $_GET['bot'] == 'false') {
        $filter['filternot']['user_id'] = -2;
    }

    $filter['filternotlikefields'][] = ['meta_msg' => '"debug":true'];

    $messages = erLhcoreClassModelmsg::getList($filter);

    erLhcoreClassChat::setTimeZoneByChat($chat);

    erTranslationClassLhTranslation::$htmlEscape = false;

    $tplPlain = new erLhcoreClassTemplate( 'lhchat/messagelist/plain.tpl.php');
    $tplPlain->set('chat', $chat);
    $tplPlain->set('messages', $messages);
    $tplPlain->set('see_sensitive_information', !((int)erLhcoreClassModelChatConfig::fetch('guardrails_enabled')->current_value == 1) || $currentUser->hasAccessTo('lhchat','see_sensitive_information'));

    if (!(isset($_GET['whisper']) && $_GET['whisper'] == 'true')) {
        $tplPlain->set('remove_whisper', true);
    }

    if (isset($_GET['no_parse']) && $_GET['no_parse'] == 'true') {
        $tplPlain->set('no_bb_code', true);
    }

    if (isset($_GET['system']) || isset($_GET['meta']) || isset($_GET['user_data']) || isset($_GET['no_parse']) ) {
        echo json_encode(array('result' => $tplPlain->fetch()));
        exit;
    }

    $tpl = erLhcoreClassTemplate::getInstance('lhchat/copymessages.tpl.php');
    $tpl->set('chat', $chat);
    $tpl->set('messages', $tplPlain->fetch());

    erTranslationClassLhTranslation::$htmlEscape = true;

    echo $tpl->fetch();
}

exit;

?>