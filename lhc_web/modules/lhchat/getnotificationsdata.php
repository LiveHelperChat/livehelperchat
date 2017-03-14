<?php

$itemsID = array();
$itemsTypes = array();
$type = 'pending_chat';
$notification_message_type = 'Unread Chat';


foreach ($Params['user_parameters_unordered']['id'] as $item) {
    if (is_numeric($item)) {
        $itemsID[] = $item;
    } else {
        $type = $item;
    }
    $itemsTypes[$item] = $type;
}

$items = erLhcoreClassChat::getList(array('ignore_fields' => erLhcoreClassChat::$chatListIgnoreField, 'filterin' => array('id' => $itemsID)));
//echo print_r($itemsTypes);
$returnArray = array();
$lastChatNickTab = $lastChatNick = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Visitor');
$lastMessage = erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','New unread message');

foreach ($items as $item) {

    if ($itemsTypes[$item->id] == 'pending_chat'){
        $notification_message_type = 'Pending Chat';
    }elseif ($itemsTypes[$item->id] == 'transfer_chat') {
        $notification_message_type = 'Transfer Chat';
    }

    $lastChatNick = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat',$notification_message_type) . ' | ' .$item->nick . ' | ' . $item->department;
    $lastMessage = erLhcoreClassChat::getGetLastChatMessagePending($item->id);
    $lastChatNickTab = $item->nick;
    $lastChatUserId = $item->user_id;

    $returnArray[] = array(
        'nick' => $lastChatNick,
        'msg' => $lastMessage,
        'nt' => $lastChatNickTab,
        'uid' => $lastChatUserId,
        'last_id_identifier' => $itemsTypes[$item->id],
        'last_id' => $item->id,

    );
}

echo erLhcoreClassChat::safe_json_encode($returnArray);

exit;
?>
