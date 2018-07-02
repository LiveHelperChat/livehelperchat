<?php

$tpl = erLhcoreClassTemplate::getInstance('lhnotifications/read.tpl.php');

try {

    $themeAppend = '';

    if (isset($Params['user_parameters_unordered']['theme']) && (int)$Params['user_parameters_unordered']['theme'] > 0){
        try {
            $theme = erLhAbstractModelWidgetTheme::fetch($Params['user_parameters_unordered']['theme']);
            $Result['theme'] = $theme;
            $tpl->set('theme',$theme);
            $themeAppend = '/(theme)/'.$theme->id;
        } catch (Exception $e) {

        }
    } else {
        $defaultTheme = erLhcoreClassModelChatConfig::fetch('default_theme_id')->current_value;
        if ($defaultTheme > 0) {
            try {
                $theme = erLhAbstractModelWidgetTheme::fetch($defaultTheme);
                $tpl->set('theme',$theme);
                $Result['theme'] = $theme;
                $themeAppend = '/(theme)/'.$theme->id;
            } catch (Exception $e) {

            }
        }
    }

    $chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters_unordered']['id']);

    if ($chat->hash == $Params['user_parameters_unordered']['hashread'])
    {
        $tpl->set('chat',$chat);

        $detect = new Mobile_Detect;
        if ($Params['user_parameters_unordered']['mode'] == 'widget' || $detect->isMobile()) {
            $Result = erLhcoreClassModule::reRun(erLhcoreClassDesign::baseurlRerun('chat/chatwidgetchat') . '/' . $chat->id . '/' . $chat->hash . $themeAppend);
            return true;
        } else {
            $Result = erLhcoreClassModule::reRun( erLhcoreClassDesign::baseurlRerun('chat/chat') . '/' . $chat->id . '/' . $chat->hash . $themeAppend);
            return true;
        }
    }

} catch (Exception $e) {

}

$Result['content'] = $tpl->fetch();
$Result['pagelayout'] = 'userchat';
?>