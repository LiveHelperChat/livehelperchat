<?php

erLhcoreClassRestAPIHandler::setHeaders();

session_write_close();

$chat = erLhcoreClassModelChat::fetch($Params['user_parameters']['chat_id']);

$validStatuses = array(
    erLhcoreClassModelChat::STATUS_PENDING_CHAT,
    erLhcoreClassModelChat::STATUS_ACTIVE_CHAT,
    erLhcoreClassModelChat::STATUS_BOT_CHAT,
);

if (isset($_GET['id']) && isset($_GET['payload'])) {
    $paramsPayload = array('id' => (isset($_GET['id']) ? $_GET['id'] : null), 'payload' => $_GET['payload'], 'processed' => (isset($_GET['processed']) && $_GET['processed'] == 'true'));
} else {
    $paramsPayload = json_decode(file_get_contents('php://input'),true);
}

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.validstatus_chat',array('chat' => & $chat, 'valid_statuses' => & $validStatuses));

try {
    if ($chat->hash == $Params['user_parameters']['hash'] && (in_array($chat->status,$validStatuses)) && !in_array($chat->status_sub, array(erLhcoreClassModelChat::STATUS_SUB_SURVEY_SHOW,erLhcoreClassModelChat::STATUS_SUB_CONTACT_FORM))) {


        if ($Params['user_parameters_unordered']['type'] != 'manualtrigger') {
            if (!isset($paramsPayload['id']) || !is_numeric($paramsPayload['id'])) {
                throw new Exception('Message not provided!');
            }

            $message = erLhcoreClassModelmsg::fetch($paramsPayload['id']);

            if (!($message instanceof erLhcoreClassModelmsg)) {
                throw new Exception('Message could not be found!');
            }

            if ($message->chat_id != $chat->id) {
                throw new Exception('Invalid message provided');
            }
        }

        if (!isset($paramsPayload['payload']) || $paramsPayload['payload'] == '') {
            throw new Exception('Payload not provided');
        }

        $updateMessage = false;

        if ($Params['user_parameters_unordered']['type'] == 'valueclicked') {
            erLhcoreClassGenericBotWorkflow::processValueClick($chat, $message, $paramsPayload['payload'], array('processed' => (isset($paramsPayload['processed']) && $paramsPayload['processed'] == true)));
        } elseif ($Params['user_parameters_unordered']['type'] == 'manualtrigger') {
            erLhcoreClassGenericBotWorkflow::processManualTrigger($chat, $paramsPayload['payload']);
        } elseif ($Params['user_parameters_unordered']['type'] == 'triggerclicked') {
            erLhcoreClassGenericBotWorkflow::processTriggerClick($chat, $message, $paramsPayload['payload'], array('processed' => (isset($paramsPayload['processed']) && $paramsPayload['processed'] == true)));
        } elseif ($Params['user_parameters_unordered']['type'] == 'editgenericstep') {
            erLhcoreClassGenericBotWorkflow::processStepEdit($chat, $message, $paramsPayload['payload'], array('processed' => (isset($paramsPayload['processed']) && $paramsPayload['processed'] == true)));
        } elseif ($Params['user_parameters_unordered']['type'] == 'reactions') {

            $metaMessage = $message->meta_msg_array;

            if (isset($metaMessage['content']['reactions']['content'])) {

                $currentPart = isset($metaMessage['content']['reactions']['current'][$paramsPayload['payload-id']]) ? $metaMessage['content']['reactions']['current'][$paramsPayload['payload-id']] : null;

                $action = 'remove';
                $identifier = '';
                $valueAction = '';

                // Same reaction icon was clicked unselect if it was selected
                if ($currentPart === $paramsPayload['payload']) {
                    unset($metaMessage['content']['reactions']['current'][$paramsPayload['payload-id']]);
                    if (empty($metaMessage['content']['reactions']['current'])) {
                        unset($metaMessage['content']['reactions']['current']);
                    }
                    $identifier = $paramsPayload['payload-id'];
                } else { // New reaction was selected
                    $validIdentifiers = [];
                    $parts = explode("\n",trim($metaMessage['content']['reactions']['content']));
                    foreach ($parts as $part) {
                        $iconParams = explode("|",$part);
                        $validIdentifiers[$iconParams[2]][] = $iconParams[1];
                    }
                    if (key_exists($paramsPayload['payload-id'],$validIdentifiers) && in_array($paramsPayload['payload'],$validIdentifiers[$paramsPayload['payload-id']])) {
                        $metaMessage['content']['reactions']['current'][$paramsPayload['payload-id']] = (string)$paramsPayload['payload'];
                        $action = 'add';
                        $valueAction = (string)$paramsPayload['payload'];
                    }
                }

                $message->meta_msg_array = $metaMessage;
                $message->meta_msg = json_encode($message->meta_msg_array);
                $message->updateThis(['update' => ['meta_msg']]);

                // Dispatch reaction action for extensions
                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.reaction_visitor', array(
                    'reaction_identifier' => $identifier,
                    'reaction_value' => $valueAction,
                    'action' => $action,
                    'msg' => & $message,
                    'chat' => & $chat
                ));

                $chat->operation_admin = "lhinst.updateMessageRowAdmin({$chat->id},{$message->id});\n";
                $chat->updateThis(['update' => ['operation_admin']]);

                $updateMessage = true;
            } elseif (isset($Params['user_parameters_unordered']['theme'])) { // Reaction by theme to add support

                $themeId = erLhcoreClassChat::extractTheme($Params['user_parameters_unordered']['theme']);
                if ($themeId !== false) {
                    $theme = erLhAbstractModelWidgetTheme::fetch($themeId);
                    if ($theme instanceof erLhAbstractModelWidgetTheme) {

                        $validIdentifiers = [];
                        $action = 'remove';
                        $identifier = '';
                        $valueAction = '';
                        $oneReactionPerMessage = isset($theme->bot_configuration_array['one_reaction_per_msg']) && $theme->bot_configuration_array['one_reaction_per_msg'] == true;


                        if (isset($theme->bot_configuration_array['enable_react_for_vi']) && $theme->bot_configuration_array['enable_react_for_vi'] == true) {
                            $updateMessage = true;
                            if (isset($theme->bot_configuration_array['custom_tb_reactions'])) {
                                $partsReaction = explode("=",$theme->bot_configuration_array['custom_tb_reactions']);
                                foreach ($partsReaction as $reaction) {
                                    $iconParams = explode("|",$reaction);
                                    if (!isset($iconParams[2]) || !isset($iconParams[1])) {
                                        $iconParams[2] = strtoupper(preg_replace("/^[0]+/","",bin2hex(mb_convert_encoding($iconParams[0], 'UTF-32', 'UTF-8'))));
                                        $iconParams[1] = 1;
                                    }
                                    $validIdentifiers[$iconParams[2]][] = $iconParams[1];
                                }
                            }
                        }

                        if (isset($theme->bot_configuration_array['custom_mw_reactions'])) {
                            $partsReaction = explode("=",$theme->bot_configuration_array['custom_mw_reactions']);
                            foreach ($partsReaction as $reaction) {
                                $iconParams = explode("|",$reaction);
                                if (!isset($iconParams[2]) || !isset($iconParams[1])) {
                                    $iconParams[2] = strtoupper(preg_replace("/^[0]+/","",bin2hex(mb_convert_encoding($iconParams[0], 'UTF-32', 'UTF-8'))));
                                    $iconParams[1] = 1;
                                }
                                $validIdentifiers[$iconParams[2]][] = $iconParams[1];
                            }
                        }

                        if (isset($validIdentifiers[$paramsPayload['payload-id']])) {
                            $currentPart = isset($metaMessage['content']['reactions']['current'][$paramsPayload['payload-id']]) ? $metaMessage['content']['reactions']['current'][$paramsPayload['payload-id']] : null;
                            if ($currentPart === $paramsPayload['payload']) {
                                unset($metaMessage['content']['reactions']['current'][$paramsPayload['payload-id']]);
                                if (empty($metaMessage['content']['reactions']['current'])) {
                                    unset($metaMessage['content']['reactions']['current']);
                                }
                                $identifier = $paramsPayload['payload-id'];
                                if ($oneReactionPerMessage) {
                                    unset($metaMessage['content']['reactions']['current']);
                                }
                            } else {
                                if ($oneReactionPerMessage) {
                                    unset($metaMessage['content']['reactions']['current']);
                                }
                                if (key_exists($paramsPayload['payload-id'],$validIdentifiers) && in_array($paramsPayload['payload'],$validIdentifiers[$paramsPayload['payload-id']])) {
                                    $metaMessage['content']['reactions']['current'][$paramsPayload['payload-id']] = (string)$paramsPayload['payload'];
                                    $action = 'add';
                                    $valueAction = (string)$paramsPayload['payload'];
                                }
                            }
                        }

                        // Dispatch reaction action for extensions
                        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.reaction_visitor', array(
                            'reaction_identifier' => $identifier,
                            'reaction_value' => $valueAction,
                            'action' => $action,
                            'msg' => & $message,
                            'chat' => & $chat
                        ));

                        $message->meta_msg_array = $metaMessage;
                        $message->meta_msg = json_encode($message->meta_msg_array);
                        $message->updateThis(['update' => ['meta_msg']]);


                        $chat->operation_admin = "lhinst.updateMessageRowAdmin({$chat->id},{$message->id});\n";
                        $chat->updateThis(['update' => ['operation_admin']]);

                        $updateMessage = true;
                    }
                }
                // <?php $reactionsOutput = ''; if (isset($theme->bot_configuration_array['custom_tb_reactions'])) :
                // @todo continbue herer validate request to add message
            }

        } else {
            erLhcoreClassGenericBotWorkflow::processButtonClick($chat, $message, $paramsPayload['payload'], array('processed' => (isset($paramsPayload['processed']) && $paramsPayload['processed'] == true)));
        }

        // On button click also update counter
        if ($chat->status == erLhcoreClassModelChat::STATUS_BOT_CHAT && $chat->bot instanceof erLhcoreClassModelGenericBotBot) {
            $ignoreButtonClick = isset($chat->bot->configuration_array['ign_btn_clk']) && $chat->bot->configuration_array['ign_btn_clk'] == true;
            if ($ignoreButtonClick === false) {
                $chatVariables = $chat->chat_variables_array;
                if (!isset($chatVariables['msg_v'])) {
                    $chatVariables['msg_v'] = 1;
                } else {
                    $chatVariables['msg_v']++;
                }
                $chat->chat_variables_array = $chatVariables;
                $chat->chat_variables = json_encode($chatVariables);
                $chat->updateThis(array('update' => array('chat_variables')));
            }
        }

        $message_id_first = 0;

        if (isset($message) && $message instanceof erLhcoreClassModelmsg) {
            $message_id_first = (int)erLhcoreClassModelmsg::getCount(['limit' => 1, 'sort' => 'id ASC', 'filtergt' => ['id' => $message->id], 'filter' => [/*'user_id' => 0,*/ 'chat_id' => $chat->id]],'count','id','id');
        }

        echo json_encode(array(
            'message_id_first' => $message_id_first,
            'update_message' => $updateMessage,
            'error' => false,
            't' => erLhcoreClassGenericBotWorkflow::$triggerName));

        // Try to finish request before any listers do their job
        flush();
        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }

    } else {
        throw new Exception('You do not have permission!');
    }

} catch (Exception $e) {
    echo json_encode(array('error' => true, 'message' => $e->getMessage()));
}

exit;

?>