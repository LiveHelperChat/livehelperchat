<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhchat/printchat.tpl.php');

if ((int)erLhcoreClassModelChatConfig::fetch('disable_print')->current_value == 1){
    exit;
}

try {
    $chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);
    if ($chat->hash == $Params['user_parameters']['hash'] && ($chat->status == erLhcoreClassModelChat::STATUS_ACTIVE_CHAT || erLhcoreClassChat::canReopen($chat,true) || ($chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT && $chat->time > time()-1800))) {
        $errors = array();
        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.before_print',array('chat' => & $chat, 'errors' => & $errors));

        if(empty($errors)) {
            erLhcoreClassChat::setTimeZoneByChat($chat);
            $tpl->set('chat',$chat);
        } else {
            $tpl->set('errors',$errors);
            $tpl->setFile('lhkernel/validation_error.tpl.php');
        }

    } else {
        $tpl->setFile( 'lhchat/errors/chatnotexists.tpl.php');
    }

} catch(Exception $e) {
    $tpl->setFile('lhchat/errors/chatnotexists.tpl.php');
}

$Result['content'] = $tpl->fetch();
$Result['pagelayout'] = 'print';

?>