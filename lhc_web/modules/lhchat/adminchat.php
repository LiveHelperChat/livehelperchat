<?php


$tpl = new erLhcoreClassTemplate('lhchat/adminchat.tpl.php');

$chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);
$tpl->set('chat',$chat);

if ( erLhcoreClassChat::hasAccessToRead($chat) )
{
    // Status active
    $chat->status = 1;
    
    if ($chat->user_id == 0)
    {
        $currentUser = erLhcoreClassUser::instance();    
        $chat->user_id = $currentUser->getUserID();
    }
    
    erLhcoreClassChat::getSession()->update($chat);    

} else {
    $tpl->setFile( 'lhchat/errors/adminchatnopermission.tpl.php');
}

echo $tpl->fetch();
exit;

?>