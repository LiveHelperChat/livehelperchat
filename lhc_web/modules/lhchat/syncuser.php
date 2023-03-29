<?php
header ( 'content-type: application/json; charset=utf-8' );

$db = ezcDbInstance::get();
$db->beginTransaction();

$chat = erLhcoreClassModelChat::fetchAndLock($Params['user_parameters']['chat_id']);

$content = 'false';
$status = 'true';
$blocked = 'false';
$ott = '';
$LastMessageID = 0;
$userOwner = 'true';
$checkStatus = 'f';
$saveChat = false;
$operation = '';
$operatorId = 0;

$responseArray = array();

if (is_object($chat) && $chat->hash == $Params['user_parameters']['hash'])
{
    $chat->updateIgnoreColumns = array('last_msg_id');
    erLhcoreClassChat::setTimeZoneByChat($chat);

	try {

		    if ($chat->auto_responder !== false) {
		        $chat->auto_responder->chat = $chat;
		        $chat->auto_responder->process();
		    }

			if ($chat->status == erLhcoreClassModelChat::STATUS_PENDING_CHAT && $chat->transfer_if_na == 1 &&
                (
			        (
			            $chat->transfer_timeout_ts < (time()-$chat->transfer_timeout_ac)
                    ) || (
                        ($department = $chat->department) && $offlineDepartmentOperators = true && $department !== false && isset($department->bot_configuration_array['off_op_exec']) && $department->bot_configuration_array['off_op_exec'] == 1 && erLhcoreClassChat::isOnline($chat->dep_id,false, array('exclude_bot' => true, 'exclude_online_hours' => true)) === false
                    )
                ) ) {

				$canExecuteWorkflow = true;
		
				if (erLhcoreClassModelChatConfig::fetch('pro_active_limitation')->current_value >= 0) {
					if ($chat->department !== false && $chat->department->department_transfer_id > 0) {
						$canExecuteWorkflow = erLhcoreClassChat::getPendingChatsCountPublic($chat->department->department_transfer_id) <= erLhcoreClassModelChatConfig::fetch('pro_active_limitation')->current_value;
					}
				}
		
				if ($canExecuteWorkflow == true) {
					erLhcoreClassChatWorkflow::transferWorkflow($chat, array('offline_operators' => isset($offlineDepartmentOperators)));
				}
			}
		
			if ($chat->reinform_timeout > 0 && $chat->unread_messages_informed == 0 && $chat->has_unread_messages == 1 && (time()-$chat->last_user_msg_time) > $chat->reinform_timeout) {			
				$department = $chat->department;
				if ($department !== false) {
					$options = $department->inform_options_array;			
					erLhcoreClassChatWorkflow::unreadInformWorkflow(array('department' => $department,'options' => $options),$chat);				
				}			
			}

            $validStatuses = array(
                erLhcoreClassModelChat::STATUS_PENDING_CHAT,
                erLhcoreClassModelChat::STATUS_ACTIVE_CHAT,
                erLhcoreClassModelChat::STATUS_BOT_CHAT,
            );

            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.validstatus_chat',array('chat' => & $chat, 'valid_statuses' => & $validStatuses));

			// Sync only if chat is pending or active
			if (in_array($chat->status,$validStatuses)) {
				// Check for new messages only if chat last message id is greater than user last message id
				if ((int)$Params['user_parameters']['message_id'] < $chat->last_msg_id) {
				    $Messages = erLhcoreClassChat::getPendingMessages((int)$Params['user_parameters']['chat_id'],(int)$Params['user_parameters']['message_id']);
				    if (count($Messages) > 0)
				    {
                        $theme = false;

                        if (isset($Params['user_parameters_unordered']['theme']) && ($themeId = erLhcoreClassChat::extractTheme($Params['user_parameters_unordered']['theme'])) !== false) {
                            try {
                                $theme = erLhAbstractModelWidgetTheme::fetch($themeId);
                            } catch (Exception $e) {

                            }
                        } else {
                            $defaultTheme = erLhcoreClassModelChatConfig::fetch('default_theme_id')->current_value;
                            if ($defaultTheme > 0) {
                                try {
                                    $theme = erLhAbstractModelWidgetTheme::fetch($defaultTheme);
                                } catch (Exception $e) {

                                }
                            }
                        }

				        $tpl = erLhcoreClassTemplate::getInstance( 'lhchat/syncuser.tpl.php');
				        $tpl->set('messages',$Messages);
				        $tpl->set('chat',$chat);
				        $tpl->set('sync_mode',isset($Params['user_parameters_unordered']['mode']) ? $Params['user_parameters_unordered']['mode'] : '');
                        $tpl->set('async_call',true);
                        $tpl->set('theme',$theme);

				        $content = $tpl->fetch();
		
				        foreach ($Messages as $msg) {
				        	if ($msg['user_id'] > 0 || $msg['user_id'] == -2) {
				        		$userOwner = 'false';
				        		break;
				        	}
				        }
		
				        // Get first message opertor id
				        reset($Messages);
				        $firstNewMessage = current($Messages);
				        $operatorId = $firstNewMessage['user_id'];
				        
				        if ($operatorId == -1) {
				        	$operatorId = 0;
				        }
				        
				        // Get Last message ID
				        end($Messages);
				        $LastMessageIDs = current($Messages);
				        $LastMessageID = $LastMessageIDs['id'];
				    }
				}
				
				if ( $chat->is_operator_typing == true && $Params['user_parameters_unordered']['ot'] != 't' ) {
				    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.syncuser.operator_typing',array('chat' => & $chat));

                    if ($chat->operator_typing_user !== false) {
                        \LiveHelperChat\Models\Departments\UserDepAlias::getAlias(array('scope' => 'typing', 'chat' => $chat));
                    }

                    $ott = ($chat->operator_typing_user !== false) ? $chat->operator_typing_user->name_support . ' ' . htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','is typing now...'),ENT_QUOTES) : htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Operator is typing now...'),ENT_QUOTES);
				}  elseif ($Params['user_parameters_unordered']['ot'] == 't' && $chat->is_operator_typing == false) {
					$ott = 'f';
				}
			}
			
		    // Closed
		    if ($chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT) {

                if (!isset($theme)) {
                    $theme = false;
                    if (isset($Params['user_parameters_unordered']['theme']) && ($themeId = erLhcoreClassChat::extractTheme($Params['user_parameters_unordered']['theme'])) !== false){
                        try {
                            $theme = erLhAbstractModelWidgetTheme::fetch($themeId);
                        } catch (Exception $e) {

                        }
                    } else {
                        $defaultTheme = erLhcoreClassModelChatConfig::fetch('default_theme_id')->current_value;
                        if ($defaultTheme > 0) {
                            try {
                                $theme = erLhAbstractModelWidgetTheme::fetch($defaultTheme);
                            } catch (Exception $e) {

                            }
                        }
                    }
                }

		        // Parse outcome
		        $tpl = erLhcoreClassTemplate::getInstance( 'lhchat/errors/chatclosed.tpl.php');
		        $tpl->set('theme',$theme);
		        $tpl->set('modeembed',isset($Params['user_parameters_unordered']['mode']) ? $Params['user_parameters_unordered']['mode'] : '');
		        $tpl->set('chat',$chat);

		        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.before_chat_closed_tpl',array('chat' => & $chat, 'tpl' => & $tpl));
		        
		        $status = $tpl->fetch();
		        
		    	$blocked = 'true';

		    	$responseArray['closed'] = true;
		    }
		    
		    // If there was two tabs open with same chat force chat close in another tab also
		    if ($chat->status_sub == erLhcoreClassModelChat::STATUS_SUB_USER_CLOSED_CHAT) {
		        $blocked = 'true';
		        $responseArray['closed'] = true;
		        $status = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','You have closed this chat!');
		    }

		    $updateFields = array('lsync');

		    if ($chat->status_sub == erLhcoreClassModelChat::STATUS_SUB_OWNER_CHANGED) {
		    	$checkStatus = 't';
		    	$chat->status_sub = erLhcoreClassModelChat::STATUS_SUB_DEFAULT;
                $updateFields[] = 'status_sub';
		    	$saveChat = true;
		    }
		    		    		    
		    if ($chat->status_sub == erLhcoreClassModelChat::STATUS_SUB_SURVEY_SHOW) {
		    	$blocked = 'true';
		    	$responseArray['closed'] = true;
		    	$status = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','You have been redirected to survey!');
		    	if ($chat->status_sub_arg != '') {		    	    
		    	    $args = json_decode($chat->status_sub_arg,true);		    	    		    	    
		    	    $responseArray['closed_arg'] = erLhcoreClassChatHelper::getSubStatusArguments($chat);		    	    
		    	}
		    }

		    if ($chat->status_sub == erLhcoreClassModelChat::STATUS_SUB_CONTACT_FORM) {
		        $checkStatus = 't';
		    }
		    
		    if ($chat->operation != '') {	    	
		    	$operation = explode("\n", trim($chat->operation));
		    	$chat->operation = '';
                $updateFields[] = 'operation';
		    	$saveChat = true;
		    }
		    
		    if ($chat->user_status != 0) {
		    	$chat->user_status = 0;
                $updateFields[] = 'user_status';
		    	$saveChat = true;
		    }
		    
		    if ($chat->has_unread_op_messages == 1)
		    {
		    	$chat->unread_op_messages_informed = 0;
		    	$chat->has_unread_op_messages = 0;
                $chat->unanswered_chat = 0;
                $updateFields[] = 'unread_op_messages_informed';
                $updateFields[] = 'has_unread_op_messages';
                $updateFields[] = 'unanswered_chat';
		    	$saveChat = true;
		    }
		    
		    if ($saveChat === true || $chat->lsync < time()-30) {
		        $chat->lsync = time();
                $chat->updateThis(array('update' => $updateFields));
		    }
		    
		    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.syncuser',array('chat' => & $chat, 'response' => & $responseArray));

		$db->commit();

	} catch (Exception $e) {
	    $db->rollback();
	}  

} else {    
    $db->rollback();
    
    $content = 'false';
    $theme = false;
    
    if (isset($Params['user_parameters_unordered']['theme']) && ($themeId = erLhcoreClassChat::extractTheme($Params['user_parameters_unordered']['theme'])) !== false){
        try {
            $theme = erLhAbstractModelWidgetTheme::fetch($themeId);
        } catch (Exception $e) {
    
        }
    } else {
        $defaultTheme = erLhcoreClassModelChatConfig::fetch('default_theme_id')->current_value;
        if ($defaultTheme > 0) {
            try {
                $theme = erLhAbstractModelWidgetTheme::fetch($defaultTheme);
            } catch (Exception $e) {
                 
            }
        }
    }
    
    $tpl = erLhcoreClassTemplate::getInstance( 'lhchat/errors/chatclosed.tpl.php');
    $tpl->set('theme',$theme);
    $tpl->set('modeembed',$Params['user_parameters_unordered']['modeembed']);
    
    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.before_chat_closed_tpl',array('chat' => & $chat, 'tpl' => & $tpl));
    
    $status = $tpl->fetch();
    
    $blocked = 'true';
}

$responseArray['error'] = 'false';
$responseArray['op'] = $operation;
$responseArray['uw'] = $userOwner;
$responseArray['msop'] = $operatorId;
$responseArray['cs'] = $checkStatus;
$responseArray['ott'] = $ott;
$responseArray['message_id'] = $LastMessageID;
$responseArray['result'] = trim($content) == '' ? 'false' : trim($content);
$responseArray['status'] = $status;
$responseArray['blocked'] = $blocked;

echo erLhcoreClassChat::safe_json_encode($responseArray);
exit;

?>