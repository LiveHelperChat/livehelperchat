<?php

$chat = erLhcoreClassModelChat::fetch($Params['user_parameters']['chat_id']);

if ( erLhcoreClassChat::hasAccessToRead($chat) )
{
    $messages = erLhcoreClassModelmsg::getList(array('limit' => 5000, 'filternotin' => array('user_id' => array(-1)), 'filter' => array('chat_id' => $chat->id)));

    erLhcoreClassChat::setTimeZoneByChat($chat);

    $formatted = array();
    foreach ($messages as $msg) {
        $formatted[] = '[' . date('H:i:s',$msg->time).'] '. ($msg->user_id == 0 ? htmlspecialchars($chat->nick) : htmlspecialchars($msg->name_support)).': '.htmlspecialchars($msg->msg);
    }

    echo json_encode(array('error' => false,'result' =>  erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Conversation started at').': '. date('Y-m-d H:i:s',$chat->time) . "\n" . implode("\n", $formatted)));
}

exit;

?>