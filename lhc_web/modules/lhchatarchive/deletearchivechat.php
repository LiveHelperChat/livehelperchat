<?php

if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
	die('Invalid CSFR Token');
	exit;
}

$archive = erLhcoreClassModelChatArchiveRange::fetch($Params['user_parameters']['archive_id']);
$archive->setTables();

$chat = erLhcoreClassModelChatArchive::fetch($Params['user_parameters']['chat_id']);
$chat->removeThis();

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.archive_deleted',array('chat' => & $chat));

erLhcoreClassModule::redirect('chatarchive/listarchivechats','/'.$archive->id);
exit;

?>