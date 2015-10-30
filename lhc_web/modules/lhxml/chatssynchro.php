<?php

$currentUser = erLhcoreClassUser::instance();
if (!$currentUser->isLogged() && !$currentUser->authenticate($_POST['username'],$_POST['password']))
{
    exit;
}

//erLhcoreClassLog::write(print_r($_POST,true));
//[chats] => 2|5,2,5,2;8|0,5,2,0,5,2
//$_POST['chats']   = '6|5,1,4;8|0,5,2,0,5,2';

if ($currentUser->isLogged() && isset($_POST['chats']))
{
    $arrayReturn = array();

    $chats = explode(';',$_POST['chats']);

    foreach ($chats as $chatContent)
    {
        list($chat_id,$msgIDs) = explode('|',$chatContent);

        $chatsMessages = array();
        $chatStatusMessage = '';

        // Get messages from with needs to synchronise
        $masgIDArray  = array_unique(explode(',',$msgIDs));

        // From this messages we can fetch msg's
        $minMessageID = min($masgIDArray);

        $Chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $chat_id);

        if ( erLhcoreClassChat::hasAccessToRead($Chat) )
        {
            if ( ($Chat->last_msg_id > (int)$minMessageID) && count($Messages = erLhcoreClassChat::getPendingMessages($chat_id,(int)$minMessageID)) > 0)
            {
                foreach ($Messages as $msg)
                {
                    foreach ($masgIDArray as $msgID)
                    {
                        if ($msgID < $msg['id']) $chatsMessages[$msgID][] = $msg;
                    }
                }
                
                if ($Chat->has_unread_messages == 1 || $Chat->unread_messages_informed == 1 ) {
                	$Chat->has_unread_messages = 0;
                	$Chat->unread_messages_informed = 0;
                	$Chat->saveThis();
                }                
                
            } else {
            	if ($Chat->is_user_typing) {
            		$chatStatusMessage = $Chat->user_typing_txt;
            	} elseif ($Chat->user_status == 1) {
                    $chatStatusMessage = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/userleftchat','Visitor has left the chat!');
                } elseif ($Chat->user_status == 0) {
                    $chatStatusMessage = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/userjoined','Visitor has joined the chat!');
                }
            }
        }

        $arrayReturn[$chat_id]['messages'] = $chatsMessages;
        $arrayReturn[$chat_id]['chat_status'] = $chatStatusMessage;
    }
    echo json_encode(array("error" => false,'result' => $arrayReturn));
} else {
    echo json_encode(array("error" => true));
}



exit;
?>