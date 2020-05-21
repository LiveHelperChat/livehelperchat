<?php

try {
    erLhcoreClassRestAPIHandler::validateRequest();
    
    $chat = erLhcoreClassModelChat::fetch((int)$_GET['chat_id']);

    // Try to find chat in archive
    if (!($chat instanceof erLhcoreClassModelChat)) {
        $chatData = erLhcoreClassChatArcive::fetchChatById((int)$_GET['chat_id']);
        if (!($chatData['chat'] instanceof erLhcoreClassModelChatArchive)) {
            throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('lhrestapi/validation', 'Could not find chat by chat_id!'));
        } else {
            $chat = $chatData['chat'];
            $chat->archive = $chatData['archive'];
        }
    } else {
        $chat->archive = null;
    }

    if (erLhcoreClassRestAPIHandler::hasAccessToRead($chat) == true) {

        $saveChat = false;

        if (isset($_GET['workflow']) && $_GET['workflow'] == true && $chat->archive === null) {
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
        
        if ($chat->archive === null) {
            $messages = erLhcoreClassChat::getPendingMessages($chat->id, $lastMessageId);
        } else {
            $messages = erLhcoreClassChatArcive::getPendingMessages($chat->id, $lastMessageId);
        }

        if (isset($_GET['ignore_system_messages']) && ($_GET['ignore_system_messages'] == 'true' || $_GET['ignore_system_messages'] == '1'))
        {
            foreach ($messages as $key => $data) {
                if ($data['user_id'] == -1) {
                    unset($messages[$key]);
                }
                $lastMessageId = $data['id'];
            }
        }

        // Extract media within message if required
        if (isset($_GET['extract_media']) && ($_GET['extract_media'] == 'true' || $_GET['extract_media'] == '1')) {
            foreach ($messages as $key => $msg) {
                $matches = array();

                $msg_text_cleaned = $msg['msg'];

                preg_match_all('/\[file="?(.*?)"?\]/', $msg_text_cleaned, $matches);

                $media = array();

                foreach ($matches[1] as $index => $body) {
                    $parts = explode('_', $body);
                    $fileID = $parts[0];
                    $hash = $parts[1];
                    try {
                        $file = erLhcoreClassModelChatFile::fetch($fileID);
                        if (is_object($file) && $hash == $file->security_hash) {

                            $url = (erLhcoreClassSystem::$httpsMode == true ? 'https:' : 'http:') . '//' . $_SERVER['HTTP_HOST'] . erLhcoreClassDesign::baseurldirect('file/downloadfile') . "/{$file->id}/{$hash}";

                            $media[] = array(
                                'id' => $file->id,
                                'size' => $file->size,
                                'upload_name' => $file->upload_name,
                                'type' => $file->type,
                                'extension' => $file->extension,
                                'hash' => $hash,
                                'url' => $url,
                            );

                            $msg_text_cleaned = str_replace($matches[0][$index],'',$msg_text_cleaned);
                        }

                    } catch (Exception $e) {

                    }
                }

                // Set message text to empty if only file was send
                if (trim($msg_text_cleaned) == '' && isset($_GET['remove_media']) && ($_GET['remove_media'] == 'true' || $_GET['remove_media'] == '1') ) {
                    $messages[$key]['msg'] = '';
                }

                $messages[$key]['media'] = $media;

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

        if (isset($_GET['as_html']) && ($_GET['as_html'] == 'true' || $_GET['as_html'] == '1')) {
            foreach ($messages as $key => $msg) {
                $messages[$key]['msg'] = erLhcoreClassBBCode::make_clickable(htmlspecialchars($msg['msg']), array('sender' => $msg['user_id']));
            }
        }

        if (isset($_GET['file_as_link']) && ($_GET['file_as_link'] == 'true' || $_GET['file_as_link'] == '1')) {
            foreach ($messages as $key => $msg) {
                $messages[$key]['msg'] = erLhcoreClassBBCodePlain::make_clickable($msg['msg'], array('sender' => $msg['user_id']));
            }
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