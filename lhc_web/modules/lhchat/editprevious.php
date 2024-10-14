<?php

header('Content-type: application/json');

try {
    $chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);

    if ( erLhcoreClassChat::hasAccessToRead($chat) )
    {
        if (is_numeric($Params['user_parameters']['msg_id'])) {
            $lastMessageObj = erLhcoreClassModelmsg::fetch($Params['user_parameters']['msg_id']);
            if ($lastMessageObj instanceof erLhcoreClassModelmsg) {
                $lastMessage = $lastMessageObj->getState();
            }
        } else {
            $lastMessage = erLhcoreClassChat::getGetLastChatMessageEdit($chat->id, $currentUser->getUserID());
        }

		if (isset($lastMessage['msg']) && $lastMessage['chat_id'] == $chat->id) {
		    if (
                ($lastMessage['user_id'] == $currentUser->getUserID()) ||
                ($lastMessage['user_id'] == 0 && erLhcoreClassUser::instance()->hasAccessTo('lhchat','editpreviouvis')) ||
                ($lastMessage['user_id'] > 0 && erLhcoreClassUser::instance()->hasAccessTo('lhchat','editpreviousop'))
            ) {

                if ($lastMessage['user_id'] == $currentUser->getUserID()) {
                   if (!erLhcoreClassUser::instance()->hasAccessTo('lhchat','editpreviousall')) {
                       $lastMessageDirectly = erLhcoreClassChat::getGetLastChatMessageEdit($chat->id, $currentUser->getUserID());
                       if (!isset($lastMessageDirectly['id']) || $lastMessageDirectly['id'] != $lastMessage['id']) {
                           echo json_encode(array('error' => 't','result' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','You can edit only your last message!')));
                           exit;
                       }
                   }
                }

                $array = array();
                $array['id'] = $lastMessage['id'];
                $array['msg'] = preg_replace('#\[translation\](.*?)\[/translation\]#is', '', $lastMessage['msg']);
                $array['error'] = 'f';

                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.before_edit_previous_admin_returned', array('response' => & $array));

                echo json_encode($array);
            } else {
                echo json_encode(array('error' => 't','result' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','You can edit your own message!')));
            }
		} else {
			echo json_encode(array('error' => 't','result' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','No last message was found!')));
		}
	}
} catch (Exception $e) {
	echo json_encode(array('error' => 't', 'result' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Message could not be found!')));
}
exit;


?>