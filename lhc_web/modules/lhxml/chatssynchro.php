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
            $Messages = erLhcoreClassChat::getPendingAdminMessages($chat_id,$minMessageID);

            if (count($Messages) > 0)
            {          
                foreach ($Messages as $msg)
                {
                    foreach ($masgIDArray as $msgID)
                    {
                        if ($msgID < $msg['id']) $chatsMessages[$msgID][] = $msg;
                    }
                }  
            } else {
                // User left chat
                if ($Chat->support_informed == 0 && $Chat->user_status == 1)
                {
                    $Chat->support_informed = 1;
                    erLhcoreClassChat::getSession()->update($Chat); 
                    $chatStatusMessage = 'User left chat';                   
                    //$ReturnMessages[] = array('chat_id' => $chat_id, 'content' => $tpl->fetch( 'lhchat/userleftchat.tpl.php'), 'message_id' => $MessageID);
                } elseif ($Chat->support_informed == 0 && $Chat->user_status == 0) {
                    $Chat->support_informed = 1;
                    erLhcoreClassChat::getSession()->update($Chat);
                    $chatStatusMessage = 'User joined chat';
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