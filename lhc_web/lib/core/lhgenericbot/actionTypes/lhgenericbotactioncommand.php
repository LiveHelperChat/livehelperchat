<?php

class erLhcoreClassGenericBotActionCommand {

    public static function process($chat, $action, $trigger, $params)
    {
        if (isset($params['presentation']) && $params['presentation'] == true) {
            return;
        }

        $params['current_trigger'] = $trigger;

        if (!isset($params['first_trigger'])) {
            $params['first_trigger'] = $params['current_trigger'];
        }
        
        if ($action['content']['command'] == 'stopchat') {

            $filterOnline = array(
                'exclude_bot' => true,
                'exclude_online_hours' => (isset($action['content']['payload_ignore_dep_hours']) && $action['content']['payload_ignore_dep_hours'] == true),
            );

            $isOnlineUser = true;

            if (
                isset($action['content']['payload_attr']) &&
                isset($action['content']['payload_val']) &&
                isset($action['content']['payload_val']) &&
                $action['content']['payload_val'] != '' &&
                $action['content']['payload_attr'] != ''
            ) {
                $user = erLhcoreClassModelUser::findOne(array('filter' => array(erLhcoreClassGenericBotWorkflow::translateMessage($action['content']['payload_attr'], array('chat' => $chat, 'args' => $params))  => erLhcoreClassGenericBotWorkflow::translateMessage($action['content']['payload_val'], array('chat' => $chat, 'args' => $params)))));

                if ($user instanceof erLhcoreClassModelUser) {
                    $filterOnline['user_id'] = $user->id;
                } else {
                    $isOnlineUser = false;
                }
            }

            if ($isOnlineUser === true) {
                $isOnline = (isset($action['content']['payload_ignore_status']) && $action['content']['payload_ignore_status'] == true) || erLhcoreClassChat::isOnline($chat->dep_id,false, $filterOnline);
            } else {
                $isOnline = false;
            }

            if ($isOnline == false && isset($action['content']['payload']) && is_numeric($action['content']['payload'])) {

                $handler = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.genericbot_chat_command_transfer', array(
                    'action' => $action,
                    'chat' => & $chat,
                    'is_online' => false
                ));

                if ($handler !== false) {
                    $trigger = $handler['trigger'];
                } else {
                    $trigger = erLhcoreClassModelGenericBotTrigger::fetch($action['content']['payload']);
                }

                if (($trigger instanceof erLhcoreClassModelGenericBotTrigger)){
                    erLhcoreClassGenericBotWorkflow::processTrigger($chat, $trigger, true);
                }

            } else if ($isOnline == true) {

                $chat->status = erLhcoreClassModelChat::STATUS_PENDING_CHAT;
                $chat->status_sub_sub = 2; // Will be used to indicate that we have to show notification for this chat if it appears on list
                $chat->pnd_time = time()/* + 3*/;

                if (isset($filterOnline['user_id'])) {
                    $chat->user_id = (int)$filterOnline['user_id'];
                }

                if ($chat->transfer_if_na == 1) {
                    $chat->transfer_timeout_ts = time();
                }

                // We do not have to set this
                // Because it triggers auto responder of not replying
                // $chat->last_op_msg_time = time();
                $chat->updateThis();

                // We have to reset auto responder
                if ($chat->auto_responder instanceof erLhAbstractModelAutoResponderChat) {
                    $chat->auto_responder->wait_timeout_send = 0;
                    $chat->auto_responder->pending_send_status = 0;
                    $chat->auto_responder->active_send_status = 0;
                    $chat->auto_responder->updateThis();
                }

                // If chat is transferred to pending state we don't want to process any old events
                $eventPending = erLhcoreClassModelGenericBotChatEvent::findOne(array('filter' => array('chat_id' => $chat->id)));

                if ($eventPending instanceof erLhcoreClassModelGenericBotChatEvent) {
                    $eventPending->removeThis();
                }

                // Because we want that mobile app would receive notification
                // By default these listeners are not set if visitors sends a message and chat is not active
                if (erLhcoreClassChatEventDispatcher::getInstance()->disableMobile == true && erLhcoreClassChatEventDispatcher::getInstance()->globalListenersSet == true) {
                    erLhcoreClassChatEventDispatcher::getInstance()->disableMobile = false;
                    erLhcoreClassChatEventDispatcher::getInstance()->globalListenersSet = false;
                    erLhcoreClassChatEventDispatcher::getInstance()->setGlobalListeners();
                }

                $handler = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.genericbot_chat_command_transfer', array(
                    'action' => $action,
                    'chat' => & $chat,
                    'is_online' => true
                ));

                if ($handler !== false) {
                    $trigger = $handler['trigger'];
                } else {
                    if (isset($action['content']['payload_online']) && is_numeric($action['content']['payload_online'])) {
                        $trigger = erLhcoreClassModelGenericBotTrigger::fetch($action['content']['payload_online']);
                    } else {
                        $trigger = null;
                    }
                }

                if (($trigger instanceof erLhcoreClassModelGenericBotTrigger)){
                    erLhcoreClassGenericBotWorkflow::processTrigger($chat, $trigger, true);
                }
            }

        } elseif ($action['content']['command'] == 'transfertobot') {
            $chat->status = erLhcoreClassModelChat::STATUS_BOT_CHAT;
            $chat->last_op_msg_time = time();
            $chat->updateThis();

            if (isset($action['content']['payload']) && is_numeric($action['content']['payload'])) {

                $handler = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.genericbot_chat_command_transfer', array(
                    'action' => $action,
                    'chat' => & $chat,
                ));

                if ($handler !== false) {
                    $trigger = $handler['trigger'];
                } else {
                    $trigger = erLhcoreClassModelGenericBotTrigger::fetch($action['content']['payload']);
                }

                if (($trigger instanceof erLhcoreClassModelGenericBotTrigger)){
                    erLhcoreClassGenericBotWorkflow::processTrigger($chat, $trigger, true);
                }
            }
        } elseif ($action['content']['command'] == 'closechat') {

            $chat->pnd_time = time();
            $chat->last_op_msg_time = time();
            $chat->has_unread_messages = 0;

            if (isset($action['content']['close_widget']) && $action['content']['close_widget'] == true) {
                // Send execute JS message
                $msg = new erLhcoreClassModelmsg();
                $msg->msg = '';
                $msg->meta_msg = '{"content":{"execute_js":{"chat_event":"endChat","payload":""}}}';
                $msg->chat_id = $chat->id;
                $msg->user_id = isset($params['override_user_id']) && $params['override_user_id'] > 0 ? (int)$params['override_user_id'] : -2;
                $msg->time = time();
                if (isset($params['override_nick']) && !empty($params['override_nick'])) {
                    $msg->name_support = (string)$params['override_nick'];
                } else {
                    $msg->name_support = erLhcoreClassGenericBotWorkflow::getDefaultNick($chat);
                }
                $msg->saveThis();
            }

            $handler = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.genericbot_chat_command_transfer', array(
                'action' => $action,
                'chat' => & $chat,
            ));

            if ($handler === false) {
                erLhcoreClassChatHelper::closeChat(array(
                    'chat' => & $chat,
                    'bot' => true
                ));
            }

        } elseif ($action['content']['command'] == 'chatattribute') {
            
            $variablesArray = (array)$chat->additional_data_array;

            $variablesAppend = json_decode($action['content']['payload'],true);

            if (is_array($variablesAppend)) {

                $updatedIdentifiers = array();

                // Update and insert new one.
                foreach ($variablesAppend as $value) {
                    if (isset($value['identifier']) && isset($value['key']) && $value['key'] != '' && $value['identifier'] != '') {
                        foreach ($variablesArray as $indexVariable => $variableData) {
                            if ($variableData['identifier'] == $value['identifier']) {

                                $updatedIdentifiers[] = $value['identifier'];

                                // Update only if empty and this variable is not empty
                                if (isset($action['content']['update_if_empty']) && $action['content']['update_if_empty'] == true && isset($variableData['value']) && $variableData['value'] != '' && $variableData['value'] != '0') {
                                    continue;
                                }

                                if (isset($value['value'])) {
                                    if (!is_numeric($value['value']) && !is_bool($value['value'])) {
                                        $valueItem = isset($params['replace_array']) ? str_replace(array_keys($params['replace_array']),array_values($params['replace_array']),$value['value']) : $value['value'];
                                        $variablesArray[$indexVariable]['value'] = erLhcoreClassGenericBotWorkflow::translateMessage($valueItem, array('chat' => $chat, 'args' => $params));
                                    } else {
                                        $variablesArray[$indexVariable]['value'] = $value['value'];
                                    }
                                } else {
                                    unset($variablesArray[$indexVariable]);
                                }

                            }
                        }
                    }
                }

                foreach ($variablesAppend as $value) {
                    if (isset($value['identifier']) && isset($value['key']) && isset($value['value']) && $value['key'] != '' && $value['identifier'] != '' && !in_array($value['identifier'],$updatedIdentifiers)) {

                        if (!is_numeric($value['value']) && !is_bool($value['value'])) {
                            $valueItem = (isset($params['replace_array']) ? str_replace(array_keys($params['replace_array']), array_values($params['replace_array']), $value['value']) : $value['value']);
                            $valueItem = erLhcoreClassGenericBotWorkflow::translateMessage($valueItem, array('chat' => $chat, 'args' => $params));
                        } else {
                            $valueItem = $value['value'];
                        }

                        $variablesArray[] = array(
                            'identifier' => $value['identifier'],
                            'key' => $value['key'],
                            'value' => $valueItem
                        );
                    }
                }

                $variablesArray = array_values($variablesArray);

                $chat->additional_data = json_encode($variablesArray);
                $chat->additional_data_array = $variablesArray;
                $chat->updateThis(array('update' => array('additional_data')));
            }

        } elseif ($action['content']['command'] == 'chatvariable') {

                $variablesArray = (array)$chat->chat_variables_array;

                if (isset($params['replace_array']) && is_array($params['replace_array'])) {
                    $variablesAppend = @str_replace(array_keys($params['replace_array']),array_values($params['replace_array']),$action['content']['payload']);
                } else {
                    $variablesAppend = $action['content']['payload'];
                }

                $variablesAppend = json_decode(erLhcoreClassGenericBotWorkflow::translateMessage($variablesAppend, array('as_json' => true, 'chat' => $chat, 'args' => $params)), true);

                if (is_array($variablesAppend)) {
                    foreach ($variablesAppend as $key => $value) {

                        // Update only if empty and this variable is not empty
                        if (isset($action['content']['update_if_empty']) && $action['content']['update_if_empty'] == true && isset($variablesArray[$key]) && $variablesArray[$key] != '' && $variablesArray[$key] != '0') {
                            continue;
                        }

                        if (isset($value)) {
                             $variablesArray[$key] = $value;
                        } elseif (isset($variablesArray[$key])) {
                            unset($variablesArray[$key]);
                        }
                    }
                }

                $chat->chat_variables = json_encode($variablesArray);
                $chat->chat_variables_array = $variablesArray;
                $chat->saveThis();

        } elseif ($action['content']['command'] == 'setchatattribute') {

                // Replace variables if any
                // Todo make sure object is not used during replacement
                $action['content']['payload_arg'] = isset($params['replace_array']) ? @str_replace(array_keys($params['replace_array']),array_values($params['replace_array']),$action['content']['payload_arg']) : $action['content']['payload_arg'];

                $eventArgs = array('old' => $chat->{$action['content']['payload']}, 'attr' => $action['content']['payload'], 'new' => $action['content']['payload_arg']);

                // Update only if empty
                if (
                    isset($action['content']['update_if_empty']) && $action['content']['update_if_empty'] == true && trim($chat->{$action['content']['payload']}) != '' && $chat->{$action['content']['payload']} != '0'
                ) {
                    return ;
                }

                $chat->{$action['content']['payload']} = erLhcoreClassGenericBotWorkflow::translateMessage($action['content']['payload_arg'], array('chat' => $chat, 'args' => $params));

                $updateDepartmentStats = false;

                if ($eventArgs['attr'] == 'dep_id' && $eventArgs['old'] != $action['content']['payload_arg']) {
                    erLhAbstractModelAutoResponder::updateAutoResponder($chat);

                    $department = erLhcoreClassModelDepartament::fetch($chat->dep_id);

                    if ($department instanceof erLhcoreClassModelDepartament) {
                        if ($department->department_transfer_id > 0) {
                            $chat->transfer_if_na = 1;
                            $chat->transfer_timeout_ts = time();
                            $chat->transfer_timeout_ac = $department->transfer_timeout;
                        }

                        if ($department->inform_unread == 1) {
                            $chat->reinform_timeout = $department->inform_unread_delay;
                        }

                        if ($department->priority > $chat->priority) {
                            $chat->priority = $department->priority;
                        }

                        $updateDepartmentStats = true;

                    }
                }

                if ($eventArgs['attr'] == 'status' && $eventArgs['old'] != $action['content']['payload_arg']) {
                    $chat->pnd_time = time();
                }

                if ($eventArgs['attr'] == 'user_id' && $eventArgs['old'] != $action['content']['payload_arg']) {
                    $chat->status_sub = erLhcoreClassModelChat::STATUS_SUB_OWNER_CHANGED;
                }

                $chat->saveThis();

                if ($updateDepartmentStats == true) {
                    erLhcoreClassChat::updateDepartmentStats($department);
                }

                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.data_changed', array('chat' => & $chat));

        } elseif ($action['content']['command'] == 'setdepartment') {

            // Department was changed
            if ($chat->dep_id != $action['content']['payload']) {

                $department = erLhcoreClassModelDepartament::fetch($action['content']['payload']);

                if ($department instanceof erLhcoreClassModelDepartament) {
                    $chat->dep_id = $department->id;

                    erLhAbstractModelAutoResponder::updateAutoResponder($chat);

                    if ($department->department_transfer_id > 0) {
                        $chat->transfer_if_na = 1;
                        $chat->transfer_timeout_ts = time();
                        $chat->transfer_timeout_ac = $department->transfer_timeout;
                    }

                    if ($department->inform_unread == 1) {
                        $chat->reinform_timeout = $department->inform_unread_delay;
                    }

                    if ($department->priority > $chat->priority) {
                        $chat->priority = $department->priority;
                    }

                    $chat->saveThis();

                    erLhcoreClassChat::updateDepartmentStats($department);
                }
            }
        } elseif ($action['content']['command'] == 'setliveattr') {

            if (isset($action['content']['remove_subject']) && $action['content']['remove_subject'] == true) {
                $payload = array(
                    "chat_emit" => "attr_rem",
                    "ext_args" => json_encode(['attr' => json_decode(erLhcoreClassGenericBotWorkflow::translateMessage($action['content']['payload'], array('chat' => $chat, 'args' => $params)),true)], JSON_HEX_APOS), // Path of the attribute
                );
            } else {
                $valueAttribute = erLhcoreClassGenericBotWorkflow::translateMessage($action['content']['payload_arg'], array('chat' => $chat, 'args' => $params));

                if (isset($action['content']['remove_if_empty']) && $action['content']['remove_if_empty'] == true && empty($valueAttribute)) {
                    $payload = array(
                        "chat_emit" => "attr_rem",
                        "ext_args" => json_encode(['attr' => json_decode(erLhcoreClassGenericBotWorkflow::translateMessage($action['content']['payload'], array('chat' => $chat, 'args' => $params)),true)], JSON_HEX_APOS), // Path of the attribute
                    );
                } else {
                    $payload = array(
                        "chat_emit" => "attr_set",
                        "ext_args" => json_encode(['attr' => json_decode(erLhcoreClassGenericBotWorkflow::translateMessage($action['content']['payload'], array('chat' => $chat, 'args' => $params)),true), 'data' => json_decode($valueAttribute)], JSON_HEX_APOS), // Path of the attribute
                    );
                }
            }

            // Store as message to visitor
            $msg = new erLhcoreClassModelmsg();
            $msg->msg = '';
            $msg->meta_msg = json_encode(array(
                'content' => array(
                    'execute_js' => $payload
                )
            ));
            $msg->chat_id = $chat->id;
            $msg->user_id = isset($params['override_user_id']) && $params['override_user_id'] > 0 ? (int)$params['override_user_id'] : -2;
            $msg->time = time();
            if (isset($params['override_nick']) && !empty($params['override_nick'])) {
                $msg->name_support = (string)$params['override_nick'];
            } else {
                $msg->name_support = erLhcoreClassGenericBotWorkflow::getDefaultNick($chat);
            }
            $msg->saveThis();

            // Update last user msg time so auto responder work's correctly
            $chat->last_op_msg_time = $chat->last_user_msg_time = time();
            $chat->last_msg_id = $msg->id;

            // All ok, we can make changes
            $chat->updateThis(array('update' => array('last_msg_id', 'last_op_msg_time', 'status_sub', 'last_user_msg_time')));

        } elseif ($action['content']['command'] == 'removeprocess') {

            $q = ezcDbInstance::get()->createDeleteQuery();

            // Repeat counter remove
            $q->deleteFrom( 'lh_generic_bot_repeat_restrict' )->where( $q->expr->eq( 'chat_id', $chat->id ) );
            $stmt = $q->prepare();
            $stmt->execute();

            // Bot chat event remove
            $q->deleteFrom( 'lh_generic_bot_chat_event' )->where( $q->expr->eq( 'chat_id', $chat->id ) );
            $stmt = $q->prepare();
            $stmt->execute();

            // Bot chat event remove
            $q->deleteFrom( 'lh_generic_bot_pending_event' )->where( $q->expr->eq( 'chat_id', $chat->id ) );
            $stmt = $q->prepare();
            $stmt->execute();
            
        } elseif ($action['content']['command'] == 'setsubject') {

            $remove = isset($action['content']['remove_subject']) && $action['content']['remove_subject'] == true;
            if ($remove == true && is_numeric($action['content']['payload'])) {
                $subjectChat = erLhAbstractModelSubjectChat::findOne(array('filter' => array('subject_id' => (int)$action['content']['payload'], 'chat_id' => $chat->id)));
                if ($subjectChat instanceof erLhAbstractModelSubjectChat) {
                    $subjectChat->removeThis();
                    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.subject_remove',array('chat' => & $chat));
                }
            } else if (is_numeric($action['content']['payload']) && ($subject = erLhAbstractModelSubject::fetch((int)$action['content']['payload'])) instanceof erLhAbstractModelSubject) {
                $subjectChat = erLhAbstractModelSubjectChat::findOne(array('filter' => array('subject_id' => (int)$action['content']['payload'], 'chat_id' => $chat->id)));
                if (!($subjectChat instanceof erLhAbstractModelSubjectChat)) {
                    $subjectChat = new erLhAbstractModelSubjectChat();
                    $subjectChat->subject_id = $subject->id;
                    $subjectChat->chat_id = $chat->id;
                    $subjectChat->saveThis();
                    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.subject_add',array('chat' => & $chat));
                }
            }

        } elseif ($action['content']['command'] == 'dispatchevent') {

                $valueTranslated = isset($action['content']['payload_arg']) ? $action['content']['payload_arg'] : '';

                if (isset($params['replace_array'])) {
                    $valueTranslated = @str_replace(array_keys($params['replace_array']),array_values($params['replace_array']), $valueTranslated);
                }

                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.genericbot_chat_command_dispatch_event', array(
                    'action' => $action,
                    'payload_translated' => erLhcoreClassGenericBotWorkflow::translateMessage($valueTranslated, array('chat' => $chat, 'args' => $params)),
                    'chat' => & $chat,
                    'params_dispatch' => $params,
                    'replace_array' => (isset($params['replace_array']) ? $params['replace_array'] : [])
                ));
        }
    }
}

?>