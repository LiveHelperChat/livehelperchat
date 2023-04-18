<?php


$tpl = erLhcoreClassTemplate::getInstance('lhchatarchive/previewchat.tpl.php');

$archive = erLhcoreClassModelChatArchiveRange::fetch($Params['user_parameters']['archive_id']);
$archive->setTables();

$chat = erLhcoreClassModelChatArchive::fetch($Params['user_parameters']['chat_id']);

if ( erLhcoreClassChat::hasAccessToRead($chat) )
{
    $tpl->set('keyword',isset($_GET['keyword']) ? (string)$_GET['keyword'] : '');
	$tpl->set('chat',$chat);
	$tpl->set('archive',$archive);
} else {
	$tpl->setFile( 'lhchat/errors/adminchatnopermission.tpl.php');
}

echo $tpl->fetch();
exit;

?>