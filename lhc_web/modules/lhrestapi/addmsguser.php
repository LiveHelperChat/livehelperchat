<?php

$definition = array(
        'msg' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::REQUIRED, 'unsafe_raw'
        )
);

$form = new ezcInputForm( INPUT_POST, $definition );
$error = false;

if ($form->hasValidData( 'msg' ) && trim($form->msg) != '' && mb_strlen($form->msg) < (int)erLhcoreClassModelChatConfig::fetch('max_message_length')->current_value)
{
	try {
	    $db = ezcDbInstance::get();
	    
	    if (isset($_POST['chat_id'])) {
	       $chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $_POST['chat_id']);	
	    } else {
	        throw new Exception('chat_id has to be provided!');
	    }
	    
	    if (isset($_POST['hash']) && $chat->hash == $_POST['hash'] && ($chat->status == erLhcoreClassModelChat::STATUS_PENDING_CHAT || $chat->status == erLhcoreClassModelChat::STATUS_ACTIVE_CHAT)) // Allow add messages only if chat is active
	    {
	        $db->beginTransaction();
	        
	        $messagesToStore = explode('[[msgitm]]', trim($form->msg));
	        
	        foreach ($messagesToStore as $messageText)
	        {
    	        $msg = new erLhcoreClassModelmsg();
    	        $msg->msg = trim($messageText);
    	        $msg->chat_id = $chat->id;
    	        $msg->user_id = 0;
    	        $msg->time = time();
    	
    	        if ($chat->chat_locale != '' && $chat->chat_locale_to != '') {
    	            erLhcoreClassTranslate::translateChatMsgVisitor($chat, $msg);
    	        }

                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.before_msg_user_saved',array('msg' => & $msg,'chat' => & $chat));

    	        erLhcoreClassChat::getSession()->save($msg);
	        }

	        $stmt = $db->prepare('UPDATE lh_chat SET last_user_msg_time = :last_user_msg_time, last_msg_id = :last_msg_id, has_unread_messages = 1, unanswered_chat = :unanswered_chat WHERE id = :id');
	        $stmt->bindValue(':id',$chat->id, PDO::PARAM_INT);
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
	        
	        $db->commit();
	    }	    
	    
	    // Assign to last message all the texts
	    $msg->msg = trim(implode("\n", $messagesToStore));
	    
	    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.addmsguser',array('chat' => & $chat, 'msg' => & $msg));
	    
	    erLhcoreClassRestAPIHandler::outputResponse(array('error' => false, 'result' => true));
	    exit;
	    
	} catch (Exception $e) {
   		$db->rollback();
    }
    
} else {
	$r = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Please enter a message, max characters').' - '.(int)erLhcoreClassModelChatConfig::fetch('max_message_length')->current_value;
	echo erLhcoreClassRestAPIHandler::outputResponse(array('error' => true, 'result' => $r));
	exit;
}



?>