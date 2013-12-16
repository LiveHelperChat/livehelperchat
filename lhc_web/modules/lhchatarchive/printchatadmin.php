<?php

$tpl = erLhcoreClassTemplate::getInstance('lhchat/printchatadmin.tpl.php');

$archive = erLhcoreClassModelChatArchiveRange::fetch($Params['user_parameters']['archive_id']);
$archive->setTables();

$chat = erLhcoreClassModelChatArchive::fetch($Params['user_parameters']['chat_id']);

if ( erLhcoreClassChat::hasAccessToRead($chat) )
{
	$tpl->set('chat',$chat);
	$tpl->set('messages', erLhcoreClassChat::getList(array('limit' => 1000,'filter' => array('chat_id' => $chat->id)),'erLhcoreClassModelChatArchiveMsg',erLhcoreClassModelChatArchiveRange::$archiveMsgTable));
} else {
    $tpl->setFile( 'lhchat/errors/adminchatnopermission.tpl.php');
}

$Result['content'] = $tpl->fetch();
$Result['pagelayout'] = 'print';


?>