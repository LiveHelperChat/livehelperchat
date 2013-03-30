<?php

$tpl = erLhcoreClassTemplate::getInstance('lhchat/transferchat.tpl.php');

$tpl->set('chat',erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']));
$currentUser = erLhcoreClassUser::instance();
$tpl->set('user_id',$currentUser->getUserID());

print $tpl->fetch();
exit;

?>