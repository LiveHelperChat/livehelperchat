<?php

class erLhcoreClassGenericBotActionText_conditional {

    public static function process($chat, $action, $trigger, $params)
    {

        $msg = new erLhcoreClassModelmsg();

        $metaMessage = array();

        foreach (['intro_us','full_us','readmore_us','intro_op','full_op','readmore_op'] as $attr) {
            if (isset($action['content'][$attr])) {
                $action['content'][$attr] = erLhcoreClassGenericBotWorkflow::translateMessage($action['content'][$attr], array('chat' => $chat));
            } else {
                $action['content'][$attr] = null;
            }
        }

        if (isset($params['error_code'])) {
            $bot = erLhcoreClassModelGenericBotBot::fetch($trigger->bot_id);
            if ($bot instanceof erLhcoreClassModelGenericBotBot) {
                $configurationArray = $bot->configuration_array;
                if (isset($configurationArray['exc_group_id']) && !empty($configurationArray['exc_group_id'])){
                    $exceptionMessage = erLhcoreClassModelGenericBotExceptionMessage::findOne(array('limit' => 1, 'sort' => 'priority ASC', 'filter' => array('active' => 1,'code' => $params['error_code']), 'filterin' => array('exception_group_id' => $configurationArray['exc_group_id'])));
                    if ($exceptionMessage instanceof erLhcoreClassModelGenericBotExceptionMessage && $exceptionMessage->message != '') {
                        $params['replace_array']['{error}'] = erLhcoreClassGenericBotWorkflow::translateMessage($exceptionMessage->message, array('chat' => $chat, 'args' => $params));
                    }
                }
            }
        }

        if (isset($params['replace_array'])) {
            foreach (['intro_us','full_us','readmore_us','intro_op','full_op','readmore_op'] as $attr) {
                $action['content'][$attr] = str_replace(array_keys($params['replace_array']), array_values($params['replace_array']), $action['content'][$attr]);
            }

            // We need to translate again because some error messages can have translatable strings themself
            foreach (['intro_us','full_us','readmore_us','intro_op','full_op','readmore_op'] as $attr) {
                $action['content'][$attr] = erLhcoreClassGenericBotWorkflow::translateMessage($attr, array('chat' => $chat, 'args' => $params));
            }
        }

        $metaMessage['content']['text_conditional'] = [
            'intro_us' => $action['content']['intro_us'],
            'full_us' => $action['content']['full_us'],
            'readmore_us' => $action['content']['readmore_us'],
            'intro_op' => $action['content']['intro_op'],
            'full_op' => $action['content']['full_op'],
            'readmore_op' => $action['content']['readmore_op'],
        ];

        $msg->meta_msg = !empty($metaMessage) ? json_encode($metaMessage) : (isset($params['meta_msg']) && !empty($params['meta_msg']) ? json_encode($params['meta_msg']) : '');
        $msg->chat_id = $chat->id;
        if (isset($params['override_nick']) && !empty($params['override_nick'])) {
            $msg->name_support = (string)$params['override_nick'];
        } else {
            $msg->name_support = erLhcoreClassGenericBotWorkflow::getDefaultNick($chat);
        }
        $msg->user_id = isset($params['override_user_id']) && $params['override_user_id'] > 0 ? (int)$params['override_user_id'] : -2;
        $msg->time = time();

        if (!isset($params['do_not_save']) || $params['do_not_save'] == false) {
            erLhcoreClassChat::getSession()->save($msg);
        }

        return $msg;
    }
}

?>