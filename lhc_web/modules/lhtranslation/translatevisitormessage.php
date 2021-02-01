<?php

header ( 'content-type: application/json; charset=utf-8' );

$chat = erLhcoreClassModelChat::fetch($Params['user_parameters']['chat_id']);

if (erLhcoreClassChat::hasAccessToRead($chat)) {
    try {

        $msg = erLhcoreClassModelmsg::fetch($Params['user_parameters']['msg_id']);
        $msg->msg = preg_replace('#\[translation\](.*?)\[/translation\]#is', '', $msg->msg);

        if ($msg->user_id > 0) {
            $translatedMessage = erLhcoreClassTranslate::translateTo($msg->msg, ($chat->chat_locale_to != '' ? $chat->chat_locale_to : substr(erLhcoreClassSystem::instance()->Language, 0, 2)), $chat->chat_locale);
        } else {
            $translatedMessage = erLhcoreClassTranslate::translateTo($msg->msg, $chat->chat_locale, $chat->chat_locale_to != '' ? $chat->chat_locale_to : substr(erLhcoreClassSystem::instance()->Language, 0, 2));
        }

        $msg->msg .= "[translation]{$translatedMessage}[/translation]";
        $msg->saveThis();

        if ($msg->user_id > 0) {
            $chat->operation = "lhinst.updateMessageRow({$msg->id});\n";
            $chat->updateThis(array('update' => array('operation')));
        }

        echo json_encode(array('error' => false));
    } catch (Exception $e){
        echo json_encode(array('error' => true, 'msg' => $e->getMessage()));
    }
}

exit;


?>