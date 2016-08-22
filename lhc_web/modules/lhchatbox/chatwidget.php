<?php

// For IE to support headers if chat is installed on different domain
header('P3P: CP="NOI ADM DEV COM NAV OUR STP"');

$visitorName = erLhcoreClassChatbox::getVisitorName();
$cache = CSCacheAPC::getMem();
$themeID = isset($Params['user_parameters_unordered']['theme']) && (int)$Params['user_parameters_unordered']['theme'] > 0 ? (int)$Params['user_parameters_unordered']['theme'] : 0;

if ($themeID == 0) {
    $defaultTheme = erLhcoreClassModelChatConfig::fetch('default_theme_id')->current_value;
    if ($defaultTheme > 0) {
        $themeID = $defaultTheme;
    }
}

$cacheKey = md5('chatbox_version_'.$cache->getCacheVersion('chatbox_'.(string)$Params['user_parameters_unordered']['identifier']).'_theme_'.$themeID.'_identifier_'.(string)$Params['user_parameters_unordered']['identifier'].'_hash_'.(string)$Params['user_parameters_unordered']['hashchatbox'].$visitorName.'_height_'.(int)$Params['user_parameters_unordered']['chat_height'].'_sound_'.(int)$Params['user_parameters_unordered']['sound'].'_mode_'.(string)$Params['user_parameters_unordered']['mode'].'_siteaccess_'.erLhcoreClassSystem::instance()->SiteAccess);

if (($Result = $cache->restore($cacheKey)) === false)
{
    $referer = '';
    $tpl = erLhcoreClassTemplate::getInstance( 'lhchatbox/chatwidget.tpl.php');
    $tpl->set('chatbox_chat_height',(!is_null($Params['user_parameters_unordered']['chat_height']) && (int)$Params['user_parameters_unordered']['chat_height'] > 0) ? (int)$Params['user_parameters_unordered']['chat_height'] : 220);

    if ($Params['user_parameters_unordered']['sound'] !== null && is_numeric($Params['user_parameters_unordered']['sound'])) {
        erLhcoreClassModelUserSetting::setSetting('chat_message',(int)$Params['user_parameters_unordered']['sound'] == 1 ? 1 : 0);
    }

    $errors = array();

    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chatbox.before_created', array('errors' => & $errors));

    if (empty($errors)) {
        $chatbox = erLhcoreClassChatbox::getInstance((string)$Params['user_parameters_unordered']['identifier'],(string)$Params['user_parameters_unordered']['hashchatbox']);
        $tpl->set('chatbox',$chatbox);
    } else {
        $tpl->set('errors', $errors);
    }

    $tpl->set('referer',$referer);
    if (isset($_GET['URLReferer']))
    {
        $referer = $_GET['URLReferer'];
        $tpl->set('referer',$referer);
    }

    if (isset($_POST['URLRefer']))
    {
        $referer = $_POST['URLRefer'];
        $tpl->set('referer',$_POST['URLRefer']);
    }

    $embedMode = false;
    $modeAppend = '';
    if ((string)$Params['user_parameters_unordered']['mode'] == 'embed') {
        $embedMode = true;
        $modeAppend = '/(mode)/embed';
    }

    if ($themeID > 0){
        try {
            $theme = erLhAbstractModelWidgetTheme::fetch($themeID);
            $Result['theme'] = $theme;
            $modeAppend .= '/(theme)/'.$theme->id;
        } catch (Exception $e) {

        }
    }

    $tpl->set('append_mode',$modeAppend);

    $Result['content'] = $tpl->fetch();
    $Result['pagelayout'] = 'widget';
    $Result['pagelayout_css_append'] = 'widget-chat';
    $Result['dynamic_height'] = true;
    $Result['dynamic_height_message'] = 'lhc_sizing_chatbox';
    $Result['additional_post_message'] = 'lhc_chb:nick:'.htmlspecialchars($visitorName,ENT_QUOTES);
    $Result['is_sync_required'] = true;

    if ($embedMode == true) {
        $Result['dynamic_height_message'] = 'lhc_sizing_chatbox_page';
        $Result['pagelayout_css_append'] = 'embed-widget';
    }

    $cache->store($cacheKey,$Result);
}
?>