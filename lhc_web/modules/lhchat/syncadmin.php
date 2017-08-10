<?php

header ( 'content-type: application/json; charset=utf-8' );
$timeCurrent = time();
$pollingEnabled = (int)erLhcoreClassModelChatConfig::fetchCache('sync_sound_settings')->data['long_polling_enabled'];
$pollingServerTimeout = (int)erLhcoreClassModelChatConfig::fetchCache('sync_sound_settings')->data['connection_timeout'];
$pollingMessageTimeout = (float)erLhcoreClassModelChatConfig::fetchCache('sync_sound_settings')->data['polling_chat_message_sinterval'];
$breakSync = false;

$content = 'false';
$content_status = 'false';
$userOwner = 'true';

$hasAccessToReadArray = array();

if (isset($_POST['chats']) && is_array($_POST['chats']) && count($_POST['chats']) > 0)
{
    $ReturnMessages = array();
    $ReturnStatuses = array();

    $tpl = erLhcoreClassTemplate::getInstance( 'lhchat/syncadmin.tpl.php');
    $currentUser = erLhcoreClassUser::instance();

    if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
    	echo json_encode(array('error' => 'true', 'result' => 'Invalid CSRF Token' ));
    	exit;
    }
    
    // Set online condition configurations
    erLhcoreClassChat::$trackActivity = (int)erLhcoreClassModelChatConfig::fetchCache('track_activity')->current_value == 1;
    erLhcoreClassChat::$trackTimeout = (int)erLhcoreClassModelChatConfig::fetchCache('checkstatus_timeout')->current_value;
    erLhcoreClassChat::$onlineCondition = (int)erLhcoreClassModelChatConfig::fetchCache('online_if')->current_value;
        
    // We do not need a session anymore
    session_write_close();
    
    $db = ezcDbInstance::get();        
    while (true) {
    	$db->beginTransaction();
    	try {    	
		    foreach ($_POST['chats'] as $chat_id_list)
		    {
		        list($chat_id, $MessageID ) = explode(',',$chat_id_list);
		        $chat_id = (int)$chat_id;
		        $MessageID = (int)$MessageID;
		        
		        $Chat = erLhcoreClassModelChat::fetch($chat_id);
		        $Chat->updateIgnoreColumns = array('last_msg_id');
		        
		        if ( isset($hasAccessToReadArray[$chat_id]) || erLhcoreClassChat::hasAccessToRead($Chat) )
		        {
		        	$hasAccessToReadArray[$chat_id] = true;
		        	
		            if ( ($Chat->last_msg_id > (int)$MessageID) && count($Messages = erLhcoreClassChat::getPendingMessages($chat_id,$MessageID)) > 0)
		            {
		            	// If chat had flag that it contains unread messages set to 0
		            	if ( $Chat->has_unread_messages == 1 || $Chat->unread_messages_informed == 1) {
		            		 $Chat->has_unread_messages = 0;
		            		 $Chat->unread_messages_informed = 0;
		            		 $Chat->saveThis();
		            	}
		
		            	// Auto accept transfered chats if I have opened this chat
		            	if ($Chat->status == erLhcoreClassModelChat::STATUS_OPERATORS_CHAT) {
		            	    
		            	   $q = $db->createDeleteQuery();
       
                           // Delete transfered chat's to me
                           $q->deleteFrom( 'lh_transfer' )->where( $q->expr->eq( 'chat_id', $Chat->id ), $q->expr->eq( 'transfer_to_user_id', $currentUser->getUserID() ) );
                           $stmt = $q->prepare();
                           $stmt->execute();
		            	}
		            	
		            	$newMessagesNumber = count($Messages);
		
		                $tpl->set('messages',$Messages);
		                $tpl->set('chat',$Chat);
		
		                $msgText = '';
		                if ($userOwner == 'true') {
		                	foreach ($Messages as $msg) {
		                		if ($msg['user_id'] != $currentUser->getUserID()) {
		                			$userOwner = 'false';
		                			$msgText = $msg['msg'];
		                			break;
		                		}
		                	}
		                }
		                // Get first message opertor id
		                reset($Messages);
		                $firstNewMessage = current($Messages);
		                  
		                // Get last message
		                end($Messages);
		                $LastMessageIDs = current($Messages);
		                
		                // Fetch content
		                $templateResult = $tpl->fetch();
		                
		                $response = array('chat_id' => $chat_id,'nck' => $Chat->nick, 'msfrom' => $MessageID, 'msop' => $firstNewMessage['user_id'], 'mn' => $newMessagesNumber, 'msg' => $msgText, 'content' => $templateResult, 'message_id' => $LastMessageIDs['id']);
		                
		                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.syncadmin',array('response' => & $response, 'messages' => $Messages, 'chat' => $Chat));
		                		                
		                $ReturnMessages[] = $response;
		            }

		            $lp = $Chat->lsync > 0 ? time()-$Chat->lsync : false; 
		            
		            if ($Chat->is_user_typing == true) {
		                $ReturnStatuses[$chat_id] = array('chat_id' => $chat_id, 'lp' => $lp, 'um' => $Chat->has_unread_op_messages, 'us' => $Chat->user_status_front, 'tp' => 'true','tx' => htmlspecialchars($Chat->user_typing_txt));
		            } else {
		                $ReturnStatuses[$chat_id] = array('chat_id' => $chat_id, 'lp' => $lp, 'um' => $Chat->has_unread_op_messages, 'us' => $Chat->user_status_front, 'tp' => 'false');
		            }

		            if ($Chat->operation_admin != '') {
		            	$ReturnStatuses[$chat_id]['oad'] = $Chat->operation_admin;
		            	$Chat->operation_admin = '';
		            	$Chat->saveThis();
		            }	            
		        }
		
		    }
	    	$db->commit();
	    } catch (Exception $e) {
	    	$db->rollback();
	    }
	    
	    if (count($ReturnMessages) > 0) {
	    	$content = $ReturnMessages;
	    	$breakSync = true;
	    }
	
	    if (count($ReturnStatuses) > 0) {
	    	$content_status = $ReturnStatuses;
	    	$breakSync = true;
	    }

	    if ($pollingEnabled == false || $breakSync == true || ($pollingServerTimeout + $timeCurrent) < time() ) {
	    	break;
	    } else {
	    	usleep($pollingMessageTimeout * 1000000);
	    }    
    }
    
}



echo erLhcoreClassChat::safe_json_encode(array('error' => 'false','uw' => $userOwner, 'result_status' => $content_status, 'result' => $content ));
exit;
?>