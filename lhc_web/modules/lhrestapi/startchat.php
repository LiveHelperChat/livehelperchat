<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
header('Content-Type: application/json');

try {
    erLhcoreClassRestAPIHandler::validateRequest();

    $chat = new erLhcoreClassModelChat();

    $inputData = new stdClass();
    $inputData->chatprefill = '';
    $inputData->email = '';
    $inputData->username = '';
    $inputData->phone = '';
    $inputData->product_id = '';

    if (is_array($Params['user_parameters_unordered']['department']) && count($Params['user_parameters_unordered']['department']) == 1) {
        erLhcoreClassChat::validateFilterIn($Params['user_parameters_unordered']['department']);
        $inputData->departament_id = array_shift($Params['user_parameters_unordered']['department']);
    } else {
        $inputData->departament_id = 0;
    }
    
    if (is_array($Params['user_parameters_unordered']['prod'])) {
        erLhcoreClassChat::validateFilterIn($Params['user_parameters_unordered']['prod']);
        $inputData->product_id_array = $Params['user_parameters_unordered']['prod'];
    }

    if (is_numeric($inputData->departament_id) && $inputData->departament_id > 0) {
        $startDataDepartment = erLhcoreClassModelChatStartSettings::findOne(array('filter' => array('department_id' => $inputData->departament_id)));
        if ($startDataDepartment instanceof erLhcoreClassModelChatStartSettings) {
            $startDataFields = $startDataDepartment->data_array;
        }
    } else {
        // Start chat field options
        $startData = erLhcoreClassModelChatConfig::fetch('start_chat_data');
        $startDataFields = (array)$startData->data;
    }

    // Leave a message functionality
    $leaveamessage = ((string)$Params['user_parameters_unordered']['leaveamessage'] == 'true' || (isset($startDataFields['force_leave_a_message']) && $startDataFields['force_leave_a_message'] == true)) ? true : false;
    
    $additionalParams = array();
    if ((string)$Params['user_parameters_unordered']['offline'] == 'true' && $leaveamessage == true) {
        $additionalParams['offline'] = true;
    }

    if (is_array($Params['user_parameters_unordered']['department'])) {
        erLhcoreClassChat::validateFilterIn($Params['user_parameters_unordered']['department']);
        $inputData->departament_id_array = $Params['user_parameters_unordered']['department'];
    }
      
    $inputData->accept_tos = false;
    $inputData->question = '';
    $inputData->operator = (int)$Params['user_parameters_unordered']['operator'];        
    $inputData->username = '';
    $inputData->hash_resume = false;
    $inputData->vid = false;
    $inputData->question = '';
    $inputData->email = '';
    $inputData->phone = '';
    $inputData->validate_start_chat = false;
    $inputData->name_items = array();
    $inputData->value_items = array();
    $inputData->value_sizes = array();
    $inputData->value_types = array();
    $inputData->value_items_admin = array(); // These variables get's filled from start chat form settings
    $inputData->hattr = array();
    $inputData->encattr = array();
    $inputData->via_encrypted = array();
    $inputData->ua = $Params['user_parameters_unordered']['ua'];
    $inputData->priority = is_numeric($Params['user_parameters_unordered']['priority']) ? (int)$Params['user_parameters_unordered']['priority'] : false;
    
    if ((string)$Params['user_parameters_unordered']['vid'] != '') {
        $inputData->vid = (string)$Params['user_parameters_unordered']['vid'];
    }
    
    $additionalParams['ignore_captcha'] = true;
    
    // Validate post data
    $Errors = erLhcoreClassChatValidator::validateStartChat($inputData,$startDataFields,$chat,$additionalParams);

    if (count($Errors) == 0)
    {
        if (isset($_POST['ip'])) {
            $chat->ip = strip_tags($_POST['ip']);
            erLhcoreClassModelChat::detectLocation($chat);
        }
                
        $chat->time = time();
        $chat->status = 0;
        
        $chat->hash = erLhcoreClassChat::generateHash();
        $chat->referrer = isset($_POST['URLRefer']) ? $_POST['URLRefer'] : '';
        $chat->session_referrer = isset($_POST['r']) ? $_POST['r'] : '';
        
        if ( empty($chat->nick) ) {
            $chat->nick = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Visitor');
        }
        
        // Update chat attributes
        $data = $_POST ['data'];
        $jsonData = json_decode ( $data, true );
        
        erLhcoreClassChatValidator::validateUpdateAttribute ( $chat, $jsonData);
        
        // Store chat
        $chat->saveThis();
                
        // Assign chat to user
        if ( erLhcoreClassModelChatConfig::fetch('track_online_visitors')->current_value == 1 ) {           
           
            // To track online users            
            $userInstance = erLhcoreClassModelChatOnlineUser::handleRequest(array('check_message_operator' => true, 'message_seen_timeout' => erLhcoreClassModelChatConfig::fetch('message_seen_timeout')->current_value, 'vid' => $Params['user_parameters_unordered']['vid']));
            
            if ($userInstance !== false) {
                
                if (isset($_POST['proactive']) && $_POST['proactive'] == 1 && $userInstance->has_message_from_operator) {
                    
                    // Store Message from operator
                    $msg = new erLhcoreClassModelmsg();
                    $msg->msg = trim($userInstance->operator_message);
                    $msg->chat_id = $chat->id;
                    $msg->name_support = (string)($userInstance->operator_user !== false ? trim($userInstance->operator_user->name_support) : (!empty($userInstance->operator_user_proactive) ? $userInstance->operator_user_proactive : erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Live Support')));
                    $msg->user_id = $userInstance->operator_user_id > 0 ? $userInstance->operator_user_id : -2;
                    $msg->time = time()-7; // Deduct 7 seconds so for user all looks more natural
                    
                    erLhcoreClassChat::getSession()->save($msg);
                    
                    $chat->last_msg_id = $msg->id;
                    $chat->saveThis();
                }
                
                $userInstance->chat_id = $chat->id;
                $userInstance->dep_id = $chat->dep_id;
                $userInstance->message_seen = 1;
                $userInstance->message_seen_ts = time();
                $userInstance->saveThis();
                        
                $chat->online_user_id = $userInstance->id;
                $chat->saveThis();
                 
                if ( erLhcoreClassModelChatConfig::fetch('track_footprint')->current_value == 1) {
                    erLhcoreClassModelChatOnlineUserFootprint::assignChatToPageviews($userInstance);
                }
            }
        }
        
        $messageInitial = false;
        
        // Store message if required
        if (isset($startDataFields['message_visible_in_page_widget']) && $startDataFields['message_visible_in_page_widget'] == true) {
            if ( $inputData->question != '') {
                // Store question as message
                $msg = new erLhcoreClassModelmsg();
                $msg->msg = trim($inputData->question);
                $msg->chat_id = $chat->id;
                $msg->user_id = 0;
                $msg->time = time();
                erLhcoreClassChat::getSession()->save($msg);
        
                $messageInitial = $msg;
        
                $chat->unanswered_chat = 1;
                $chat->last_msg_id = $msg->id;
                $chat->saveThis();
            }
        }
        
        // Auto responder does not make sense in this mode       
        $responder = erLhAbstractModelAutoResponder::processAutoResponder($chat);
    
        if ($responder instanceof erLhAbstractModelAutoResponder) {
            $beforeAutoResponderErrors = array();
            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.before_auto_responder_triggered',array('chat' => & $chat, 'errors' => & $beforeAutoResponderErrors));
    
            if (empty($beforeAutoResponderErrors)) {
                $chat->wait_timeout = $responder->wait_timeout;
                $chat->timeout_message = $responder->timeout_message;
                $chat->wait_timeout_send = 1 - $responder->repeat_number;
                $chat->wait_timeout_repeat = $responder->repeat_number;
    
                if ($responder->wait_message != '') {
                    $msg = new erLhcoreClassModelmsg();
                    $msg->msg = trim($responder->wait_message);
                    $msg->chat_id = $chat->id;
                    $msg->name_support = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Live Support');
                    $msg->user_id = -2;
                    $msg->time = time() + 5;
                    erLhcoreClassChat::getSession()->save($msg);
    
                    if ($chat->last_msg_id < $msg->id) {
                        $chat->last_msg_id = $msg->id;
                    }
                }
    
                $chat->saveThis();
    
                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.auto_responder_triggered', array('chat' => & $chat));
            } else {
                $msg = new erLhcoreClassModelmsg();
                $msg->msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Auto responder got error').': '.implode('; ', $beforeAutoResponderErrors);
                $msg->chat_id = $chat->id;
                $msg->user_id = -1;
                $msg->time = time();

                if ($chat->last_msg_id < $msg->id) {
                    $chat->last_msg_id = $msg->id;
                }

                erLhcoreClassChat::getSession()->save($msg);
            }
        }

        erLhcoreClassChat::updateDepartmentStats($chat->department);

        erLhcoreClassChat::prefillGetAttributesObject($chat, array('user','plain_user_name'), array('user'), array('do_not_clean' => true));
        
        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.chat_started',array('chat' => & $chat, 'msg' => $messageInitial));

        echo erLhcoreClassRestAPIHandler::outputResponse(array('error' => false, 'result' => array('chat' => $chat->getState())));
    } else {
        echo erLhcoreClassRestAPIHandler::outputResponse(array('error' => true, 'result' => array('errors' => $Errors)));
    }

} catch (Exception $e) {
    echo erLhcoreClassRestAPIHandler::outputResponse(array(
        'error' => true,
        'result' => array('errors' => $e->getMessage(), 'trace' => $e->getTraceAsString())
    ));
}

exit();