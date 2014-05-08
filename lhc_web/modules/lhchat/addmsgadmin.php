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
	        	exit;
	        }
	
	        $userData = $currentUser->getUserData();
	
	        $msg = new erLhcoreClassModelmsg();
	        $msg->msg = trim($form->msg);
	        $msg->chat_id = $Params['user_parameters']['chat_id'];
	        $msg->user_id = $userData->id;
	        $msg->time = time();
	        $msg->name_support = $userData->name_support;
	        erLhcoreClassChat::getSession()->save($msg);
	
	        // Set last message ID
	        if ($Chat->last_msg_id < $msg->id) {
	
	        	if ($userData->invisible_mode == 0) {
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
	
	        	$Chat->last_msg_id = $msg->id;
	        	$Chat->updateThis();
	        }
	
	        echo json_encode(array('error' => 'false'));
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