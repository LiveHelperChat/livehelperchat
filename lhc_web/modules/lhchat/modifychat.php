<?php

// Set new chat owner
$currentUser = erLhcoreClassUser::instance();
$currentUser->getUserID();

$chat = erLhcoreClassModelChat::fetch($Params['user_parameters']['chat_id']);

// Chat can be closed only by owner
if ( erLhcoreClassChat::hasAccessToRead($chat) && $currentUser->hasAccessTo('lhchat','modifychat') ) {
  $tpl = erLhcoreClassTemplate::getInstance('lhchat/modifychat.tpl.php');

  if (ezcInputForm::hasPostData()) {

        $db = ezcDbInstance::get();
        $db->beginTransaction();

        $chat->syncAndLock();

        $chatOriginal = clone $chat;
	  	$Errors = erLhcoreClassChatValidator::validateChatModify($chat);

	  	if (count($Errors) == 0) {

	  		if ($chat->nick != $chatOriginal->nick) {
                $msg = new erLhcoreClassModelmsg();
                $msg->msg = (string)$currentUser->getUserData(true)->name_support . ' ' . erTranslationClassLhTranslation::getInstance()->getTranslation('chat/closechatadmin', 'changed visitor nick from').' "' . $chatOriginal->nick .'" '.erTranslationClassLhTranslation::getInstance()->getTranslation('chat/closechatadmin', 'to') . ' "' . $chat->nick .'"';
                $msg->chat_id = $chat->id;
                $msg->user_id = - 1;
                $msg->time = time();
                $msg->saveThis();
                
                $chat->last_msg_id = $msg->id;
            }

            $chat->saveThis();

	  		erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.modified', array('chat' => & $chat, 'params' => $Params));

	  		$tpl->set('chat_updated',true);
	  	} else {
	  		$tpl->set('errors',$Errors);
	  	}

	  	$db->commit();
  }

  $tpl->set('pos',$Params['user_parameters_unordered']['pos']);
  $tpl->set('chat',$chat);
  $Result['content'] = $tpl->fetch();
  $Result['pagelayout'] = 'popup';

} else {
	exit;
}

?>