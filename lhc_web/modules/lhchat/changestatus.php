<?php

$tpl = erLhcoreClassTemplate::getInstance('lhchat/changestatus.tpl.php');
$chat = erLhcoreClassChat::getSession()->load('erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);
$tpl->set('chat', $chat);

if (erLhcoreClassChat::hasAccessToRead($chat)) {
    $currentUser = erLhcoreClassUser::instance();
    
    if (isset($_POST['ChatStatus']) && is_numeric($_POST['ChatStatus'])) {
        
        $userData = $currentUser->getUserData();
        $changeStatus = (int) $_POST['ChatStatus'];
        
        if (in_array($changeStatus, array(
            erLhcoreClassModelChat::STATUS_ACTIVE_CHAT,
            erLhcoreClassModelChat::STATUS_PENDING_CHAT,
            erLhcoreClassModelChat::STATUS_CLOSED_CHAT,
            erLhcoreClassModelChat::STATUS_CHATBOX_CHAT,
            erLhcoreClassModelChat::STATUS_OPERATORS_CHAT
        ))) {
            
            erLhcoreClassChatHelper::changeStatus(array(
                'user' => $userData,
                'chat' => $chat,
                'status' => $changeStatus,
                'allow_close_remote' => $currentUser->hasAccessTo('lhchat', 'allowcloseremote')
            ));
            
            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.data_changed',array('chat' => & $chat, 'user' => $currentUser));
            
            echo json_encode(array(
                'error' => 'false'
            ));
            exit();
        } else {
            echo json_encode(array(
                'error' => 'true',
                'result' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat', 'Invalid chat status')
            ));
            exit();
        }
    }
}

print $tpl->fetch();
exit();

?>