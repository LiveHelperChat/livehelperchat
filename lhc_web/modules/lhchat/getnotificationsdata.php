<?php
header ( 'content-type: application/json; charset=utf-8' );
$itemsID = array();
$itemsTypes = array();
$type = 'pending_chat';
$notification_message_type = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Pending Chat');

$itemsGrouped = [];
$returnArray = array();

$validChatGroups = ['bot_chats','pending_chat','transfer_chat','transfer_chat_dep','unread_chat'];
$validMailGroups = ['pmails','amails'];

foreach ($Params['user_parameters_unordered']['id'] as $item) {
    if (is_numeric($item)) {
        $itemsGrouped[$type][] = (int)$item;
        if (in_array($type,$validChatGroups)) {
            $itemsTypes[$item] = $type;
            $itemsID[] = (int)$item;
        }
    } else {
        $type = $item;
    }    
}

$mails = [];

if (isset($itemsGrouped['pmails'])) {

    $itemsMail = erLhcoreClassModelMailconvConversation::getList(array(
        'filterin' => array(
            'id' => $itemsGrouped['pmails']
        )
    ));

    foreach ($itemsMail as $itemMail)
    {
        $mails[] = $itemMail->id;
        $returnArray[] = array(
            'nick' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','New mail') . ' - ' . $itemMail->from_address,
            'msg' => $itemMail->subject,
            'nt' => $itemMail->subject,
            'last_id_identifier' => 'pmails',
            'last_id' => $itemMail->id
        );
    }
}

if (isset($itemsGrouped['amails'])) {
    $itemsMail = erLhcoreClassModelMailconvConversation::getList(array(
        'filterin' => array(
            'id' => $itemsGrouped['amails']
        )
    ));

    foreach ($itemsMail as $itemMail)
    {
        if (!in_array($itemMail->id,$mails)){
            $returnArray[] = array(
                'nick' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Unresponded mail') . ' - ' . $itemMail->from_address,
                'msg' => $itemMail->subject,
                'nt' => $itemMail->subject,
                'last_id_identifier' => 'amails',
                'last_id' => $itemMail->id
            );
        }
    }
}


$items = [];

if (!empty($itemsID)){
    $items = erLhcoreClassChat::getList(array(
        'ignore_fields' => erLhcoreClassChat::$chatListIgnoreField,
        'filterin' => array(
            'id' => $itemsID
        )
    ));
}

foreach ($items as $item) {
    
    $nick = $item->nick;
    $department = (string)$item->department;
        
    if ($itemsTypes[$item->id] == 'unread_chat') {
        $notification_message_type = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Unread message');
    } elseif ($itemsTypes[$item->id] == 'pending_chat') {
        $notification_message_type = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Pending Chat');
    } elseif ($itemsTypes[$item->id] == 'bot_chats') {
        $notification_message_type = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Bot Chat');
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
    
    if ($type == 'active_chat' || $type == 'bot_chats') {
        $type = 'pending_chat';
    } elseif ($type == 'transfer_chat_dep') {
        $type = 'transfer_chat';
    }

    $titleParts = array_filter(array($notification_message_type, $nick, $department));

    // do not show notification if i'm not chat owner and it's already belongs to other user
    if ($item->user_id > 0 && $type != 'transfer_chat' && $item->user_id != $currentUser->getUserID()) {
        continue;
    }

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