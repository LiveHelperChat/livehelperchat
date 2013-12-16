<?php

$tpl = erLhcoreClassTemplate::getInstance('lhchat/printchatadmin.tpl.php');
$chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);

if ( erLhcoreClassChat::hasAccessToRead($chat) )
{
	$tpl->set('chat',$chat);
	$tpl->set('messages',erLhcoreClassModelmsg::getList(array('limit' => 1000,'sort' => 'id ASC','filter' => array('chat_id' => $chat->id))));
} else {
    $tpl->setFile( 'lhchat/errors/adminchatnopermission.tpl.php');
}

$Result['content'] = $tpl->fetch();
$Result['pagelayout'] = 'print';


?>