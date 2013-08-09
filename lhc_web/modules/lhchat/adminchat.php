<?php


$tpl = erLhcoreClassTemplate::getInstance('lhchat/adminchat.tpl.php');

$chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);
$tpl->set('chat',$chat);

if ( erLhcoreClassChat::hasAccessToRead($chat) )
{
    // If status is pending change status to active
    if ($chat->status == erLhcoreClassModelChat::STATUS_PENDING_CHAT) {
    	$chat->status = erLhcoreClassModelChat::STATUS_ACTIVE_CHAT;
    	$chat->wait_time = time() - $chat->time;
    }

    if ($chat->user_id == 0)
    {
        $currentUser = erLhcoreClassUser::instance();
        $chat->user_id = $currentUser->getUserID();
    }

    $chat->support_informed = 1;
    $chat->has_unread_messages = 0;
    erLhcoreClassChat::getSession()->update($chat);

} else {
    $tpl->setFile( 'lhchat/errors/adminchatnopermission.tpl.php');
    $tpl->set('show_close_button',true);

}

echo $tpl->fetch();
exit;

?>