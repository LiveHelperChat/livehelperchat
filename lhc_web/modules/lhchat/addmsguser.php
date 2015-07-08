<?php

$definition = array(
        'msg' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::REQUIRED, 'unsafe_raw'
        )
);

$form = new ezcInputForm( INPUT_POST, $definition );
$r = '';
$error = 'f';

if ($form->hasValidData( 'msg' ) && trim($form->msg) != '' && mb_strlen($form->msg) < (int)erLhcoreClassModelChatConfig::fetch('max_message_length')->current_value)
{
	try {
	    $chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);	
	    
	    if ($chat->hash == $Params['user_parameters']['hash'] && ($chat->status == erLhcoreClassModelChat::STATUS_PENDING_CHAT || $chat->status == erLhcoreClassModelChat::STATUS_ACTIVE_CHAT)) // Allow add messages only if chat is active
	    {
	        $db = ezcDbInstance::get();
	        
	        $db->beginTransaction();
	        
	        $msg = new erLhcoreClassModelmsg();
	        $msg->msg = trim($form->msg);
	        $msg->chat_id = $Params['user_parameters']['chat_id'];
	        $msg->user_id = 0;
	        $msg->time = time();
	
	        if ($chat->chat_locale != '' && $chat->chat_locale_to != '') {
	            erLhcoreClassTranslate::translateChatMsgVisitor($chat, $msg);
	        }
	        
	        erLhcoreClassChat::getSession()->save($msg);

	        $stmt = $db->prepare('UPDATE lh_chat SET last_user_msg_time = :last_user_msg_time, last_msg_id = :last_msg_id, has_unread_messages = 1 WHERE id = :id');
	        $stmt->bindValue(':id',$chat->id,PDO::PARAM_INT);
	        $stmt->bindValue(':last_user_msg_time',$msg->time,PDO::PARAM_INT);

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
	    
	    echo json_encode(array('error' => $error, 'r' => $r));
	   	
	    flush();
	    
	    session_write_close();
	    
	    if ( function_exists('fastcgi_finish_request') ) {
	        fastcgi_finish_request();
	    };
	    	     	    
	    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.addmsguser',array('chat' => & $chat, 'msg' => & $msg));
	    exit;
	    
	} catch (Exception $e) {
   		$db->rollback();
    }
    
} else {
	$error = 't';
	$r = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Please enter a message, max characters').' - '.(int)erLhcoreClassModelChatConfig::fetch('max_message_length')->current_value;
	echo json_encode(array('error' => $error, 'r' => $r));
	exit;
}



?>