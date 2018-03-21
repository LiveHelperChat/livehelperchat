<?php

header('Content-Type: application/json');

$items = array();

$chat = erLhcoreClassModelChat::fetchAndLock($Params['user_parameters']['chat_id']);

if ( erLhcoreClassChat::hasAccessToRead($chat) ) {

    $messages = erLhcoreClassChat::getChatMessages($chat->id, erLhcoreClassChat::$limitMessages);

    $dataPrevious = erLhcoreClassChatWorkflow::hasPreviousChats(array(
        'messages' => $messages,
        'chat' => $chat,
    ));

    if ($dataPrevious['has_messages'] == true) {
        $items[] = array (
            'selector' => '#messagesBlock-' . $chat->id.' > .load-prev-btn',
            'action' => 'show',
            'attr' => array(
                'chat-original-id' => $chat->id,
                'chat-id' => $dataPrevious['chat_history']->id,
                'message-id' => $dataPrevious['message_id']
            )
        );
    } else {
        $items[] = array (
            'selector' => '#messagesBlock-' . $chat->id.' > .load-prev-btn',
            'action' => 'hide'
        );
    }
}



echo json_encode(array('error' => true, 'items' => $items));
exit;
?>