<?php

$itemsID = array();
$itemsTypes = array();
$type = 'pending_chat';
$notification_message_type = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Pending Chat');

foreach ($Params['user_parameters_unordered']['id'] as $item) {
    if (is_numeric($item)) {
        if (!in_array($item, $itemsID)) {
            $itemsID[] = $item;
            $itemsTypes[$item] = $type;
        }
    } else {
        $type = $item;
    }    
}

$items = erLhcoreClassChat::getList(array(
    'ignore_fields' => erLhcoreClassChat::$chatListIgnoreField,
    'filterin' => array(
        'id' => $itemsID
    )
));

$returnArray = array();

foreach ($items as $item) {
    
    if ($itemsTypes[$item->id] == 'unread_chat') {
        $notification_message_type = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Unread message');
    } elseif ($itemsTypes[$item->id] == 'pending_chat') {
        $notification_message_type = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Pending Chat');
    } elseif ($itemsTypes[$item->id] == 'transfer_chat' || ($itemsTypes[$item->id] == 'transfer_chat_dep')) {
        $notification_message_type = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Transfer Chat');
    } elseif ($itemsTypes[$item->id] == 'active_chat') {
        $notification_message_type = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Assigned Chat');
    }
        
    $type = $itemsTypes[$item->id];
    
    if ($type == 'active_chat') {
        $type = 'pending_chat';
    } elseif ($type == 'transfer_chat_dep') {
        $type = 'transfer_chat';
    }
    
    $returnArray[] = array(
        'nick' => $notification_message_type . ' | ' . $item->nick . ' | ' . $item->department,
        'msg' => erLhcoreClassChat::getGetLastChatMessagePending($item->id),
        'nt' => $item->nick,
        'last_id_identifier' => $type,
        'last_id' => $item->id
    );
}

echo erLhcoreClassChat::safe_json_encode($returnArray);

exit();
?>