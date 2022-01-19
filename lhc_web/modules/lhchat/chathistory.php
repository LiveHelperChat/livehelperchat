<?php

session_write_close();

$tpl = erLhcoreClassTemplate::getInstance('lhchat/chathistory.tpl.php');

$chat = erLhcoreClassChat::getSession()->load('erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);

if ( erLhcoreClassChat::hasAccessToRead($chat) ) {

    $commandResponse = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.chathistory', array('chat' => $chat));

    if (isset($commandResponse['processed']) && $commandResponse['processed'] == true) {
        $previousChats = $commandResponse['previous_chats'];
        $nextChats = $commandResponse['next_chats'];
    } else {
        $previousChats = array_reverse(erLhcoreClassModelChat::getList(['sort' => 'id DESC', 'limit' => 10, 'filtergt' => ['cls_time' => $chat->time], 'filterlt' => ['id' => $chat->id], 'filter' => ['user_id' => $chat->user_id]]));
        $nextChats = erLhcoreClassModelChat::getList(['sort' => 'id ASC', 'limit' => 10, 'filterlt' => ['time' => $chat->cls_time], 'filtergt' => ['id' => $chat->id], 'filter' => ['user_id' => $chat->user_id]]);
    }

    $tpl->setArray([
        'chatOriginal' => $chat,
        'previousChats' => $previousChats,
        'nextChats' => $nextChats,
        'activeChats' =>  erLhcoreClassModelChat::getList(['sort' => 'id DESC', 'limit' => 20,'filterin' => ['status' => [erLhcoreClassModelChat::STATUS_ACTIVE_CHAT, erLhcoreClassModelChat::STATUS_PENDING_CHAT]], 'filter' => ['user_id' => $chat->user_id]])
    ]);

} else {
    $tpl->setFile( 'lhchat/errors/adminchatnopermission.tpl.php');
}

$Result['content'] = $tpl->fetch();
$Result['pagelayout'] = 'popup';


?>