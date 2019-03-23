<?php

$tpl = erLhcoreClassTemplate::getInstance('lhchat/adminchat.tpl.php');

$db = ezcDbInstance::get();
$db->beginTransaction();

$chat = erLhcoreClassModelChat::fetchAndLock($Params['user_parameters']['chat_id']);

if ($chat instanceof erLhcoreClassModelChat && erLhcoreClassChat::hasAccessToRead($chat) )
{
	$userData = $currentUser->getUserData();

	if ($userData->invisible_mode == 0 && erLhcoreClassChat::hasAccessToWrite($chat)) {
	    try {

            if ($chat->user_id != $userData->id && !$currentUser->hasAccessTo('lhchat','open_all')) {
                throw new Exception('You do not have permission to open all pending chats.');
            }

	        $db->beginTransaction();
	        
    		$operatorAccepted = false;
    		$chatDataChanged = false;
    		
    	    if ($chat->user_id == 0 && $chat->status != erLhcoreClassModelChat::STATUS_BOT_CHAT && $chat->status != erLhcoreClassModelChat::STATUS_CLOSED_CHAT) {
    	        $currentUser = erLhcoreClassUser::instance();
    	        $chat->user_id = $currentUser->getUserID();	     
    	        $chatDataChanged = true;
    	    }
    	    
    	    // If status is pending change status to active
    	    if ($chat->status == erLhcoreClassModelChat::STATUS_PENDING_CHAT) {
    	    	$chat->status = erLhcoreClassModelChat::STATUS_ACTIVE_CHAT;

    	    	$chat->wait_time = time() - ($chat->pnd_time > 0 ? $chat->pnd_time : $chat->time);
    	    	$chat->user_id = $currentUser->getUserID();
    	    	
    	    	// User status in event of chat acceptance
    	    	$chat->usaccept = $userData->hide_online;
    	    	
    	    	$operatorAccepted = true;
    	    	$chatDataChanged = true;
    	    }

    	    // Check does chat transfer record exists if operator opened chat directly
    	    if ($chat->transfer_uid > 0) {
                erLhcoreClassTransfer::handleTransferredChatOpen($chat, $currentUser->getUserID());
            }

    	    if ($chat->support_informed == 0 || $chat->has_unread_messages == 1 ||  $chat->unread_messages_informed == 1) {
    	    	$chatDataChanged = true;
    	    }
    	    
    	    $tpl->set('arg', $Params['user_parameters_unordered']['arg']);
    	    
    	    // Store who has acceped a chat so other operators will be able easily indicate this
    	    if ($operatorAccepted == true) {
    	    	         	        
    	        $msg = new erLhcoreClassModelmsg();
    	        $msg->msg = (string)$currentUser->getUserData(true)->name_support.' '.erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','has accepted the chat!');
    	        $msg->chat_id = $chat->id;
    	        $msg->user_id = -1;
    	        $msg->time = time();
    	        	       
    	        if ($chat->last_msg_id < $msg->id) {
    	            $chat->last_msg_id = $msg->id;
    	        }
    
    	        erLhcoreClassChat::getSession()->save($msg);
    	    }
    	    
    	    // Update general chat attributes
            if ($chat->user_id == $currentUser->getUserID()) {
                $chat->support_informed = 1;
                $chat->has_unread_messages = 0;
                $chat->unread_messages_informed = 0;
            }

    	    if ($chat->unanswered_chat == 1 && ($chat->user_status_front == 0 || $chat->user_status_front == 2))
    	    {
    	        $chat->unanswered_chat = 0;
    	    }

    	    erLhcoreClassChat::getSession()->update($chat);
    	    
    	    $db->commit();
    	    
    	    session_write_close();

    	    if ($chatDataChanged == true) {
    	    	erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.data_changed',array('chat' => & $chat,'user' => $currentUser));
    	    }

    	    if ($operatorAccepted == true) {
    	    	erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.accept',array('chat' => & $chat,'user' => $currentUser));	    	
    	    	erLhcoreClassChat::updateActiveChats($chat->user_id);	
    
    	    	if ($chat->department !== false) {
    	    	    erLhcoreClassChat::updateDepartmentStats($chat->department);
    	    	}
    	    	
    	    	erLhcoreClassChatWorkflow::presendCannedMsg($chat);
    	    	$options = $chat->department->inform_options_array;
    	    	erLhcoreClassChatWorkflow::chatAcceptedWorkflow(array('department' => $chat->department,'options' => $options),$chat);
    	    };

    	    $db->commit();
    	    
    	    $tpl->set('chat',$chat);
            $tpl->set('canEditChat',true);

    	    echo $tpl->fetch();
    	        	    
	    } catch (Exception $e) {
	        $db->rollback();

            $tpl->setFile( 'lhchat/errors/adminchatnopermission.tpl.php');
            $tpl->set('show_close_button',true);
            $tpl->set('auto_close_dialog',true);
            $tpl->set('chat_id',(int)$Params['user_parameters']['chat_id']);
            $tpl->set('chat',$chat);
            echo $tpl->fetch();
            exit;
	    }
	} else {
	    $tpl->set('canEditChat',erLhcoreClassChat::hasAccessToWrite($chat));
	    $tpl->set('chat',$chat);
	    echo $tpl->fetch();
	}

	exit;

} else {
    $tpl->setFile( 'lhchat/errors/adminchatnopermission.tpl.php');
    $tpl->set('show_close_button',true);
    $tpl->set('auto_close_dialog',true);
    $tpl->set('chat_id',(int)$Params['user_parameters']['chat_id']);
    echo $tpl->fetch();
    exit;
}



?>