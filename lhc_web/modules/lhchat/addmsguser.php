<?php
header('content-type: application/json; charset=utf-8');

$definition = array(
        'msg' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::REQUIRED, 'unsafe_raw'
        )
);

$form = new ezcInputForm( INPUT_POST, $definition );
$r = '';
$error = 'f';

if ($form->hasValidData( 'msg' ) && trim($form->msg) != '' && trim(str_replace('[[msgitm]]', '',$form->msg)) != '' && mb_strlen($form->msg) < (int)erLhcoreClassModelChatConfig::fetch('max_message_length')->current_value)
{
	try {
	    $db = ezcDbInstance::get();
	     
	    $db->beginTransaction();
	    
	    $chat = erLhcoreClassModelChat::fetchAndLock($Params['user_parameters']['chat_id']);	

	    if ($chat->hash == $Params['user_parameters']['hash'] && ($chat->status == erLhcoreClassModelChat::STATUS_PENDING_CHAT || $chat->status == erLhcoreClassModelChat::STATUS_ACTIVE_CHAT)) // Allow add messages only if chat is active
	    {	        	        
	        $messagesToStore = explode('[[msgitm]]', trim($form->msg));
	        
	        foreach ($messagesToStore as $messageText)
	        {
	            if (trim($messageText) != '')
                {
                    $msg = new erLhcoreClassModelmsg();
                    $msg->msg = trim($messageText);
                    $msg->chat_id = $Params['user_parameters']['chat_id'];
                    $msg->user_id = 0;
                    $msg->time = time();

                    if ($chat->chat_locale != '' && $chat->chat_locale_to != '') {
                        erLhcoreClassTranslate::translateChatMsgVisitor($chat, $msg);
                    }

                    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.before_msg_user_saved',array('msg' => & $msg,'chat' => & $chat));

                    erLhcoreClassChat::getSession()->save($msg);
                }
	        }

	        if (!isset($msg)){
                $error = 't';
                $r = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Please enter a message, max characters').' - '.(int)erLhcoreClassModelChatConfig::fetch('max_message_length')->current_value;
                echo erLhcoreClassChat::safe_json_encode(array('error' => $error, 'r' => $r));
                exit;
            }

	        $stmt = $db->prepare('UPDATE lh_chat SET last_user_msg_time = :last_user_msg_time, lsync = :lsync, last_msg_id = :last_msg_id, has_unread_messages = 1, unanswered_chat = :unanswered_chat WHERE id = :id');
	        $stmt->bindValue(':id', $chat->id, PDO::PARAM_INT);
	        $stmt->bindValue(':lsync', time(), PDO::PARAM_INT);
	        $stmt->bindValue(':last_user_msg_time', $msg->time, PDO::PARAM_INT);
	        $stmt->bindValue(':unanswered_chat',($chat->status == erLhcoreClassModelChat::STATUS_PENDING_CHAT ? 1 : 0), PDO::PARAM_INT);

	        // Set last message ID
	        if ($chat->last_msg_id < $msg->id) {	        
	        	$stmt->bindValue(':last_msg_id',$msg->id,PDO::PARAM_INT);
	        } else {
	        	$stmt->bindValue(':last_msg_id',$chat->last_msg_id,PDO::PARAM_INT);
	        }

	        $stmt->execute();
	        	        
	        if ($chat->has_unread_messages == 1 && $chat->last_user_msg_time < (time() - 5)) {
	        	erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.unread_chat',array('chat' => & $chat));
	        }
	    }	    
	    
	    // Assign to last message all the texts
	    $msg->msg = trim(implode("\n", $messagesToStore));
	    
	    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.addmsguser',array('chat' => & $chat, 'msg' => & $msg));
	    
	    // Initialize auto responder if required
	    if ($chat->status_sub == erLhcoreClassModelChat::STATUS_SUB_START_ON_KEY_UP)
	    {
	        // Invitation...
	        // We have to apply proactive invitation rules
	        if ($chat->chat_initiator == erLhcoreClassModelChat::CHAT_INITIATOR_PROACTIVE && $chat->online_user !== false && $chat->online_user->invitation !== false) {
	            
	            if ($chat->online_user->invitation->wait_message != '') {
	                $msg = new erLhcoreClassModelmsg();
	                $msg->msg = trim($chat->online_user->invitation->wait_message);
	                $msg->chat_id = $chat->id;
	                $msg->name_support = $chat->online_user->operator_user !== false ? $chat->online_user->operator_user->name_support : (!empty($chat->online_user->operator_user_proactive) ? $chat->online_user->operator_user_proactive : erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Live Support'));
	                $msg->user_id = $chat->online_user->operator_user_id > 0 ? $chat->online_user->operator_user_id : -2;
	                $msg->time = time()+5;
	                erLhcoreClassChat::getSession()->save($msg);
	            }
	            
	            // Store wait timeout attribute for future
                // @todo fix me
	            /*$chat->wait_timeout = $chat->online_user->invitation->wait_timeout;
	            $chat->timeout_message = $chat->online_user->invitation->timeout_message;
	            $chat->wait_timeout_send = 1-$chat->online_user->invitation->repeat_number;
	            $chat->wait_timeout_repeat = $chat->online_user->invitation->repeat_number;*/
	            
	            $chat->status_sub = erLhcoreClassModelChat::STATUS_SUB_DEFAULT;
	            $chat->time = time(); // Update initial chat start time for auto responder
	            $chat->saveThis();
	            
	            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.auto_responder_triggered',array('chat' => & $chat));
	            
	        }
	        // @todo fix me
	        /* else {
        	    // Auto responder
        	    $responder = erLhAbstractModelAutoResponder::processAutoResponder($chat);
        	    
        	    if ($responder instanceof erLhAbstractModelAutoResponder) {
        	        $chat->wait_timeout = $responder->wait_timeout;
        	        $chat->timeout_message = $responder->timeout_message;
        	        $chat->wait_timeout_send = 1-$responder->repeat_number;
        	        $chat->wait_timeout_repeat = $responder->repeat_number;
        	    
        	        if ($responder->wait_message != '') {
        	            $msg = new erLhcoreClassModelmsg();
        	            $msg->msg = trim($responder->wait_message);
        	            $msg->chat_id = $chat->id;
        	            $msg->name_support = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Live Support');
        	            $msg->user_id = -2;
        	            $msg->time = time()+5;
        	            erLhcoreClassChat::getSession()->save($msg);
        	    
        	            if ($chat->last_msg_id < $msg->id) {
        	                $chat->last_msg_id = $msg->id;
        	            }
        	        }
        	    
        	        $chat->status_sub = erLhcoreClassModelChat::STATUS_SUB_DEFAULT;
        	        $chat->time = time(); // Update initial chat start time for auto responder
        	        $chat->saveThis();
        	    
        	        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.auto_responder_triggered',array('chat' => & $chat));
        	    }
	        }*/
	    }
	    
	    $db->commit();
	     
	    echo erLhcoreClassChat::safe_json_encode(array('error' => $error, 'r' => $r));
	    
	    exit;
	    
	} catch (Exception $e) {
   		$db->rollback();
    }
    
} else {
	$error = 't';
	$r = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Please enter a message, max characters').' - '.(int)erLhcoreClassModelChatConfig::fetch('max_message_length')->current_value;
	echo erLhcoreClassChat::safe_json_encode(array('error' => $error, 'r' => $r));
	exit;
}



?>