<?php

$definition = array(
        'msg' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::REQUIRED, 'unsafe_raw'
        )
);

$form = new ezcInputForm( INPUT_POST, $definition );

if (trim($form->msg) != '')
{
	$db = ezcDbInstance::get();
	$db->beginTransaction();	
	try {
		$Chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);
						
	    // Has access to read, chat
	    //FIXME create permission to add message...
	    if ( erLhcoreClassChat::hasAccessToRead($Chat) )
	    {
	        $currentUser = erLhcoreClassUser::instance();
	
	        if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
	        	echo json_encode(array('error' => 'true', 'result' => 'Invalid CSRF Token' ));
	        	$db->rollback();
	        	exit;
	        }
	
	        $userData = $currentUser->getUserData();
		      
	        $messageUserId = $userData->id;
	        $msgText = trim($form->msg);
	        $ignoreMessage = false;
	        $returnBody = '';
	        
	        if (strpos($msgText, '!') === 0) {
	            $statusCommand = erLhcoreClassChatCommand::processCommand(array('user' => $userData, 'msg' => $msgText, 'chat' => & $Chat));
	            if ($statusCommand['processed'] === true) {
	                $messageUserId = -1; // Message was processed set as internal message
	                $msgText = trim('[b]'.$userData->name_support.'[/b]: '.$msgText .' '. ($statusCommand['process_status'] != '' ? '|| '.$statusCommand['process_status'] : ''));
	                
	                if (isset($statusCommand['ignore']) && $statusCommand['ignore'] == true) {
	                    $ignoreMessage = true;
	                }
	                
	                if (isset($statusCommand['info'])) {
	                    $tpl = erLhcoreClassTemplate::getInstance('lhchat/lists/assistance_message.tpl.php');
	                    $tpl->set('msg',array('msg' =>  $statusCommand['info'], 'time' => time()));
	                    $returnBody = $tpl->fetch();
	                }
	            };
	        }
	        
	        if ($ignoreMessage == false) {	        
    	        $msg = new erLhcoreClassModelmsg();
    	        $msg->msg = $msgText;
    	        $msg->chat_id = $Params['user_parameters']['chat_id'];
    	        $msg->user_id = $messageUserId;
    	        $msg->time = time();
    	        $msg->name_support = $userData->name_support;
    	        
    	        if ($messageUserId != -1 && $Chat->chat_locale != '' && $Chat->chat_locale_to != '') {
    	            erLhcoreClassTranslate::translateChatMsgOperator($Chat, $msg);
    	        }
    	        
    	        erLhcoreClassChat::getSession()->save($msg);
    	
    	        // Set last message ID
    	        if ($Chat->last_msg_id < $msg->id) {
    	        	
    	        	$stmt = $db->prepare('UPDATE lh_chat SET status = :status, user_status = :user_status, last_msg_id = :last_msg_id WHERE id = :id');
    	        	$stmt->bindValue(':id',$Chat->id,PDO::PARAM_INT);
    	        	$stmt->bindValue(':last_msg_id',$msg->id,PDO::PARAM_INT);
    	        		        	
    	        	if ($userData->invisible_mode == 0 && $messageUserId > 0) { // Change status only if it's not internal command
    		        	if ($Chat->status == erLhcoreClassModelChat::STATUS_PENDING_CHAT) {
    		        		$Chat->status = erLhcoreClassModelChat::STATUS_ACTIVE_CHAT;        		
    		        	}
    	        	}
    	
    	        	if ($Chat->user_status == erLhcoreClassModelChat::USER_STATUS_CLOSED_CHAT) {
    	        		$Chat->user_status = erLhcoreClassModelChat::USER_STATUS_PENDING_REOPEN;
    	        		if ( ($onlineuser = $Chat->online_user) !== false) {
    	        			$onlineuser->reopen_chat = 1;
    	        			$onlineuser->saveThis();
    	        		}
    	        	}
    	        	
    	        	$stmt->bindValue(':user_status',$Chat->user_status,PDO::PARAM_INT);
    	        	$stmt->bindValue(':status',$Chat->status,PDO::PARAM_INT);
    	        	$stmt->execute();	        	
    	        }
	        }
	              
	        echo json_encode(array('error' => 'false','r' => $returnBody));
	        
	        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.web_add_msg_admin',array('msg' => & $msg,'chat' => & $Chat));
	    }   
	     	    
	    $db->commit();
	    
	} catch (Exception $e) {
   		$db->rollback();
    }

} else {
    echo json_encode(array('error' => 'true'));
}


exit;

?>