<?php

class erLhcoreClassGenericBotActionProgress {

    public static function process($chat, $action, $trigger, $params = array())
    {
        // Do not process if chat status changed

        $msg = new erLhcoreClassModelmsg();

        $metaMessage = array();

        if (isset($action['content']['method']) && !empty($action['content']['interval']) && $action['content']['interval'] > 0)
        {
            $handler = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.genericbot_handler', array(
                'render' => $action['content']['method'],
                'render_args' => $params,
                'chat' => & $chat,
                'trigger' => $trigger
            ));

            // We have valid handler, so we have and function also
            if ($handler !== false && isset($handler['render']) && is_callable($handler['render']))
            {
                $action['content']['args'] = $handler['render_args'];
                $action['content']['method'] = $handler['render'];

                if (isset($action['content']['argument_template']['args'])) {
                    $metaMessage['content']['payload_data'] = $action['content']['argument_template']['args'];
                    unset($action['content']['argument_template']);
                }

                $metaMessage['content']['progress'] = $action['content'];

                $msg->msg = "";
                $msg->meta_msg = !empty($metaMessage) ? json_encode($metaMessage) : '';
                $msg->chat_id = $chat->id;
                $msg->name_support = erLhcoreClassGenericBotWorkflow::getDefaultNick($chat);
                $msg->user_id = -2;
                $msg->time = time() + 5;

                if (!isset($params['do_not_save']) || $params['do_not_save'] == false) {
                    erLhcoreClassChat::getSession()->save($msg);
                }
            }
        }

        return $msg;
    }
}

?>