<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
header('Content-Type: application/json');

try {
    erLhcoreClassRestAPIHandler::validateRequest();
    
    $chat = erLhcoreClassModelChat::fetch((int)$_GET['chat_id']);
    
    if (erLhcoreClassRestAPIHandler::hasAccessToRead($chat) == true) {
        
        if (isset($_GET['workflow']) && $_GET['workflow'] == true) {
            // Auto responder
            if ($chat->status == erLhcoreClassModelChat::STATUS_PENDING_CHAT && $chat->wait_timeout_send <= 0 && $chat->wait_timeout > 0 && !empty($chat->timeout_message) && (time() - $chat->time) > ($chat->wait_timeout*($chat->wait_timeout_repeat-(abs($chat->wait_timeout_send))))) {
                $errors = array();
                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.before_auto_responder_triggered',array('chat' => & $chat, 'errors' => & $errors));
            
                if (empty($errors)) {
                    erLhcoreClassChatWorkflow::timeoutWorkflow($chat);
                } else {
                    $msg = new erLhcoreClassModelmsg();
                    $msg->msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Auto responder got error').': '.implode('; ', $errors);
                    $msg->chat_id = $chat->id;
                    $msg->user_id = -1;
                    $msg->time = time();
            
                    if ($chat->last_msg_id < $msg->id) {
                        $chat->last_msg_id = $msg->id;
                    }
            
                    erLhcoreClassChat::getSession()->save($msg);
                }
            
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
            
        }
        
        // Operator typing
        if ( $chat->is_operator_typing == true) {
            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.syncuser.operator_typing',array('chat' => & $chat));
            $ott = ($chat->operator_typing_user !== false) ? $chat->operator_typing_user->name_support . ' ' . erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','is typing now...') : erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chat','Operator is typing now...');
        }  elseif ($chat->is_operator_typing == false) {
            $ott = '';
        }

        $lastMessageId = isset($_GET['last_message_id']) ? (int)$_GET['last_message_id'] : 0;
        $messages = erLhcoreClassChat::getPendingMessages($chat->id, $lastMessageId);

        if (isset($_GET['ignore_system_messages']) &&  $_GET['ignore_system_messages'] == true)
        {
            foreach ($messages as $key => $data) {
                if ($data['user_id'] == -1) {
                    unset($messages[$key]);
                }
                $lastMessageId = $data['id'];
            }
        }
                
        $checkStatus = false;
        
        // Closed
        $closed = false;
        if ($chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT) {
            $closed = true;
        }
        
        if (isset($_GET['workflow']) && $_GET['workflow'] == true) {
            if ($chat->status_sub == erLhcoreClassModelChat::STATUS_SUB_OWNER_CHANGED) {
                $checkStatus = true;
                $chat->status_sub = erLhcoreClassModelChat::STATUS_SUB_DEFAULT;
                $saveChat = true;
            }
        }
        
        if ($chat->status_sub == erLhcoreClassModelChat::STATUS_SUB_SURVEY_SHOW) {        
            $closed = true;            
        }
        
        if ($chat->status_sub == erLhcoreClassModelChat::STATUS_SUB_CONTACT_FORM) {
            $checkStatus = true;
        }
        
        if (isset($_GET['workflow']) && $_GET['workflow'] == true) {
            if ($chat->operation != '') {
                $operation = explode("\n", trim($chat->operation));
                $chat->operation = '';
                $saveChat = true;
            }
        }
        
        if (isset($_GET['workflow']) && $_GET['workflow'] == true) {
            if ($chat->user_status != 0) {
                $chat->user_status = 0;
                $saveChat = true;
            }
        
            if ($chat->has_unread_op_messages == 1)
            {
                $chat->unread_op_messages_informed = 0;
                $chat->has_unread_op_messages = 0;
                $saveChat = true;
            }
        }
        
        if ($saveChat === true) {
            $chat->updateThis();
        }
        
        erLhcoreClassRestAPIHandler::outputResponse(array(
            'error' => false,
            'result' => array(
            		'messages' => array_values($messages), 
            		'ot' => $ott, 
            		'closed' => $closed, 
            		'check_status' => $checkStatus,
            		'lmid' => $lastMessageId
            )
        ));
        
    } else {
        throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('lhrestapi/validation', 'You do not have permission to read this chat!'));
    }
    
} catch (Exception $e) {
    echo erLhcoreClassRestAPIHandler::outputResponse(array(
        'error' => true,
        'result' => $e->getMessage()
    ));
}

exit();