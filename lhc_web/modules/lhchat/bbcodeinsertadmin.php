<?php 

$tpl = erLhcoreClassTemplate::getInstance( 'lhchat/bbcodeinsertadmin.tpl.php');
$chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);
$tpl->set('chat',$chat);
echo $tpl->fetch();
exit;
