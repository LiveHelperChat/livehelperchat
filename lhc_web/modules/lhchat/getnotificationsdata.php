<?php
header ( 'content-type: application/json; charset=utf-8' );
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
    
    $nick = $item->nick;
    $department = (string)$item->department;
        
    if ($itemsTypes[$item->id] == 'unread_chat') {
        $notification_message_type = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Unread message');
    } elseif ($itemsTypes[$item->id] == 'pending_chat') {
        $notification_message_type = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Pending Chat');
    } elseif ($itemsTypes[$item->id] == 'transfer_chat' || ($itemsTypes[$item->id] == 'transfer_chat_dep')) {
        
        if ($item->status == erLhcoreClassModelChat::STATUS_OPERATORS_CHAT) {
            $notification_message_type = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','New message from operator');
            $nick = '';
            $department = '';
        } else {
            $notification_message_type = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Transfer Chat');
        }
        
    } elseif ($itemsTypes[$item->id] == 'active_chat') {
        $notification_message_type = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Assigned Chat');
    }
    
    $type = $itemsTypes[$item->id];
    
    if ($type == 'active_chat') {
        $type = 'pending_chat';
    } elseif ($type == 'transfer_chat_dep') {
        $type = 'transfer_chat';
    }

    $titleParts = array_filter(array($notification_message_type, $nick, $department));

    $returnArray[] = array(
        'nick' => implode(' | ', $titleParts),
        'msg' => erLhcoreClassChat::getGetLastChatMessagePending($item->id),
        'nt' => $item->nick,
        'last_id_identifier' => $type,
        'last_id' => $item->id
    );
}

echo erLhcoreClassChat::safe_json_encode($returnArray);

exit();
?>