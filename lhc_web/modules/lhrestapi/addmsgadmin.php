<?php

try {
    erLhcoreClassRestAPIHandler::validateRequest();

    // erLhcoreClassLog::write(print_r($_POST,true));

    $definition = array(
        'chat_id' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::REQUIRED, 'int'
        ),
        'msg' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::REQUIRED, 'unsafe_raw'
        ),
        'meta_msg' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'operator_name' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'sender' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'string'
        )
    );

    $form = new ezcInputForm( INPUT_POST, $definition );

    if (trim($form->msg) != '')
    {
        $db = ezcDbInstance::get();

        try {
            $db->beginTransaction();

            $Chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $form->chat_id);

            if (!($Chat instanceof erLhcoreClassModelChat)) {
                throw new Exception('Chat could not be found!');
            }

            // Has access to read, chat
            //FIXME create permission to add message...
            if ( erLhcoreClassRestAPIHandler::hasAccessToRead($Chat) )
            {
                $userData = erLhcoreClassRestAPIHandler::getUser();

                if ($form->sender != 'bot') {
                    $messageUserId = $userData->id;
                } else {
                    $messageUserId = -2;
                }

                $msgText = trim($form->msg);
                $ignoreMessage = false;
                $returnBody = '';
                $customArgs = array();
                $msg = new erLhcoreClassModelmsg();

                if (strpos($msgText, '!') === 0) {
                    $statusCommand = erLhcoreClassChatCommand::processCommand(array('user' => $userData, 'msg' => $msgText, 'chat' => & $Chat));
                    if ($statusCommand['processed'] === true) {
                        $messageUserId = -1; // Message was processed set as internal message

                        $rawMessage = !isset($statusCommand['raw_message']) ? $msgText : $statusCommand['raw_message'];

                        $msgText = trim('[b]'.$userData->name_support.'[/b]: '.$rawMessage .' '. ($statusCommand['process_status'] != '' ? '|| '.$statusCommand['process_status'] : ''));

                        if (isset($statusCommand['ignore']) && $statusCommand['ignore'] == true) {
                            $ignoreMessage = true;
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
                    $msg->msg = $msgText;
                    $msg->chat_id = $Chat->id;
                    $msg->user_id = $messageUserId;
                    $msg->time = time();

                    if ($form->hasValidData('meta_msg') && $form->meta_msg != '') {
                        $metaParts = json_decode($form->meta_msg,true);
                        // Parse meta message as it was bot message and store it within message
                        // We cannot store directly meta message content because it may contain callbacks which can be internal functions
                        // It would be huge security flaw in automated hosting environment
                        if ($metaParts !== null) {
                            $trigger = new erLhcoreClassModelGenericBotTrigger();
                            $trigger->actions_front = $metaParts;
                            $trigger->actions = $form->meta_msg;

                            // Combine all meta messages to single one
                            $messages = erLhcoreClassGenericBotWorkflow::processTriggerPreview($Chat, $trigger, array('args' => array('do_not_save' => true)));

                            $metaData = array();
                            foreach ($messages as $metaMessage) {
                                $metaData = array_merge_recursive($metaData, $metaMessage->meta_msg_array);
                            }

                            $metaData = array_filter($metaData);

                            if (!empty($metaData)) {
                                $msg->meta_msg = json_encode($metaData);
                            }
                        }
                    }

                    if ($form->hasValidData('operator_name') && $form->operator_name != '') {
                        $msg->name_support = $form->operator_name;
                    } elseif ($form->sender == 'bot') {

                        $chatVariables = $Chat->chat_variables_array;

                        $nameSupport = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Live Support');

                        if ($Chat->gbot_id > 0) {
                            $bot = erLhcoreClassModelGenericBotBot::fetch($Chat->gbot_id);
                            if ($bot instanceof erLhcoreClassModelGenericBotBot && $bot->nick != '') {
                                $nameSupport = $bot->nick;
                            }
                        }

                        $msg->name_support = $nameSupport;

                    } else {
                        $msg->name_support = $userData->name_support;
                    }

                    if ($messageUserId != -1 && $Chat->chat_locale != '' && $Chat->chat_locale_to != '') {
                        erLhcoreClassTranslate::translateChatMsgOperator($Chat, $msg);
                    }

                    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.before_msg_admin_saved',array('msg' => & $msg,'chat' => & $Chat));

                    erLhcoreClassChat::getSession()->save($msg);

                    // Set last message ID
                    if ($Chat->last_msg_id < $msg->id) {

                        $statusSub = '';
                        if ($Chat->status_sub == erLhcoreClassModelChat::STATUS_SUB_ON_HOLD && $messageUserId !== -1) {
                            $statusSub = ',status_sub = 0, last_user_msg_time = ' . (time() - 1);
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
                        if ($Chat->status_sub != erLhcoreClassModelChat::STATUS_SUB_ON_HOLD && $Chat->auto_responder !== false) {
                            if ($Chat->auto_responder->active_send_status != 0) {
                                $Chat->auto_responder->active_send_status = 0;
                                $Chat->auto_responder->saveThis();
                            }
                        }

                        $stmt = $db->prepare('UPDATE lh_chat SET status = :status, user_status = :user_status, last_msg_id = :last_msg_id, last_op_msg_time = :last_op_msg_time, has_unread_op_messages = :has_unread_op_messages, unread_op_messages_informed = :unread_op_messages_informed' . $statusSub . ' WHERE id = :id');
                        $stmt->bindValue(':id',$Chat->id,PDO::PARAM_INT);
                        $stmt->bindValue(':last_msg_id',$msg->id,PDO::PARAM_INT);
                        $stmt->bindValue(':last_op_msg_time',time(),PDO::PARAM_INT);
                        $stmt->bindValue(':has_unread_op_messages',1,PDO::PARAM_INT);
                        $stmt->bindValue(':unread_op_messages_informed',0,PDO::PARAM_INT);

                        if ($userData->invisible_mode == 0 && $messageUserId > 0) { // Change status only if it's not internal command
                            if ($Chat->status == erLhcoreClassModelChat::STATUS_PENDING_CHAT) {
                                $Chat->status = erLhcoreClassModelChat::STATUS_ACTIVE_CHAT;
                                $Chat->user_id = $messageUserId;
                            }
                        }

                        // Chat can be reopened only if user did not ended chat explictly
                        if ($Chat->user_status == erLhcoreClassModelChat::USER_STATUS_CLOSED_CHAT && $Chat->status_sub != erLhcoreClassModelChat::STATUS_SUB_USER_CLOSED_CHAT) {
                            $Chat->user_status = erLhcoreClassModelChat::USER_STATUS_PENDING_REOPEN;
                            if ( ($onlineuser = $Chat->online_user) !== false) {
                                $onlineuser->reopen_chat = 1;
                                $onlineuser->saveThis();
                            }
                        }

                        $stmt->bindValue(':user_status',$Chat->user_status,PDO::PARAM_INT);
                        $stmt->bindValue(':status',$Chat->status,PDO::PARAM_INT);
                        $stmt->execute();
                    }

                    // If chat is in bot mode and operators writes a message, accept a chat as operator.
                    if ($form->sender == 'operator' && $Chat->status == erLhcoreClassModelChat::STATUS_BOT_CHAT && $messageUserId != -1) {

                        if ($userData->invisible_mode == 0 && erLhcoreClassRestAPIHandler::hasAccessToWrite($Chat)) {
                            $Chat->refreshThis();
                            $Chat->status = erLhcoreClassModelChat::STATUS_ACTIVE_CHAT;

                            $Chat->wait_time = time() - ($Chat->pnd_time > 0 ? $Chat->pnd_time : $Chat->time);
                            $Chat->user_id = $userData->id;

                            // User status in event of chat acceptance
                            $Chat->usaccept = $userData->hide_online;
                            $Chat->saveThis();

                            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.data_changed',array('chat' => & $Chat, 'user_data' => $userData));

                            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.accept',array('chat' => & $Chat, 'user_data' => $userData));
                            erLhcoreClassChat::updateActiveChats($Chat->user_id);

                            if ($Chat->department !== false) {
                                erLhcoreClassChat::updateDepartmentStats($Chat->department);
                            }

                            $options = $Chat->department->inform_options_array;
                            erLhcoreClassChatWorkflow::chatAcceptedWorkflow(array('department' => $Chat->department, 'options' => $options),$Chat);
                        }
                    }
                }

                if ($Chat->status == erLhcoreClassModelChat::STATUS_OPERATORS_CHAT) {

                    $transfer = erLhcoreClassModelTransfer::findOne(array('filter' => array('transfer_user_id' => $userData->id, 'transfer_to_user_id' => ($Chat->user_id == $userData->id ? $Chat->sender_user_id : $Chat->user_id))));

                    if ($transfer === false) {
                        $transfer = new erLhcoreClassModelTransfer();

                        $transfer->chat_id = $Chat->id;

                        $transfer->from_dep_id = $Chat->dep_id;

                        // User which is transfering
                        $transfer->transfer_user_id = $userData->id;

                        // To what user
                        $transfer->transfer_to_user_id = $Chat->user_id == $userData->id ? $Chat->sender_user_id : $Chat->user_id;
                        $transfer->saveThis();
                    }
                }

                echo erLhcoreClassChat::safe_json_encode(array('error' => false, 'r' => $returnBody, 'msg' => $msg->getState())+ $customArgs);

                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.web_add_msg_admin', array('msg' => & $msg,'chat' => & $Chat));

            } else {
                throw new Exception('You cannot read this chat!');
            }

            $db->commit();

        } catch (Exception $e) {
            http_response_code(400);
            echo erLhcoreClassRestAPIHandler::outputResponse(array(
                'error' => true,
                'r' => $e->getMessage()
            ));
            $db->rollback();
        }

    } else {
        http_response_code(400);
        echo erLhcoreClassRestAPIHandler::outputResponse(array(
            'error' => true,
            'r' => "Please enter a message!"
        ));
    }
} catch (Exception $e) {
    http_response_code(400);
    echo erLhcoreClassRestAPIHandler::outputResponse(array(
        'error' => true,
        'r' => $e->getMessage()
    ));
}

exit;

?>