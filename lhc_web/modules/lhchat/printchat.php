<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhchat/printchat.tpl.php');

try {
    $chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);
    if ($chat->hash == $Params['user_parameters']['hash'] && ($chat->status == erLhcoreClassModelChat::STATUS_ACTIVE_CHAT || erLhcoreClassChat::canReopen($chat,true))) {
        $tpl->set('chat',$chat);
    } else {
        $tpl->setFile( 'lhchat/errors/chatnotexists.tpl.php');
    }

} catch(Exception $e) {
   $tpl->setFile('lhchat/errors/chatnotexists.tpl.php');

   // This is called then user closes chat widget
   // We mark session variable as user closed the chat
   CSCacheAPC::getMem()->setSession('chat_hash_widget',false);
}

$Result['content'] = $tpl->fetch();
$Result['pagelayout'] = 'print';

?>