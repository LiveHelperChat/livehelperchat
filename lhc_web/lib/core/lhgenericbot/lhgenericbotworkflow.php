<?php

class erLhcoreClassGenericBotWorkflow {

    public static function findEvent($text, $botId, $type = 0)
    {
        $event = erLhcoreClassModelGenericBotTriggerEvent::findOne(array('filter' => array('bot_id' => $botId, 'type' => $type),'filterlikeright' => array('pattern' => $text)));
        return $event;
    }

    public static $currentEvent = null;

    public static function userMessageAdded(& $chat, $msg) {

        // Try to find current callback handler just
        $chatEvent = erLhcoreClassModelGenericBotChatEvent::findOne(array('filter' => array('chat_id' => $chat->id)));
        if ($chatEvent instanceof erLhcoreClassModelGenericBotChatEvent) {
            self::$currentEvent = $chatEvent;
            self::processEvent($chatEvent, $chat, array('msg' => $msg));
            return;
        }

        // Try to find current workflow
        $workflow = erLhcoreClassModelGenericBotChatWorkflow::findOne(array('filterin' => array('status' => array(0,1)), 'filter' => array('chat_id' => $chat->id)));
        if ($workflow instanceof erLhcoreClassModelGenericBotChatWorkflow) {
            self::processWorkflow($workflow, $chat, array('msg' => $msg));
            return;
        }

        // There is no current workflow in progress                
        $handler = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.genericbot_get_message', array(
                    'chat' => & $chat,
                    'msg' => $msg,
                    'payload' => $msg->msg,
        ));
        
        if ($handler !== false) {
            $event = $handler['event'];
        } else {
            // There is no current workflow in progress
            $event = self::findEvent($msg->msg, $chat->chat_variables_array['gbot_id']);
        }           

        if ($event instanceof erLhcoreClassModelGenericBotTriggerEvent) {
            self::processTrigger($chat, $event->trigger);
            return;
        }

        self::sendDefault($chat, $chat->chat_variables_array['gbot_id']);
    }

    // Send default message if there is any
    public static function sendDefault(& $chat, $botId)
    {
        $trigger = erLhcoreClassModelGenericBotTrigger::findOne(array('filter' => array('bot_id' => $botId, 'default_unknown' => 1)));

        if ($trigger instanceof erLhcoreClassModelGenericBotTrigger) {
            $message = erLhcoreClassGenericBotWorkflow::processTrigger($chat, $trigger);

            if (isset($message) && $message instanceof erLhcoreClassModelmsg) {
                self::setLastMessageId($chat->id, $message->id);
            }
        }
    }

    public static function processEvent($chatEvent, & $chat, $params = array()) {

        if (isset($params['msg'])) {
            $payload = $params['msg']->msg;
        } else {
            $payload =  $params['payload'];
        }

        $db = ezcDbInstance::get();

        try {

            // Event was processed we can remove it now
            foreach ($chatEvent->content_array['callback_list'] as $eventData) {

                // Perhaps there is extension which listens for a specific event
                if (isset($eventData['content']['event']) && !empty($eventData['content']['event'])) {
                    $handler = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.genericbot_event_handler', array(
                        'render' => $eventData['content']['event'],
                        'render_args' => array(),
                        'chat' => & $chat,
                        'event' => & $chatEvent,
                        'event_data' => $eventData,
                        'payload' => & $payload,
                    ));
                }

                if (isset($handler) && $handler !== false && isset($handler['render']) && is_callable($handler['render'])){

                    // Extension itself has to update chat
                    $dataProcess = call_user_func_array($handler['render'], $handler['render_args']);

                    if (isset($dataProcess['valid']) && $dataProcess['valid'] == false) {
                        if (isset($dataProcess['message']) && !empty($dataProcess['message'])) {
                            throw new Exception($dataProcess['message']);
                        } else {
                            throw new Exception('Your message does not match required format!');
                        }
                    } elseif (!isset($dataProcess['valid'])) {
                        throw new Exception('Returned format is incorrect and data could not be validated!');
                    }

                } else {
                    if ($eventData['content']['type'] == 'chat') {
                        if ($eventData['content']['field'] == 'email') {
                            if (filter_var($payload, FILTER_VALIDATE_EMAIL)) {
                                $q = $db->createUpdateQuery();
                                $q->update( 'lh_chat' )
                                    ->set( 'email', $q->bindValue($payload) )
                                    ->where( $q->expr->eq( 'id', $chat->id ) );
                                $stmt = $q->prepare();
                                $stmt->execute();
                                $chat->email = $payload;

                            } else {
                                throw new Exception('Invalid e-mail address');
                            }
                        } else if ($eventData['content']['field'] == 'phone') {

                            if ($payload == '' || mb_strlen($payload) < erLhcoreClassModelChatConfig::fetch('min_phone_length')->current_value) {
                                throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Please enter your phone'));
                            }

                            if (mb_strlen($payload) > 100)
                            {
                                throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Maximum 100 characters for phone'));
                            }

                            $q = $db->createUpdateQuery();
                            $q->update( 'lh_chat' )
                                ->set( 'phone', $q->bindValue($payload) )
                                ->where( $q->expr->eq( 'id', $chat->id ) );
                            $stmt = $q->prepare();
                            $stmt->execute();
                            $chat->phone = $payload;
                        }
                    }
                }

                // Success message
                if (isset($eventData['content']['success_message']) && !empty($eventData['content']['success_message'])) {
                    self::sendAsBot($chat, $eventData['content']['success_message']);
                }

                // Initiate payload based callback if there is any
                if (isset($eventData['content']['success_callback']) && !empty($eventData['content']['success_callback'])) {
                    self::reprocessPayload($eventData['content']['success_callback'], $chat, 1);
                }

                // Initiate text based callback if there is any
                if (isset($eventData['content']['success_text_pattern']) && !empty($eventData['content']['success_text_pattern'])) {
                    self::reprocessPayload($eventData['content']['success_text_pattern'], $chat, 0);
                }
            }

            $chatEvent->removeThis();

        } catch (Exception $e) {
             self::sendAsBot($chat, $e->getMessage());
        }
    }

    public static function reprocessPayload($payload, $chat, $type = 1)
    {
        $event = self::findEvent($payload, $chat->chat_variables_array['gbot_id'], $type);

        if ($event instanceof erLhcoreClassModelGenericBotTriggerEvent) {
            $message = self::processTrigger($chat, $event->trigger);
        }

        if (isset($message) && $message instanceof erLhcoreClassModelmsg) {
            self::setLastMessageId($chat->id, $message->id);
        } else {
            if (erConfigClassLhConfig::getInstance()->getSetting( 'site', 'debug_output' ) == true) {
                self::sendAsBot($chat,erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Button action could not be found!'));
            }
        }
    }

    public static function processWorkflow($workflow, & $chat, $params = array())
    {

        $reprocess = false;
        try {
            $currentStep = $workflow->collected_data_array['current_step'];
            $currentStepId = $workflow->collected_data_array['step'];

            if (isset($params['msg'])) {
                $payload = $params['msg']->msg;
            } else {
                $payload =  $params['payload'];
            }

            if ($currentStep['type'] == 'text') {

                if (isset($currentStep['content']['validation_callback']) && !empty($currentStep['content']['validation_callback'])) {
                    $handler = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.genericbot_handler', array(
                        'render' => $currentStep['content']['validation_callback'],
                        'render_args' => (isset($currentStep['content']['validation_argument']) ? $currentStep['content']['validation_argument'] : null),
                        'chat' => & $chat,
                        'workflow' => & $workflow,
                        'payload' => & $payload,
                    ));

                    if ($handler !== false && isset($handler['render']) && is_callable($handler['render'])) {

                        $dataProcess = call_user_func_array($handler['render'], $handler['render_args']);

                        if ($dataProcess['valid'] == false) {
                            if (isset($dataProcess['message']) && !empty($dataProcess['message'])){
                                throw new Exception($dataProcess['message']);
                            } else if (isset($currentStep['content']['validation_error']) && !empty($currentStep['content']['validation_error'])){
                                throw new Exception($currentStep['content']['validation_error']);
                            } else {
                                throw new Exception('Your message does not match required format!');
                            }
                        }
                    }
                }

                if (isset($currentStep['content']['validation']) && !empty($currentStep['content']['validation'])) {
                    if (!preg_match('/' . $currentStep['content']['validation'] . '/',$payload)) {
                        if (isset($currentStep['content']['validation_error']) && !empty($currentStep['content']['validation_error'])){
                            throw new Exception($currentStep['content']['validation_error']);
                        } else {
                            throw new Exception('Your message does not match required format!');
                        }
                    }
                }

                $workflow->collected_data_array['collected'][$currentStep['content']['field']] = array(
                    'value' => $payload,
                    'name' => $currentStep['content']['name']
                );

            } else if ($currentStep['type'] == 'email') {
                if (filter_var($payload, FILTER_VALIDATE_EMAIL)) {
                    $workflow->collected_data_array['collected'][$currentStep['content']['field']] = array(
                        'value' => $payload,
                        'name' => $currentStep['content']['name']
                    );
                } else {
                    throw new Exception('Invalid e-mail address');
                }
            } else if ($currentStep['type'] == 'phone') {

                if ($payload == '' || mb_strlen($payload) < erLhcoreClassModelChatConfig::fetch('min_phone_length')->current_value) {
                    throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Please enter your phone'));
                }

                if (mb_strlen($payload) > 100)
                {
                    throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Maximum 100 characters for phone'));
                }

                $workflow->collected_data_array['collected'][$currentStep['content']['field']] = array(
                    'value' => $payload,
                    'name' => $currentStep['content']['name']
                );

            } else if ($currentStep['type'] == 'dropdown') {

                $handler = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.genericbot_handler', array(
                    'render' => $currentStep['content']['provider_dropdown'],
                    'render_args' => (isset($currentStep['content']['provider_argument']) ? $currentStep['content']['provider_argument'] : null),
                    'chat' => & $chat,
                    'workflow' => & $workflow,
                    'payload' => & $payload,
                ));

                if ($handler !== false && isset($handler['render']) && is_callable($handler['render'])) {

                    $content = array(
                        'content' => array(
                            'dropdown' => array(
                                'provider_dropdown' => $handler['render'],
                                'provider_name' => $currentStep['content']['provider_name'],
                                'provider_id' => $currentStep['content']['provider_id'],
                            )
                        )
                    );

                    $messageClick = self::getValueName($content, $payload);

                    if (empty($messageClick)) {
                        $reprocess = true;
                        throw new Exception('Please choose a value from dropdown!');
                    } else {
                        $message = self::sendAsUser($chat, $messageClick);
                        self::setLastMessageId($chat->id, $message->id);
                    }

                    $workflow->collected_data_array['collected'][$currentStep['content']['field']] = array(
                        'value' => $payload,
                        'value_literal' => $messageClick,
                        'name' => $currentStep['content']['name']
                    );

                } else {
                    throw new Exception('Validation function could not be found! Have you defined listener for ' . $currentStep['content']['provider_dropdown'] . ' identifier');
                }

            } else if ($currentStep['type'] == 'buttons') {

                $reprocess = true;

                $handler = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.genericbot_handler', array(
                    'render' => $currentStep['content']['render_validate'],
                    'render_args' => $currentStep['content']['render_args'],
                    'chat' => & $chat,
                    'workflow' => & $workflow,
                    'payload' => & $payload,
                ));

                if ($handler !== false && isset($handler['render']) && is_callable($handler['render']))
                {
                    $dataProcess = call_user_func_array($handler['render'], $handler['render_args']);

                    $message = self::sendAsUser($chat, $dataProcess['chosen_value_literal']);
                    self::setLastMessageId($chat->id, $message->id);

                    $workflow->collected_data_array['collected'][$currentStep['content']['field']] = array(
                        'value' => $dataProcess['chosen_value'],
                        'value_literal' => $dataProcess['chosen_value_literal'],
                        'name' => $currentStep['content']['name']
                    );

                    if (isset($dataProcess['go_to_step'])) {
                        $workflow->collected_data_array['step'] = $currentStepId = $dataProcess['go_to_step'];
                    }

                } else {
                    throw new Exception('Validation function could not be found! Have you defined listener for ' . $currentStep['content']['render_validate'] . ' identifier');
                }

            } else if ($currentStep['type'] == 'custom') {
                $reprocess = true;

                $handler = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.genericbot_handler', array(
                    'render' => $currentStep['content']['render_validate'],
                    'render_args' => $currentStep['content']['render_args'],
                    'chat' => & $chat,
                    'workflow' => & $workflow,
                    'payload' => & $payload,
                ));

                if ($handler !== false && isset($handler['render']) && is_callable($handler['render']))
                {
                    $dataProcess = call_user_func_array($handler['render'], $handler['render_args']);

                    if ($dataProcess['valid'] == false) {
                        if (isset($dataProcess['message']) && !empty($dataProcess['message'])) {
                            throw new Exception($dataProcess['message']);
                        } else {
                            throw new Exception('Your message does not match required format!');
                        }
                    }

                    $message = self::sendAsUser($chat, $dataProcess['chosen_value_literal']);
                    self::setLastMessageId($chat->id, $message->id);

                    $workflow->collected_data_array['collected'][$currentStep['content']['field']] = array(
                        'value' => $dataProcess['chosen_value'],
                        'value_literal' => $dataProcess['chosen_value_literal'],
                        'name' => $currentStep['content']['name']
                    );

                    if (isset($dataProcess['go_to_step'])) {
                        $workflow->collected_data_array['step'] = $currentStepId = $dataProcess['go_to_step'];
                    }

                } else {
                    throw new Exception('Validation function could not be found!');
                }
            }

            if ($workflow->status == erLhcoreClassModelGenericBotChatWorkflow::STATUS_PENDING_CONFIRM) {
                $reprocess = true;

                if ($payload == 'confirm') {

                    // Send message as user confirmed it
                    $message = self::sendAsUser($chat, 'Confirm');
                    self::setLastMessageId($chat->id, $message->id);

                    if (isset($workflow->collected_data_array['collectable_options']['collection_callback']) && $workflow->collected_data_array['collectable_options']['collection_callback'] !== '') {

                        $handler = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.genericbot_handler', array(
                            'render' => $workflow->collected_data_array['collectable_options']['collection_callback'],
                            'render_args' => (isset($workflow->collected_data_array['collectable_options']['collection_argument']) ? $workflow->collected_data_array['collectable_options']['collection_argument'] : null),
                            'chat' => & $chat,
                            'workflow' => & $workflow,
                            'payload' => & $payload,
                        ));

                        if ($handler !== false && isset($handler['render']) && is_callable($handler['render']))
                        {
                            $dataProcess = call_user_func_array($handler['render'], $handler['render_args']);

                            if (isset($dataProcess['chosen_value_literal']) && !empty($dataProcess['chosen_value_literal'])) {
                                $message = self::sendAsBot($chat, $dataProcess['chosen_value_literal']);
                            }

                            $workflow->collected_data_array['collected_confirm'] = array(
                                'info' => $dataProcess['info'],
                            );
                        }
                    }

                    $workflow->status = erLhcoreClassModelGenericBotChatWorkflow::STATUS_COMPLETED;
                    $workflow->collected_data_array['current_step'] = array();

                } else {
                    throw new Exception('Information was unconfirmed!');
                }
            }


            // There is more steps to proceed
            if (count($workflow->collected_data_array['steps']) >= $currentStepId+1 && isset($workflow->collected_data_array['steps'][$currentStepId+1])) {
                $workflow->collected_data_array['current_step'] = $workflow->collected_data_array['steps'][$currentStepId+1];
                $workflow->collected_data_array['step'] = $currentStepId+1;
                erLhcoreClassGenericBotActionCollectable::processStep($chat, $workflow->collected_data_array['current_step']);
            } else {

                // Collected information should be confirmed by user
                if ($workflow->status == erLhcoreClassModelGenericBotChatWorkflow::STATUS_STARTED && isset($workflow->collected_data_array['collectable_options']['show_summary']) && $workflow->collected_data_array['collectable_options']['show_summary'] == true) {

                    $workflow->status = erLhcoreClassModelGenericBotChatWorkflow::STATUS_PENDING_CONFIRM;

                    if (isset($workflow->collected_data_array['collectable_options']['show_summary_callback']) &&
                        $workflow->collected_data_array['collectable_options']['show_summary_callback'] !== ''
                    ) {
                        $handler = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.genericbot_handler', array(
                            'render' => $workflow->collected_data_array['collectable_options']['show_summary_callback'],
                            'render_args' => array(),
                            'chat' => & $chat,
                            'workflow' => & $workflow,
                            'payload' => & $payload,
                        ));

                        if ($handler !== false && isset($handler['render']) && is_callable($handler['render'])) {
                            $stepData = call_user_func_array($handler['render'], $handler['render_args']);
                        } else {
                            $stepData = erLhcoreClassGenericBotActionCollectable::sendSummary($chat, $workflow);
                        }

                    } else {
                       $stepData = erLhcoreClassGenericBotActionCollectable::sendSummary($chat, $workflow);
                    }

                    if (isset($workflow->collected_data_array['collectable_options']['show_summary_confirm_name']) && !empty($workflow->collected_data_array['collectable_options']['show_summary_confirm_name'])) {
                        $stepData['content']['collectable_options']['show_summary_confirm_name'] = $workflow->collected_data_array['collectable_options']['show_summary_confirm_name'];
                    }

                    if (isset($workflow->collected_data_array['collectable_options']['show_summary_checkbox_name']) && !empty($workflow->collected_data_array['collectable_options']['show_summary_checkbox_name'])) {
                        $stepData['content']['collectable_options']['show_summary_checkbox_name'] = $workflow->collected_data_array['collectable_options']['show_summary_checkbox_name'];
                    }

                    if (isset($workflow->collected_data_array['collectable_options']['show_summary_checkbox']) && $workflow->collected_data_array['collectable_options']['show_summary_checkbox'] == true) {
                        $stepData['content']['collectable_options']['show_summary_checkbox'] = $workflow->collected_data_array['collectable_options']['show_summary_checkbox'];
                    }

                    $workflow->identifier = $workflow->collected_data_array['collectable_options']['identifier_collection'];
                    $workflow->collected_data_array['current_step'] = $stepData;
                    erLhcoreClassGenericBotActionCollectable::processStep($chat, $workflow->collected_data_array['current_step']);

                } elseif ($workflow->status == erLhcoreClassModelGenericBotChatWorkflow::STATUS_COMPLETED || $workflow->status == erLhcoreClassModelGenericBotChatWorkflow::STATUS_STARTED) {

                    // Finish workflow if no more steps were found and no STATUS_PENDING_CONFIRM is pending
                    if ($workflow->status == erLhcoreClassModelGenericBotChatWorkflow::STATUS_STARTED) {

                        if (isset($workflow->collected_data_array['collectable_options']['collection_callback']) && $workflow->collected_data_array['collectable_options']['collection_callback'] !== '') {

                            $handler = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.genericbot_handler', array(
                                'render' => $workflow->collected_data_array['collectable_options']['collection_callback'],
                                'render_args' => (isset($workflow->collected_data_array['collectable_options']['collection_argument']) ? $workflow->collected_data_array['collectable_options']['collection_argument'] : null),
                                'chat' => & $chat,
                                'workflow' => & $workflow,
                                'payload' => & $payload,
                            ));

                            if ($handler !== false && isset($handler['render']) && is_callable($handler['render']))
                            {
                                $dataProcess = call_user_func_array($handler['render'], $handler['render_args']);

                                if (isset($dataProcess['chosen_value_literal']) && !empty($dataProcess['chosen_value_literal'])) {
                                    $message = self::sendAsBot($chat, $dataProcess['chosen_value_literal']);
                                }

                                $workflow->collected_data_array['collected_confirm'] = array(
                                    'info' => $dataProcess['info'],
                                );
                            }
                        }

                        $workflow->status = erLhcoreClassModelGenericBotChatWorkflow::STATUS_COMPLETED;
                        $workflow->collected_data_array['current_step'] = array();
                    }

                    if (isset($workflow->collected_data_array['collectable_options']['confirmation_message']) && $workflow->collected_data_array['collectable_options']['confirmation_message'] != '') {
                        if (isset($workflow->collected_data_array['collected_confirm']['info']) && !empty($workflow->collected_data_array['collected_confirm']['info'])) {

                            $replaceArray = array();
                            foreach ($workflow->collected_data_array['collected_confirm']['info'] as $key => $value) {
                                $replaceArray['{'.$key.'}'] = $value;
                            }

                            self::sendAsBot($chat, str_replace(array_keys($replaceArray),$replaceArray,$workflow->collected_data_array['collectable_options']['confirmation_message']));
                        } else {
                            self::sendAsBot($chat, $workflow->collected_data_array['collectable_options']['confirmation_message']);
                        }
                    }

                    if (isset($workflow->collected_data_array['collectable_options']['collection_callback_pattern']) && $workflow->collected_data_array['collectable_options']['collection_callback_pattern'] != '') {

                        $event = self::findEvent($workflow->collected_data_array['collectable_options']['collection_callback_pattern'], $chat->chat_variables_array['gbot_id'], 1);

                        if ($event instanceof erLhcoreClassModelGenericBotTriggerEvent) {
                            $message = self::processTrigger($chat, $event->trigger);
                        }

                        if (isset($message) && $message instanceof erLhcoreClassModelmsg) {
                            self::setLastMessageId($chat->id, $message->id);
                        } else {
                            if (erConfigClassLhConfig::getInstance()->getSetting( 'site', 'debug_output' ) == true) {
                                self::sendAsBot($chat,erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Button action could not be found!'));
                            }
                        }
                    }

                }
            }

            $workflow->collected_data = json_encode($workflow->collected_data_array);
            $workflow->saveThis();

        } catch (Exception $e) {
            self::sendAsBot($chat, $e->getMessage());

            if ($reprocess) {
                erLhcoreClassGenericBotActionCollectable::processStep($chat, $workflow->collected_data_array['current_step']);
            }
        }
    }

    public static function processTrigger($chat, $trigger, $setLastMessageId = false)
    {
        $message = null;
        foreach ($trigger->actions_front as $action) {
            $messageNew = call_user_func_array("erLhcoreClassGenericBotAction" . ucfirst($action['type']).'::process',array($chat, $action, $trigger));
            if ($messageNew instanceof erLhcoreClassModelmsg) {
                $message = $messageNew;
            }
        }

        if ($setLastMessageId == true && isset($message) && $message instanceof erLhcoreClassModelmsg) {
            self::setLastMessageId($chat->id, $message->id);
        }

        return $message;
    }

    public static function getClickName($metaData, $payload, $returnAll = false)
    {
        if (isset($metaData['content']['quick_replies'])) {
            foreach ($metaData['content']['quick_replies'] as $reply) {
                if ($reply['content']['payload'] == $payload) {
                    return $returnAll == false ? $reply['content']['name'] : $reply['content'];
                }
            }
        } elseif (isset($metaData['content']['buttons_generic'])) {
            foreach ($metaData['content']['buttons_generic'] as $reply) {
                if ($reply['content']['payload'] == $payload) {
                    return $returnAll == false ? $reply['content']['name'] : $reply['content'];
                }
            }
        }
    }

    public static function processTriggerClick($chat, $messageContext, $payload, $params = array()) {
        if (isset($chat->chat_variables_array['gbot_id'])) {

            // Try to find current workflow first
            $workflow = erLhcoreClassModelGenericBotChatWorkflow::findOne(array('filterin' => array('status' => array(0,1)), 'filter' => array('chat_id' => $chat->id)));
            if ($workflow instanceof erLhcoreClassModelGenericBotChatWorkflow) {
                self::processWorkflow($workflow, $chat, array('payload' => $payload));
                return;
            }

            $handler = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.genericbot_get_trigger_click', array(
                'chat' => & $chat,
                'msg' => $messageContext,
                'payload' => $payload,
            ));

            if ($handler !== false) {
                $trigger = $handler['trigger'];
            } else {
                $trigger = erLhcoreClassModelGenericBotTrigger::fetch($payload);

                if (!($trigger instanceof erLhcoreClassModelGenericBotTrigger)){
                    self::sendAsBot($chat,erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Trigger could not be found!'));
                    return;
                }
            }

            $messageClick = self::getClickName($messageContext->meta_msg_array, $payload);

            if (!empty($messageClick)) {
                if ((isset($params['processed']) && $params['processed'] == true) || !isset($params['processed'])){
                    $messageContext->meta_msg_array['processed'] = true;
                }
                $messageContext->meta_msg = json_encode($messageContext->meta_msg_array);
                $messageContext->saveThis();
                self::sendAsUser($chat, $messageClick);
            }

            $message = self::processTrigger($chat, $trigger);

            if (isset($message) && $message instanceof erLhcoreClassModelmsg) {
                self::setLastMessageId($chat->id, $message->id);
            } else {
                if (erConfigClassLhConfig::getInstance()->getSetting( 'site', 'debug_output' ) == true) {
                    self::sendAsBot($chat,erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Button action could not be found!'));
                }
            }
        }
    }

    public static function processButtonClick($chat, $messageContext, $payload, $params = array()) {

        if (isset($chat->chat_variables_array['gbot_id'])) {

            // Try to find current workflow first
            $workflow = erLhcoreClassModelGenericBotChatWorkflow::findOne(array('filterin' => array('status' => array(0,1)), 'filter' => array('chat_id' => $chat->id)));
            if ($workflow instanceof erLhcoreClassModelGenericBotChatWorkflow) {
                self::processWorkflow($workflow, $chat, array('payload' => $payload));
                return;
            }

            $handler = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.genericbot_get_click', array(
                    'chat' => & $chat,
                    'msg' => $messageContext,
                    'payload' => $payload,
            ));

            if ($handler !== false) {
                $event = $handler['event'];
            } else {
                $event = self::findEvent($payload, $chat->chat_variables_array['gbot_id'], 1);
            }

            $messageClick = self::getClickName($messageContext->meta_msg_array, $payload);

            if (!empty($messageClick)) {
                if ((isset($params['processed']) && $params['processed'] == true) || !isset($params['processed'])){
                    $messageContext->meta_msg_array['processed'] = true;
                }
                $messageContext->meta_msg = json_encode($messageContext->meta_msg_array);
                $messageContext->saveThis();
                self::sendAsUser($chat, $messageClick);
            }

            if ($event instanceof erLhcoreClassModelGenericBotTriggerEvent) {
                $message = self::processTrigger($chat, $event->trigger);
            }

            if (isset($message) && $message instanceof erLhcoreClassModelmsg) {
                self::setLastMessageId($chat->id, $message->id);
            } else {
                if (erConfigClassLhConfig::getInstance()->getSetting( 'site', 'debug_output' ) == true) {
                    self::sendAsBot($chat,erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Button action could not be found!'));
                }
            }
        }
    }

    public static function processValueClick($chat, $messageContext, $payload, $params = array())
    {

        // Try to find current workflow first
        $workflow = erLhcoreClassModelGenericBotChatWorkflow::findOne(array('filterin' => array('status' => array(0,1)), 'filter' => array('chat_id' => $chat->id)));
        if ($workflow instanceof erLhcoreClassModelGenericBotChatWorkflow) {
            self::processWorkflow($workflow, $chat, array('payload' => $payload));
            return;
        }

        $messageClick = self::getValueName($messageContext->meta_msg_array, $payload);

        if (!empty($messageClick)) {
            if ((isset($params['processed']) && $params['processed'] == true) || !isset($params['processed'])){
                $messageContext->meta_msg_array['processed'] = true;
            }
            $messageContext->meta_msg = json_encode($messageContext->meta_msg_array);
            $messageContext->saveThis();
            self::sendAsUser($chat, $messageClick);
        }
    }

    public static function getValueName($metaData, $payload)
    {
        if (isset($metaData['content']['dropdown'])) {
            $options = call_user_func_array($metaData['content']['dropdown']['provider_dropdown'], array());
            foreach ($options as $option) {
                if ($option->{$metaData['content']['dropdown']['provider_id']} == $payload) {
                    return $option->{$metaData['content']['dropdown']['provider_name']};
                }
            }
        }
    }
    /**
     * @desc generic update actions
     *
     * @param $chat
     * @param $messageContext
     * @param $payload
     */
    public static function processUpdateClick($chat, $messageContext, $payload)
    {
        if (isset($chat->chat_variables_array['gbot_id'])) {

            if (is_callable('erLhcoreClassGenericBotUpdateActions::' . $payload . 'Action')){

                $messageClick = self::getClickName($messageContext->meta_msg_array, $payload, true);

                if (!empty($messageClick)) {
                    self::sendAsUser($chat, $messageClick['name']);
                }

                $message = call_user_func_array("erLhcoreClassGenericBotUpdateActions::" . $payload . 'Action', array($chat, $messageClick));

                $messageContext->meta_msg_array['processed'] = true;
                $messageContext->meta_msg = json_encode($messageContext->meta_msg_array);
                $messageContext->saveThis();

                if (isset($message) && $message instanceof erLhcoreClassModelmsg) {
                    self::setLastMessageId($chat->id, $message->id);
                }

            } else {
                self::sendAsBot($chat,erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Update actions could not be found!'));
            }
        }
    }

    public static function sendAsBot($chat, $message)
    {
        $msg = new erLhcoreClassModelmsg();
        $msg->msg = $message;
        $msg->chat_id = $chat->id;
        $msg->name_support = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Live Support');
        $msg->user_id = -2;
        $msg->time = time() + 5;
        erLhcoreClassChat::getSession()->save($msg);

        self::setLastMessageId($chat->id, $msg->id);
    }

    public static function sendAsUser($chat, $messageText) {
        $msg = new erLhcoreClassModelmsg();
        $msg->msg = trim($messageText);
        $msg->chat_id = $chat->id;
        $msg->user_id = 0;
        $msg->time = time();

        if ($chat->chat_locale != '' && $chat->chat_locale_to != '') {
            erLhcoreClassTranslate::translateChatMsgVisitor($chat, $msg);
        }

        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.before_msg_user_saved',array('msg' => & $msg, 'chat' => & $chat));

        erLhcoreClassChat::getSession()->save($msg);

        return $msg;
    }

    public static function setLastMessageId($chatId, $messageId) {

        $db = ezcDbInstance::get();

        $stmt = $db->prepare('UPDATE lh_chat SET last_user_msg_time = :last_user_msg_time, lsync = :lsync, last_msg_id = :last_msg_id, has_unread_messages = 1, unanswered_chat = :unanswered_chat WHERE id = :id');
        $stmt->bindValue(':id', $chatId, PDO::PARAM_INT);
        $stmt->bindValue(':lsync', time(), PDO::PARAM_INT);
        $stmt->bindValue(':last_user_msg_time', time(), PDO::PARAM_INT);
        $stmt->bindValue(':unanswered_chat', 0, PDO::PARAM_INT);
        $stmt->bindValue(':last_msg_id',$messageId,PDO::PARAM_INT);
        $stmt->execute();
    }
}

?>