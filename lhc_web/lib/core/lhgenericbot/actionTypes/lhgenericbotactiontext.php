<?php

class erLhcoreClassGenericBotActionText {

    public static function process($chat, $action)
    {
        $buttons = '';

        //print_r($action);

        if (isset($action['content']['quick_replies']) && !empty($action['content']['quick_replies']))
        {
            $buttons = '[listinline]';

            foreach ($action['content']['quick_replies'] as $quickReply) {
                $buttons .= '[li][button id='.$quickReply['content']['payload'].']' . $quickReply['content']['name'] . '[/button][/li]';
            }

            $buttons .= '[/listinline]';
        }

        $msg = new erLhcoreClassModelmsg();
        $msg->msg = trim($action['content']['text']) . $buttons;
        $msg->chat_id = $chat->id;
        $msg->name_support = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Live Support');
        $msg->user_id = -2;
        $msg->time = time() + 5;

        erLhcoreClassChat::getSession()->save($msg);
    }
}

?>