<?php


$content = 'false';
$content_status = 'false';

if (isset($_POST['chats']) && is_array($_POST['chats']) && count($_POST['chats']) > 0)
{
    $ReturnMessages = array();
    $ReturnStatuses = array();
    
    $tpl = new erLhcoreClassTemplate( 'lhchat/syncadmin.tpl.php');
    
    foreach ($_POST['chats'] as $chat_id_list)
    {   
        list($chat_id,$MessageID) = explode(',',$chat_id_list);
            
        $Chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $chat_id);
        
        if ( erLhcoreClassChat::hasAccessToRead($Chat) )
        {
            $Messages = erLhcoreClassChat::getPendingAdminMessages($chat_id,$MessageID);       
            if (count($Messages) > 0) 
            {   
                $tpl->set('messages',$Messages);
                $tpl->set('chat',$Chat);
                 
                $LastMessageIDs = array_pop($Messages);   
                      
                $templateResult = $tpl->fetch();  
                                          
                $ReturnMessages[] = array('chat_id' => $chat_id, 'content' => $templateResult, 'message_id' => $LastMessageIDs['id']);
            } else {
                // User left chat
                if ($Chat->support_informed == 0 && $Chat->user_status == 1)
                {
                    $Chat->support_informed = 1;
                    erLhcoreClassChat::getSession()->update($Chat);                    
                    $ReturnMessages[] = array('chat_id' => $chat_id, 'content' => $tpl->fetch( 'lhchat/userleftchat.tpl.php'), 'message_id' => $MessageID);
                } elseif ($Chat->support_informed == 0 && $Chat->user_status == 0) {
                    $Chat->support_informed = 1;
                    erLhcoreClassChat::getSession()->update($Chat);
                    $ReturnMessages[] = array('chat_id' => $chat_id, 'content' => $tpl->fetch( 'lhchat/userjoinged.tpl.php'), 'message_id' => $MessageID);
                }
            }
            
            if ($Chat->is_user_typing) {
                $ReturnStatuses[] = array('chat_id' => $chat_id,'tp' => 'true');
            } else {
                $ReturnStatuses[] = array('chat_id' => $chat_id,'tp' => 'false');
            }             
        }
        
    }
    
    if (count($ReturnMessages) > 0) $content = $ReturnMessages;
    
    if (count($ReturnStatuses) > 0) $content_status = $ReturnStatuses;
}



echo json_encode(array('error' => 'false','result_status' => $content_status, 'result' => $content ));
exit;
?>