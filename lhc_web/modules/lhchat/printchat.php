<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhchat/printchat.tpl.php');

if ((int)erLhcoreClassModelChatConfig::fetch('disable_print')->current_value == 1){
	exit;
}

try {
    $chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);
    if ($chat->hash == $Params['user_parameters']['hash'] && ($chat->status == erLhcoreClassModelChat::STATUS_ACTIVE_CHAT || erLhcoreClassChat::canReopen($chat,true))) {
        $tpl->set('chat',$chat);
    } else {
        $tpl->setFile( 'lhchat/errors/chatnotexists.tpl.php');
    }

} catch(Exception $e) {
   $tpl->setFile('lhchat/errors/chatnotexists.tpl.php');
}

$Result['content'] = $tpl->fetch();
$Result['pagelayout'] = 'print';

?>