<?php

try {
    $chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);
} catch (Exception $e) {
    $chat = false;
}

if (is_object($chat) && $chat->hash == $Params['user_parameters']['hash'])
{
	if ($chat->user_status != 1) {

		$db = ezcDbInstance::get();
		$db->beginTransaction();

		    // User closed chat
		    $chat->user_status = 1;
		    $chat->support_informed = 1;
		    $chat->user_typing = time()-5;// Show for shorter period these status messages
		    $chat->is_user_typing = 1;
		    $chat->user_closed_ts = time();
		    $chat->user_typing_txt = htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/userleftchat','User has left the chat!'),ENT_QUOTES);

		    erLhcoreClassChat::getSession()->update($chat);

	    $db->commit();
	}


}

echo json_encode(array('error' => 'false', 'result' => 'ok'));
exit;

?>