<?php

$chat = erLhcoreClassModelChat::fetch($Params['user_parameters']['chat_id']);

if ( erLhcoreClassChat::hasAccessToRead($chat) )
{
    if (isset($_GET['system']) && $_GET['system'] == 'true') {
        $messages = erLhcoreClassModelmsg::getList(array('limit' => 5000, 'filter' => array('chat_id' => $chat->id)));
    } else {
        $messages = erLhcoreClassModelmsg::getList(array('limit' => 5000, 'filternotin' => array('user_id' => array(-1)), 'filter' => array('chat_id' => $chat->id)));
    }

    erLhcoreClassChat::setTimeZoneByChat($chat);

    $formatted = array();
    foreach ($messages as $msg) {
        if ($msg->msg != ''){
            $formatted[] = '[' . date('H:i:s',$msg->time).'] '. ($msg->user_id == 0 ? htmlspecialchars($chat->nick) : ($msg->user_id == -1 ? 'System assistant' : htmlspecialchars($msg->name_support)) ).': '.htmlspecialchars($msg->msg);
        }
    }

    if (isset($_GET['system'])) {
        echo json_encode(array('result' => implode("\n",$formatted)));
        exit;
    }

    $tpl = erLhcoreClassTemplate::getInstance('lhchat/copymessages.tpl.php');
    $tpl->set('chat', $chat);
    $tpl->set('messages', implode("\n",$formatted));

    echo $tpl->fetch();
}

exit;

?>