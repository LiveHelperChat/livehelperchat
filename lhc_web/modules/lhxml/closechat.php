<?php

$currentUser = erLhcoreClassUser::instance();
if (!$currentUser->isLogged() && !$currentUser->authenticate($_POST['username'],$_POST['password']))
{
    exit;
}


$chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);
// Chat can be closed only by owner
if ($chat->user_id == $currentUser->getUserID() || $currentUser->hasAccessTo('lhchat','allowcloseremote'))
{
	if ($chat->status != erLhcoreClassModelChat::STATUS_CLOSED_CHAT) {

	    $chat->status = erLhcoreClassModelChat::STATUS_CLOSED_CHAT;
	    $chat->chat_duration = erLhcoreClassChat::getChatDurationToUpdateChatID($chat);
        $chat->cls_time = time();
	    $userData = $currentUser->getUserData(true);

        $nickFrom = (string)$userData->name_support;
        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.get_nick_alias', array('user_id' => $userData->id, 'nick' => & $nickFrom));

	    $msg = new erLhcoreClassModelmsg();
	    $msg->msg = (string)$nickFrom.' '.erTranslationClassLhTranslation::getInstance()->getTranslation('chat/closechatadmin','has closed the chat!');
	    $msg->chat_id = $chat->id;
	    $msg->user_id = -1;

	    $chat->last_user_msg_time = $msg->time = time();

	    erLhcoreClassChat::getSession()->save($msg);

	    $chat->updateThis();
	    
	    erLhcoreClassChat::updateActiveChats($chat->user_id);
	    
	    // Execute callback for close chat
	    erLhcoreClassChat::closeChatCallback($chat,$userData);
	    
	    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.desktop_client_closed',array('chat' => & $chat));
	}    
}

exit;
?>