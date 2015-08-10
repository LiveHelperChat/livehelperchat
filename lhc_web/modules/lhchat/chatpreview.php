<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhchat/chatpreview.tpl.php');

try {
    $chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);
    if ($chat->hash == $Params['user_parameters']['hash'] && ($chat->status == erLhcoreClassModelChat::STATUS_ACTIVE_CHAT || erLhcoreClassChat::canReopen($chat,true) || ($chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT && $chat->time > time()-1800))) {
        erLhcoreClassChat::setTimeZoneByChat($chat);
        $tpl->set('chat',$chat);
    } else {
        $tpl->setFile( 'lhchat/errors/chatnotexists.tpl.php');
    }

} catch(Exception $e) {
   $tpl->setFile('lhchat/errors/chatnotexists.tpl.php');
}

echo $tpl->fetch();
exit;

?>