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

		if (isset($lastMessage['msg'])) {
		    if ($lastMessage['user_id'] == $currentUser->getUserID()) {
                $array = array();
                $array['id'] = $lastMessage['id'];
                $array['msg'] = preg_replace('#\[translation\](.*?)\[/translation\]#is', '', $lastMessage['msg']);
                $array['error'] = 'f';

                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.before_edit_previous_admin_returned',array('response' => & $array));

                echo json_encode($array);
            } else {
                echo json_encode(array('error' => 't','result' => 'You can edit your own message!'));
            }
		} else {
			echo json_encode(array('error' => 't','result' => 'No last message was found!'));
		}
	}
} catch (Exception $e) {
	echo json_encode(array('error' => 't', 'result' => 'Message could not be found!'));
}
exit;


?>