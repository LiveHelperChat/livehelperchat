<?php

erLhcoreClassRestAPIHandler::setHeaders('Content-Type: text/html; charset=UTF-8');

$message = erLhcoreClassModelmsg::fetch($Params['user_parameters']['msg_id']);

if ($message instanceof erLhcoreClassModelmsg &&
        ($chat = erLhcoreClassModelChat::fetch($message->chat_id)) &&
        erLhcoreClassChat::hasAccessToRead($chat) &&
        erLhcoreClassChat::hasAccessToWrite($chat) && (
            (isset($chat->chat_variables_array['theme_id']) && ($theme = erLhAbstractModelWidgetTheme::fetch($chat->chat_variables_array['theme_id']))) ||
            ($chat->theme_id > 0 && ($theme = erLhAbstractModelWidgetTheme::fetch($chat->theme_id)))
        )  &&
        $theme instanceof erLhAbstractModelWidgetTheme
) {
    $tpl = erLhcoreClassTemplate::getInstance( 'lhchat/reacttomessagesmodal.tpl.php');
    $tpl->set('theme', $theme);
    $tpl->set('messageId', $message->id);
    $tpl->set('message', $message);
    $tpl->set('admin_mode', true);
    echo $tpl->fetch();
}

exit;

?>