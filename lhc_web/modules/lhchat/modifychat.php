<?php

// Set new chat owner
$currentUser = erLhcoreClassUser::instance();
$currentUser->getUserID();

$chat = erLhcoreClassModelChat::fetch($Params['user_parameters']['chat_id']);

$tpl = erLhcoreClassTemplate::getInstance('lhchat/modifychat.tpl.php');

if ( erLhcoreClassChat::hasAccessToRead($chat) && $currentUser->hasAccessTo('lhchat','modifychatcore') ) {
    if (ezcInputForm::hasPostData() && isset($_POST['UpdateChatCore'])) {

        $db = ezcDbInstance::get();
        $db->beginTransaction();

        $chat->syncAndLock();

        $chatOriginal = clone $chat;
        $Errors = erLhcoreClassChatValidator::validateChatModifyCore($chat);

        if (count($Errors) == 0) {

            if ($chat->dep_id != $chatOriginal->dep_id) {
                $msg = new erLhcoreClassModelmsg();
                $msg->msg = (string)$currentUser->getUserData(true)->name_support . ' ' . erTranslationClassLhTranslation::getInstance()->getTranslation('chat/closechatadmin', 'changed chat department from') . ' "' . $chatOriginal->department . '" ' . erTranslationClassLhTranslation::getInstance()->getTranslation('chat/closechatadmin', 'to') . ' "' . $chat->department . '"';
                $msg->chat_id = $chat->id;
                $msg->user_id = - 1;
                $msg->time = time();
                $msg->saveThis();

                erLhAbstractModelAutoResponder::updateAutoResponder($chat);

                $chat->last_msg_id = $msg->id;
            }

            $chat->saveThis(array('ignore' => array('status')));

            // Update department and user stats
            if ($chat->dep_id != $chatOriginal->dep_id) {
                erLhcoreClassChat::updateDepartmentStats($chat->department);
                erLhcoreClassChat::updateActiveChats($chat->user_id);
            }

            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.modified', array('chat' => & $chat, 'params' => $Params));

            $tpl->set('chat_updated',true);
        } else {
            $tpl->set('errors',$Errors);
        }

        $db->commit();
    }
}

// Chat can be closed only by owner
if ( erLhcoreClassChat::hasAccessToRead($chat) && $currentUser->hasAccessTo('lhchat','modifychat') ) {

  if (ezcInputForm::hasPostData() && isset($_POST['UpdateChat'])) {

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

            $userInstance = $chat->online_user;

            if ($userInstance instanceof erLhcoreClassModelChatOnlineUser) {
                $attr = $userInstance->online_attr_system_array;
                // Update informing options for online visitor
                if (isset($_POST['informReturn'])) {

                    if (!isset($attr['lhc_ir']) || !is_array($attr['lhc_ir'])) {
                        $attr['lhc_ir'] = array();
                    }

                    $attr['lhc_ir'][] = $currentUser->getUserData(true)->id;

                    $userInstance->online_attr_system = json_encode($attr);
                    $userInstance->online_attr_system_array = $attr;
                    $userInstance->saveThis();
                    
                } elseif (isset($userInstance->online_attr_system_array['lhc_ir'])) {

                    $index = array_search($currentUser->getUserData(true)->id,$userInstance->online_attr_system_array['lhc_ir']);

                    if ($index !== false) {
                        unset($attr['lhc_ir'][$index]);

                        if (empty($attr['lhc_ir'])) {
                            unset($attr['lhc_ir']);
                        }

                        $userInstance->online_attr_system = json_encode($attr);
                        $userInstance->online_attr_system_array = $attr;
                        $userInstance->saveThis();
                    }
                }
            }

            if ($userInstance instanceof erLhcoreClassModelChatOnlineUser && $chat->nick != erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Visitor')) {
                $onlineAttr = $userInstance->online_attr_system_array;
                $onlineAttr['username'] = $chat->nick;
                $userInstance->online_attr_system = json_encode($onlineAttr);
                $userInstance->saveThis();
            }

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