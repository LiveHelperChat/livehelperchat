<?php

class erLhcoreClassGenericBotActionIframe{

    public static function process($chat, $action, $trigger, $params)
    {
        $msg = new erLhcoreClassModelmsg();

        $metaMessage = array();

        if (isset($action['content']['body_html']) && !empty($action['content']['body_html']))
        {

            $metaMessage['content']['iframe'] = ['body_html' => erLhcoreClassGenericBotWorkflow::translateMessage($action['content']['body_html'], array('chat' => $chat, 'args' => $params))];

            if (isset($action['content']['payload_css']) && $action['content']['payload_css'] != '') {
                $metaMessage['content']['iframe']['payload_css'] = json_decode($action['content']['payload_css'],true);
            }

            if (isset($action['content']['payload_js']) && $action['content']['payload_js'] != '') {
                $metaMessage['content']['iframe']['payload_js'] = json_decode($action['content']['payload_js'],true);
            }

            if (isset($action['content']['body_form']) && $action['content']['body_form'] != '') {
                $metaMessage['content']['iframe']['body_form'] = (int)$action['content']['body_form'];
            }

            if (isset($action['content']['iframe_url']) && $action['content']['iframe_url'] != '') {
                $metaMessage['content']['iframe']['iframe_url'] = $action['content']['iframe_url'];
            }

            if (isset($action['content']['payload_js_source']) && $action['content']['payload_js_source'] != '') {
                $metaMessage['content']['iframe']['payload_js_source'] = erLhcoreClassGenericBotWorkflow::translateMessage($action['content']['payload_js_source'], array('chat' => $chat, 'args' => $params));
            }

            if (isset($action['content']['iframe_options']) && !empty($action['content']['iframe_options'])) {
                $metaMessage['content']['iframe']['iframe_options'] = $action['content']['iframe_options'];
            }

            $metaMessage['content']['iframe']['iframe_options']['iframe-identifier'] = 'trigger-' . $trigger->id;

            if (isset($action['content']['iframe_style']) && !empty($action['content']['iframe_style'])) {
                $metaMessage['content']['iframe']['style'] = $action['content']['iframe_style'];
            }

            $msg->msg = "";
            $msg->meta_msg = !empty($metaMessage) ? json_encode($metaMessage) : '';
            $msg->chat_id = $chat->id;
            if (isset($params['override_nick']) && !empty($params['override_nick'])) {
                $msg->name_support = (string)$params['override_nick'];
            } else {
                $msg->name_support = erLhcoreClassGenericBotWorkflow::getDefaultNick($chat);
            }
            $msg->user_id = isset($params['override_user_id']) && $params['override_user_id'] > 0 ? (int)$params['override_user_id'] : -2;
            $msg->time = time();

            if (erLhcoreClassGenericBotWorkflow::$setBotFlow === false) {
                $msg->time += 1;
            }

            if (!isset($params['do_not_save']) || $params['do_not_save'] == false) {
                erLhcoreClassChat::getSession()->save($msg);
            }
        }

        return $msg;
    }
}

?>