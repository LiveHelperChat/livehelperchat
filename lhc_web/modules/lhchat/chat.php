<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhchat/chat.tpl.php');

try {

    $chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);

    if ($chat->hash == $Params['user_parameters']['hash'])
    {
        $tpl->set('chat_id',$Params['user_parameters']['chat_id']);
        $tpl->set('hash',$Params['user_parameters']['hash']);
        $tpl->set('chat',$chat);

        // User online
        if ($chat->user_status != 0) {

        	$db = ezcDbInstance::get();
        	$db->beginTransaction();

	        	$chat->user_status = 0;
	        	$chat->support_informed = 1;

	        	// Log that user has joined the chat
	        	$msg = new erLhcoreClassModelmsg();
	        	$msg->msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/userjoined','User has joined the chat!');
	        	$msg->chat_id = $chat->id;
	        	$msg->user_id = -1; // System messages get's user_id -1

	        	$chat->last_user_msg_time = $msg->time = time();

	        	erLhcoreClassChat::getSession()->save($msg);

	        	// Set last message ID
	        	if ($chat->last_msg_id < $msg->id) {
	        		$chat->last_msg_id = $msg->id;
	        	}

	        	erLhcoreClassChat::getSession()->update($chat);

        	$db->commit();
        }

    } else {
        $tpl->setFile( 'lhchat/errors/chatnotexists.tpl.php');
    }

} catch(Exception $e) {
   $tpl->setFile('lhchat/errors/chatnotexists.tpl.php');
   
   // This is called then user closes chat widget
   // We mark session variable as user closed the chat
   CSCacheAPC::getMem()->setSession('chat_hash_widget',false);
}



$Result['content'] = $tpl->fetch();
$Result['pagelayout'] = 'userchat';

$Result['path'] = array(array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Chat started')))


?>