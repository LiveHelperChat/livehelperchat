<?php
header ( 'content-type: application/json; charset=utf-8' );
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
		$Chat = erLhcoreClassModelChat::fetchAndLock($Params['user_parameters']['chat_id']);

	    if ($Chat instanceof erLhcoreClassModelChat && erLhcoreClassChat::hasAccessToRead($Chat) && erLhcoreClassChat::hasAccessToWrite($Chat) && ($Chat->status == erLhcoreClassModelChat::STATUS_OPERATORS_CHAT || $Chat->user_id == 0 || $Chat->user_id == $currentUser->getUserID() || $currentUser->hasAccessTo('lhchat','writeremotechat')))
	    {
	        $currentUser = erLhcoreClassUser::instance();
	
	        if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
	        	echo erLhcoreClassChat::safe_json_encode(array('error' => 'true', 'token' => $currentUser->getCSFRToken(), 'r' => 'Try again or refresh a page. We could not verify your request.' ));
	        	$db->rollback();
	        	exit;
	        }
	
	        $userData = $currentUser->getUserData();
		      
	        $messageUserId = $userData->id;
	        $msgText = trim($form->msg);
	        $ignoreMessage = false;
	        $returnBody = '';
	        $customArgs = array();
            $whisper = isset($_POST['whisper']);
            $asChatOwner = isset($_POST['mode_write']) && $_POST['mode_write'] == 'op' && $Chat->user_id > 0 && $Chat->user_id != $messageUserId && erLhcoreClassUser::instance()->hasAccessTo('lhchat','impersonate');

	        if (!$whisper && strpos($msgText, '!') === 0) {

                $lastMessageId = $Chat->last_msg_id;

	            $statusCommand = erLhcoreClassChatCommand::processCommand(array('user' => $userData, 'msg' => $msgText, 'chat' => & $Chat));
	            if ($statusCommand['processed'] === true) {
	                $messageUserId = -1; // Message was processed set as internal message

                    // Find a new possible bot messages and trigger message added events for third party integrations
                    $botMessages = erLhcoreClassModelmsg::getList(array('filterin' => ['user_id' => [($Chat->user_id > 0 ? $Chat->user_id : -2), -2]],'filter' => array( 'chat_id' => $Chat->id), 'filtergt' => array('id' => $lastMessageId)));

                    foreach ($botMessages as $botMessage) {
                        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.web_add_msg_admin', array(
                            'chat' => & $Chat,
                            'msg' => $botMessage,
                            'no_auto_events' => true    // Some triggers updates last message and webhooks them self sends this event, we want to avoid that
                        ));
                    }

	                $rawMessage = !isset($statusCommand['raw_message']) ? $msgText : $statusCommand['raw_message'];
	                
	                $msgText = trim('[b]'.$userData->name_support.'[/b]: '.$rawMessage .' '. ($statusCommand['process_status'] != '' ? '|| '.$statusCommand['process_status'] : ''));
	                
	                if (isset($statusCommand['ignore']) && $statusCommand['ignore'] == true) {
	                    $ignoreMessage = true;
                        if (isset($statusCommand['last_message'])) {
                            $msg = $statusCommand['last_message'];
                            if (is_object($msg)){
                                $Chat->last_msg_id = $msg->id;
                                $Chat->updateThis(['update' => ['last_msg_id']]);
                            }
                        }
	                }

	                if (isset($statusCommand['info'])) {
	                    $tpl = erLhcoreClassTemplate::getInstance('lhchat/lists/assistance_message.tpl.php');
	                    $tpl->set('msg',array('msg' =>  $statusCommand['info'], 'time' => time()));
	                    $returnBody = $tpl->fetch();
	                }

                    if (isset($statusCommand['custom_args'])) {
                        $customArgs = $statusCommand['custom_args'];
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
                $msg->del_st = erLhcoreClassModelmsg::STATUS_SENT;

                if ($msg->user_id > 0 && $asChatOwner == true) {
                    $messageUserId = $msg->user_id = $Chat->user_id;
                    $msg->name_support = $Chat->plain_user_name;
                }

                if (strpos($msg->msg,'[html]') !== false && !$currentUser->hasAccessTo('lhchat','htmlbbcodeenabled')) {
                    $msg->msg = '[html] is disabled for you!';
                    $msg->user_id = -1;
                }

    	        if (isset($_POST['meta_msg']) && $currentUser->hasAccessTo('lhchat','metamsgenabled')) {
                    $meta_msg = json_decode($_POST['meta_msg'], true);
                    if (is_array($meta_msg)) {
                        $metaContent = [];

                        foreach ($meta_msg as $meta_msg_key => $meta_msg_value) {
                            $metaContent['content'][$meta_msg_key] = $meta_msg_value;
                        }

                        if (!empty($metaContent)) {
                            $msg->meta_msg = json_encode($metaContent);
                        }
                    }
                }

    	        if ($messageUserId != -1 && $Chat->chat_locale != '' && $Chat->chat_locale_to != '' && isset($Chat->chat_variables_array['lhc_live_trans']) && $Chat->chat_variables_array['lhc_live_trans'] === true) {
    	            erLhcoreClassTranslate::translateChatMsgOperator($Chat, $msg);
    	        }

                \LiveHelperChat\Models\Departments\UserDepAlias::getAlias(array('scope' => 'msg', 'msg' => & $msg, 'chat' => $Chat));
                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.before_msg_admin_saved',array('msg' => & $msg,'chat' => & $Chat));

                if (isset($_POST['whisper'])) {
                    $msg->meta_msg = json_encode(['content' => ['whisper' => true]]);
                }

    	        erLhcoreClassChat::getSession()->save($msg);
    	
    	        // Set last message ID
    	        if ($Chat->last_msg_id < $msg->id) {

    	            $updateFields = array();

    	            if (!$whisper && $Chat->status_sub == erLhcoreClassModelChat::STATUS_SUB_ON_HOLD && $messageUserId !== -1 && !isset($Chat->chat_variables_array['lhc_hldu'])) {
                        $updateFields[] = 'status_sub';
                        $updateFields[] = 'last_user_msg_time';
                        $Chat->status_sub = erLhcoreClassModelChat::STATUS_SUB_DEFAULT;
                        $tpl = erLhcoreClassTemplate::getInstance('lhchat/lists/assistance_message.tpl.php');
                        $tpl->set('msg', array('msg' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat', 'Hold removed!'), 'time' => time()));
                        $returnBody .= $tpl->fetch();
                        $customArgs['hold_removed'] = true;

                        if ($Chat->auto_responder !== false) {
                            $Chat->auto_responder->active_send_status = 0;
                            $Chat->auto_responder->saveThis();
                        }
                    }

                    // Reset active counter if operator send new message and it's sync request and there was new message from operator
                    if (!$whisper && $Chat->status_sub != erLhcoreClassModelChat::STATUS_SUB_ON_HOLD && $Chat->auto_responder !== false) {
    	                if ($Chat->auto_responder->active_send_status != 0) {
                            $Chat->auto_responder->active_send_status = 0;
                            $Chat->auto_responder->saveThis();
    	                }
                    }

                    $Chat->last_op_msg_time = time();
                    $Chat->last_msg_id = $msg->id;
                    $updateFields[] = 'last_op_msg_time';
                    $updateFields[] = 'last_msg_id';

                    if (!$whisper && $Chat->status != erLhcoreClassModelChat::STATUS_CLOSED_CHAT) {
                        $Chat->has_unread_op_messages = 1;
                        $updateFields[] = 'has_unread_op_messages';
                        if ($Chat->status_sub_sub == erLhcoreClassModelChat::STATUS_SUB_SUB_MSG_DELIVERED) {
                            $Chat->status_sub_sub = erLhcoreClassModelChat::STATUS_SUB_SUB_DEFAULT;
                            $updateFields[] = 'status_sub_sub';
                        }
                    }

    	        	if (!$whisper && $Chat->unread_op_messages_informed != 0) {
                        $Chat->unread_op_messages_informed = 0;
                        $updateFields[] = 'unread_op_messages_informed';
                    }

    	        	
    	        	if (!$whisper && $userData->invisible_mode == 0 && $messageUserId > 0) { // Change status only if it's not internal command
    		        	if ($Chat->status == erLhcoreClassModelChat::STATUS_PENDING_CHAT) {
    		        		$Chat->status = erLhcoreClassModelChat::STATUS_ACTIVE_CHAT;
                            $Chat->status_sub = erLhcoreClassModelChat::STATUS_SUB_OWNER_CHANGED;
    		        		$Chat->user_id = $messageUserId;
                            $updateFields[] = 'status';
                            $updateFields[] = 'status_sub';
                            $updateFields[] = 'user_id';
    		        	}
    	        	}
    	
    	        	// Chat can be reopened only if user did not ended chat explictly
    	        	if (!$whisper && $Chat->user_status == erLhcoreClassModelChat::USER_STATUS_CLOSED_CHAT && $Chat->status_sub != erLhcoreClassModelChat::STATUS_SUB_USER_CLOSED_CHAT) {
    	        		$Chat->user_status = erLhcoreClassModelChat::USER_STATUS_PENDING_REOPEN;
                        $updateFields[] = 'user_status';
    	        		if ( ($onlineuser = $Chat->online_user) !== false) {
    	        			$onlineuser->reopen_chat = 1;
    	        			$onlineuser->saveThis();
    	        		}
    	        	}

                    if (!$whisper && $Chat->wait_time == 0) {
                        $Chat->wait_time = time() - ($Chat->pnd_time > 0 ? $Chat->pnd_time : $Chat->time);
                        $updateFields[] = 'wait_time';
                    }

                    $Chat->updateThis(array('update' => $updateFields));
    	        }

    	        if (!$whisper && isset($_POST['subjects_ids']) && !empty($_POST['subjects_ids'])) {
    	            $subjects_ids = explode(',',$_POST['subjects_ids']);
    	            erLhcoreClassChat::validateFilterIn($subjects_ids);
                    $presentSubjects = erLhAbstractModelSubjectChat::getList(array('filterin' => array('subject_id' => $subjects_ids),'filter' => array('chat_id' => $Chat->id)));

                    $presentSubjectsIds = [];
                    foreach ($presentSubjects as $presentSubject) {
                        $presentSubjectsIds[] = $presentSubject->subject_id;
                    }

                    foreach (array_diff($subjects_ids,$presentSubjectsIds) as $subjectIdToSave)
                    {
                        $subjectChat = new erLhAbstractModelSubjectChat();
                        $subjectChat->chat_id = $Chat->id;
                        $subjectChat->subject_id = $subjectIdToSave;
                        $subjectChat->saveThis();
                    }
                }

                if (!$whisper && isset($_POST['canned_id']) && is_numeric($_POST['canned_id']) && @erLhcoreClassModelChatConfig::fetch('statistic_options')->data['canned_stats'] == 1) {
                    // @todo add indication do we need to track these stats
                    erLhcoreClassModelCannedMsgUse::logUse(array(
                        'canned_id' => (int)$_POST['canned_id'],
                        'chat_id'   => $Chat->id,
                        'ctime'     => time(),
                        'user_id'   => $currentUser->getUserID(),
                    ));
                }

    	        // If chat is in bot mode and operators writes a message, accept a chat as operator.
    	        if (!$whisper && $Chat->status == erLhcoreClassModelChat::STATUS_BOT_CHAT && $messageUserId != -1) {

                    $userData = $currentUser->getUserData();

                    if ($userData->invisible_mode == 0 && erLhcoreClassChat::hasAccessToWrite($Chat)) {
                        $Chat->status = erLhcoreClassModelChat::STATUS_ACTIVE_CHAT;

                        $Chat->pnd_time = time();
                        $Chat->wait_time = 1;

                        $Chat->user_id = $currentUser->getUserID();

                        // User status in event of chat acceptance
                        $Chat->usaccept = $userData->hide_online;
                        $Chat->operation_admin .= "lhinst.updateVoteStatus(".$Chat->id.");";
                        $Chat->saveThis();

                        // If chat is transferred to pending state we don't want to process any old events
                        $eventPending = erLhcoreClassModelGenericBotChatEvent::findOne(array('filter' => array('chat_id' => $Chat->id)));

                        if ($eventPending instanceof erLhcoreClassModelGenericBotChatEvent) {
                            $eventPending->removeThis();
                        }

                        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.data_changed',array('chat' => & $Chat, 'user' => $currentUser));

                        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.accept',array('chat' => & $Chat, 'user' => $currentUser));
                        erLhcoreClassChat::updateActiveChats($Chat->user_id);

                        if ($Chat->department !== false) {
                            erLhcoreClassChat::updateDepartmentStats($Chat->department);
                        }

                        $options = $Chat->department->inform_options_array;
                        erLhcoreClassChatWorkflow::chatAcceptedWorkflow(array('department' => $Chat->department,'options' => $options),$Chat);
                    }
                }
	        }

	        if (!$whisper && $Chat->status == erLhcoreClassModelChat::STATUS_OPERATORS_CHAT) {
	            
	            $transfer = erLhcoreClassModelTransfer::findOne(array('filter' => array('transfer_user_id' => $currentUser->getUserID(), 'transfer_to_user_id' => ($Chat->user_id == $currentUser->getUserID() ? $Chat->sender_user_id : $Chat->user_id))));
	            
	            if ($transfer === false) {
    	            $transfer = new erLhcoreClassModelTransfer();
    	            
    	            $transfer->chat_id = $Chat->id;
    	            
    	            $transfer->from_dep_id = $Chat->dep_id;
    	            
    	            // User which is transfering
    	            $transfer->transfer_user_id = $currentUser->getUserID();
    	            
    	            // To what user
    	            $transfer->transfer_to_user_id = $Chat->user_id == $currentUser->getUserID() ? $Chat->sender_user_id : $Chat->user_id;
    	            $transfer->saveThis();
	            }
	        }
	        
	        echo erLhcoreClassChat::safe_json_encode(array('error' => 'false','r' => $returnBody) + $customArgs);

            if (isset($msg)) {
                $Chat->last_message = $msg;
            }
            
	        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.web_add_msg_admin', array('msg' => & $msg,'chat' => & $Chat, 'ou' => (isset($onlineuser) ? $onlineuser : null)));

	    } else {
	        throw new Exception('You cannot read/write to this chat!');
        }

	    $db->commit();
	    
	} catch (Exception $e) {
	    echo $e->getMessage();
   		$db->rollback();
    }

} else {
    echo erLhcoreClassChat::safe_json_encode(array('error' => 'true', 'r' => 'Please enter a message...'));
}


exit;

?>