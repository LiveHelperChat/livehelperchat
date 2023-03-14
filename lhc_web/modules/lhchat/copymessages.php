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

    $messages = erLhcoreClassModelmsg::getList($filter);

    erLhcoreClassChat::setTimeZoneByChat($chat);

    erTranslationClassLhTranslation::$htmlEscape = false;

    $tplPlain = new erLhcoreClassTemplate( 'lhchat/messagelist/plain.tpl.php');
    $tplPlain->set('chat', $chat);
    $tplPlain->set('messages', $messages);

    if (isset($_GET['system']) || isset($_GET['meta'])) {
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