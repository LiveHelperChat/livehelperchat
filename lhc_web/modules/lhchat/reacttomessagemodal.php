<?php

erLhcoreClassRestAPIHandler::setHeaders('Content-Type: text/html; charset=UTF-8');

if (isset($Params['user_parameters_unordered']['theme']) && ($themeId = erLhcoreClassChat::extractTheme($Params['user_parameters_unordered']['theme'])) !== false) {
    $theme = erLhAbstractModelWidgetTheme::fetch($themeId);
    if ($theme instanceof erLhAbstractModelWidgetTheme) {
        $theme->translate();
        $tpl = erLhcoreClassTemplate::getInstance( 'lhchat/reacttomessagesmodal.tpl.php');
        $tpl->set('theme', $theme);
        $tpl->set('messageId', $Params['user_parameters']['message_id']);
        $tpl->set('message', erLhcoreClassModelmsg::fetch($Params['user_parameters']['message_id']));
        echo $tpl->fetch();
    }
}

exit;

?>