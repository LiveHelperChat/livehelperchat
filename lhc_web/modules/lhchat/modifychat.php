<?php

// Set new chat owner
$currentUser = erLhcoreClassUser::instance();
$currentUser->getUserID();
$chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);

// Chat can be closed only by owner
if ( erLhcoreClassChat::hasAccessToRead($chat) && $currentUser->hasAccessTo('lhchat','modifychat') ) {
  $tpl = erLhcoreClassTemplate::getInstance('lhchat/modifychat.tpl.php');
 
  if (ezcInputForm::hasPostData()) {
  
	  	$Errors = erLhcoreClassChatValidator::validateChatModify($chat);

	  	if (count($Errors) == 0) {	  		
	  		$chat->saveThis();

	  		erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.modified',array('chat' => & $chat, 'params' => $Params));

	  		$tpl->set('chat_updated',true);
	  	} else {
	  		$tpl->set('errors',$Errors);
	  	}
  }

  $tpl->set('chat',$chat);
  $Result['content'] = $tpl->fetch();
  $Result['pagelayout'] = 'popup';

} else {
	exit;
}

?>