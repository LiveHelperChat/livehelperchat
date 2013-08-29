<?php

$chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);


$currentUser = erLhcoreClassUser::instance();

if ($currentUser->hasAccessTo('lhchat','deleteglobalchat') || ($currentUser->hasAccessTo('lhchat','deletechat') && $chat->user_id == $currentUser->getUserID()))
{
	$chat->removeThis();	
	CSCacheAPC::getMem()->removeFromArray('lhc_open_chats', $chat->id);
	
    echo json_encode(array('error' => 'false', 'result' => 'ok' ));
} else {
   echo json_encode(array('error' => 'true', 'result' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/deletechatadmin',"You do not have rights to delete a chat") ));
}

exit;

?>