<?php
$chatsOpen = CSCacheAPC::getMem()->getArray('lhc_open_chats');
if (!empty($chatsOpen)){
	$chats = erLhcoreClassChat::getList(array('filterin' => array('id' => $chatsOpen)));

	// Delete any old chat if it exists
	$deleteKeys = array_diff($chatsOpen, array_keys($chats));
	foreach ($deleteKeys as $chat_id) {
		CSCacheAPC::getMem()->removeFromArray('lhc_open_chats', $chat_id);
	}

	foreach ($chats as $chat ){
		if (erLhcoreClassChat::hasAccessToRead($chat)){
			echo "lhinst.startChat('$chat->id',$('#tabs'),'".erLhcoreClassDesign::shrt($chat->nick,10,'...',30,ENT_QUOTES)."');";
		} else {
			CSCacheAPC::getMem()->removeFromArray('lhc_open_chats', $chat->id);
		}
	}
}
?>