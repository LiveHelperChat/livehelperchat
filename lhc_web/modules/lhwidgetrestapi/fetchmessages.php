<?php

erLhcoreClassRestAPIHandler::setHeaders();
$requestPayload = json_decode(file_get_contents('php://input'),true);

$db = ezcDbInstance::get();
$db->beginTransaction();

try {
    if (isset($requestPayload['chat_id'])) {
        $chat = erLhcoreClassModelChat::fetch($requestPayload['chat_id']);
    } else {
        $chat = false;
    }
} catch (Exception $e) {
    $chat = false;
}

$content = '';
$ott = '';
$LastMessageID = 0;
$userOwner = true;
$saveChat = false;
$operation = '';
$operatorId = 0;
$visitorTotalMessages = 0;

$responseArray = array('status' => erLhcoreClassModelChat::STATUS_CLOSED_CHAT, 'status_sub' => erLhcoreClassModelChat::STATUS_SUB_DEFAULT);

if (is_object($chat) && $chat->hash == $requestPayload['hash'])
{
    erLhcoreClassChat::setTimeZoneByChat($chat);
    $chat->updateIgnoreColumns = array('last_msg_id');

    $responseArray['status_sub'] = $chat->status_sub;
    $responseArray['status'] = $chat->status;

	try {

		    if ($chat->auto_responder !== false) {
		        $chat->auto_responder->chat = $chat;
		        $chat->auto_responder->process();
		    }

			if ($chat->status == erLhcoreClassModelChat::STATUS_PENDING_CHAT && $chat->transfer_if_na == 1 && $chat->transfer_timeout_ts < (time()-$chat->transfer_timeout_ac) ) {

				$canExecuteWorkflow = true;

				if (erLhcoreClassModelChatConfig::fetch('pro_active_limitation')->current_value >= 0) {
					if ($chat->department !== false && $chat->department->department_transfer_id > 0) {
						$canExecuteWorkflow = erLhcoreClassChat::getPendingChatsCountPublic($chat->department->department_transfer_id) <= erLhcoreClassModelChatConfig::fetch('pro_active_limitation')->current_value;
					}
				}

				if ($canExecuteWorkflow == true) {
					erLhcoreClassChatWorkflow::transferWorkflow($chat);
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
			if (in_array($chat->status,$validStatuses) || ($chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT && ($chat->last_op_msg_time == 0 || $chat->last_op_msg_time > time() - (int)erLhcoreClassModelChatConfig::fetch('open_closed_chat_timeout')->current_value))) {
				// Check for new messages only if chat last message id is greater than user last message id
				if (!isset($requestPayload['lmgsid']) || (int)$requestPayload['lmgsid'] < $chat->last_msg_id) {
				    $Messages = erLhcoreClassChat::getPendingMessages((int)$requestPayload['chat_id'], (isset($requestPayload['lmgsid']) ? (int)$requestPayload['lmgsid'] : 0));
				    if (count($Messages) > 0)
				    {
				        $tpl = erLhcoreClassTemplate::getInstance( 'lhchat/syncuser.tpl.php');
				        $tpl->set('messages',$Messages);
				        $tpl->set('chat',$chat);
				        $tpl->set('sync_mode',/*isset($Params['user_parameters_unordered']['mode']) ? $Params['user_parameters_unordered']['mode'] :*/ '');

				        if ($requestPayload['lmgsid'] == 0) {
                            if (isset($requestPayload['new_chat']) && $requestPayload['new_chat'] == true) {
                                $tpl->set('chat_started_now',true);
                            }
                        } else {
                            $tpl->set('async_call',true);
                        }

				        if (isset($requestPayload['theme']) && $requestPayload['theme'] > 0) {
                            $tpl->set('theme',erLhAbstractModelWidgetTheme::fetch($requestPayload['theme']));
                        }

                        $tpl->set('react',true);

                        $content = $tpl->fetch();

                        $operatorId = null;

				        foreach ($Messages as $msg) {
				        	if ($msg['user_id'] > 0 || $msg['user_id'] == -2 && $userOwner === true) {
				        		$userOwner = false;
				        	}

				        	if ($msg['user_id'] != -1 && $operatorId === null) {
                                $operatorId = (int)$msg['user_id'];
                            }

				        	if ($msg['user_id'] == 0) {
                                $visitorTotalMessages++;
                            }

                            if ((int)$requestPayload['lmgsid'] == 0) {
                                $operatorIdLast = (int)$msg['user_id'];
                            }

                            $LastMessageID = $msg['id'];
				        }
				    }
				}

				if ( $chat->is_operator_typing == true /*&& $Params['user_parameters_unordered']['ot'] != 't'*/ ) {
				    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.syncuser.operator_typing',array('chat' => & $chat));
					$ott = ($chat->operator_typing_user !== false) ? $chat->operator_typing_user->name_support . ' ' . htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','is typing now...'),ENT_QUOTES) : htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Operator is typing now...'),ENT_QUOTES);
				}  elseif (/*$Params['user_parameters_unordered']['ot'] == 't' &&*/ $chat->is_operator_typing == false) {
					$ott = false;
				}
			}

		    // Closed
		    if ($chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT || $chat->status_sub == erLhcoreClassModelChat::STATUS_SUB_USER_CLOSED_CHAT) {
		    	$responseArray['closed'] = true;
		    }

		    if ($chat->status_sub == erLhcoreClassModelChat::STATUS_SUB_OWNER_CHANGED) {
		    	$chat->status_sub = erLhcoreClassModelChat::STATUS_SUB_DEFAULT;
		    	$saveChat = true;
		    }

		    if ($chat->status_sub == erLhcoreClassModelChat::STATUS_SUB_SURVEY_SHOW) {
		    	$responseArray['closed'] = true;
		    	if ($chat->status_sub_arg != '') {
		    	    $args = json_decode($chat->status_sub_arg,true);
		    	    $responseArray['closed_arg'] = $args;
		    	}
		    }

		    if ($chat->operation != '') {
		    	$operation = explode("\n", trim($chat->operation));
		    	$chat->operation = '';
		    	$saveChat = true;
		    }

		    if ($chat->user_status != 0) {
		    	$chat->user_status = 0;
		    	$saveChat = true;
		    }

		    if ($chat->has_unread_op_messages == 1)
		    {
		    	$chat->unread_op_messages_informed = 0;
		    	$chat->has_unread_op_messages = 0;
                $chat->unanswered_chat = 0;
		    	$saveChat = true;
		    }

		    if ($saveChat === true || $chat->lsync < time()-30) {
		        $chat->lsync = time();
		    	$chat->updateThis(array('update' => array(
		    	    'lsync',
                    'unanswered_chat',
                    'has_unread_op_messages',
                    'unread_op_messages_informed',
                    'user_status',
                    'operation',
                    'status_sub',
                )));
		    }

		    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.syncuser',array('chat' => & $chat, 'response' => & $responseArray));

		$db->commit();

	} catch (Exception $e) {
	    $db->rollback();
	}

} else {
    $db->rollback();
    $responseArray['closed'] = true;
}

$responseArray['op'] = $operation;
$responseArray['uw'] = $userOwner;
$responseArray['msop'] = $operatorId;
if (isset($operatorIdLast)) {
    $responseArray['lmsop'] = $operatorIdLast;
}
$responseArray['ott'] = $ott;

// Append how many of messages ones are visitor ones
if ($visitorTotalMessages > 0) {
    $responseArray['vtm'] = $visitorTotalMessages;
}

$responseArray['message_id'] = (int)$LastMessageID;
$responseArray['messages'] = trim($content);

echo erLhcoreClassChat::safe_json_encode($responseArray);
exit;

?>