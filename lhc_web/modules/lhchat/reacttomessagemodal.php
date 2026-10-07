<?php

erLhcoreClassRestAPIHandler::setHeaders('Content-Type: text/html; charset=UTF-8');

if (isset($Params['user_parameters_unordered']['theme']) && ($themeId = erLhcoreClassChat::extractTheme($Params['user_parameters_unordered']['theme'])) !== false) {
    $theme = erLhAbstractModelWidgetTheme::fetch($themeId);
    if ($theme instanceof erLhAbstractModelWidgetTheme && is_numeric($Params['user_parameters']['message_id']) && ($message = erLhcoreClassModelmsg::fetch($Params['user_parameters']['message_id'])) instanceof erLhcoreClassModelmsg && ($chat = erLhcoreClassModelChat::fetch($message->chat_id)) instanceof erLhcoreClassModelChat && $chat->status != erLhcoreClassModelChat::STATUS_CLOSED_CHAT) {
        $theme->translate();
        $tpl = erLhcoreClassTemplate::getInstance( 'lhchat/reacttomessagesmodal.tpl.php');
        $tpl->set('theme', $theme);
        $tpl->set('messageId', (int)$Params['user_parameters']['message_id']);
        $tpl->set('message', $message);
        echo $tpl->fetch();
    }
}

exit;

?>