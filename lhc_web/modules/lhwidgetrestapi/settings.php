<?php

erLhcoreClassRestAPIHandler::setHeaders();

if (isset($_GET['dep']) && is_array($_GET['dep']) && !empty($_GET['dep'])){
    $department = (isset($_GET['dep']) && is_array($_GET['dep']) && !empty($_GET['dep']) ? $_GET['dep'] : false);
} else if (isset($_GET['dep']) && $_GET['dep'] != '') {
    $department = explode(',',$_GET['dep']);
} else {
    $department = false;
}

if (is_array($department)) {
    erLhcoreClassChat::validateFilterIn($department);
}

$outputResponse = array(
    'isOnline' => erLhcoreClassChat::isOnline($department, false, array(
        'online_timeout' => (int) erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data['online_timeout'],
        'ignore_user_status' => (isset($_GET['ignore_user_status']) && $_GET['ignore_user_status'] == 'true')
    )),
    'hideOffline' => false,
    'vid' => isset($_GET['vid']) ? $_GET['vid'] : substr(sha1(mt_rand() . microtime()),0,20)
);

$ignorable_ip = erLhcoreClassModelChatConfig::fetch('ignorable_ip')->current_value;
$fullHeight = (isset($Params['user_parameters_unordered']['fullheight']) && $Params['user_parameters_unordered']['fullheight'] == 'true') ? true : false;

if ( $ignorable_ip == '' || !erLhcoreClassIPDetect::isIgnored(erLhcoreClassIPDetect::getIP(),explode(',',$ignorable_ip))) {
    if (is_array($Params['user_parameters_unordered']['department'])){
        erLhcoreClassChat::validateFilterIn($Params['user_parameters_unordered']['department']);
        $department = $Params['user_parameters_unordered']['department'];
    } else {
        $department = false;
    }

    $jsVars = array();

    // Additional javascript variables
    if (is_array($department) && !empty($department)) {
        foreach (erLhAbstractModelChatVariable::getList(array('ignore_fields' => array('dep_id','var_name','var_identifier','type'), 'customfilter' => array('dep_id = 0 OR dep_id IN (' . implode(',',$department) .')'))) as $jsVar) {
            $jsVars[] = array('id' => $jsVar->id,'var' => $jsVar->js_variable);
        }
    } else {
        foreach (erLhAbstractModelChatVariable::getList(array('ignore_fields' => array('dep_id','var_name','var_identifier','type'), 'filter' => array('dep_id' => 0))) as $jsVar) {
            $jsVars[] = array('id' => $jsVar->id, 'var' => $jsVar->js_variable);
        }
    }

    $outputResponse['js_vars'] = $jsVars;

    if (is_array($Params['user_parameters_unordered']['ua'])){
        $uarguments = $Params['user_parameters_unordered']['ua'];
    } else {
        $uarguments = false;
    }

    $proactiveInviteActive = erLhcoreClassModelChatConfig::fetch('pro_active_invite')->current_value;

    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.chatcheckoperatormessage', array('proactive_active' => & $proactiveInviteActive));

    $injectInvitation = array();
    $userInstance = erLhcoreClassModelChatOnlineUser::handleRequest(array('inject_html' => & $injectInvitation, 'tag' => isset($_GET['tag']) ? $_GET['tag'] : false, 'uactiv' => 1, 'wopen' => 0 /*@todo add support if request is made and widget is open, chat is going*/, 'tpl' => & $tpl, 'tz' => $_GET['tz'], 'message_seen_timeout' => erLhcoreClassModelChatConfig::fetch('message_seen_timeout')->current_value, 'department' => $department, 'identifier' => (string)$Params['user_parameters_unordered']['identifier'], 'pages_count' => true, 'vid' => $outputResponse['vid'], 'check_message_operator' => true, 'pro_active_limitation' =>  erLhcoreClassModelChatConfig::fetch('pro_active_limitation')->current_value, 'pro_active_invite' => $proactiveInviteActive));

    // Exit if not required
    $statusGeoAdjustment = erLhcoreClassChat::getAdjustment(erLhcoreClassModelChatConfig::fetch('geoadjustment_data')->data_value,'',false, $userInstance);
    if ($statusGeoAdjustment['status'] == 'offline' || $statusGeoAdjustment['status'] == 'hidden') {
        $outputResponse['hideOffline'] = false;
        $outputResponse['isOnline'] = false;
    }

    if (erLhcoreClassModelChatConfig::fetch('track_footprint')->current_value == 1 && isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
        erLhcoreClassModelChatOnlineUserFootprint::addPageView($userInstance);
    }

    if ($userInstance !== false) {
        if ($userInstance->invitation_id == -1) {
            $userInstance->invitation_id = 0;
            $userInstance->invitation_assigned = true;
            $userInstance->saveThis();
        }
    }
}

if (isset($_GET['theme']) && (int)$_GET['theme'] > 0){
    $outputResponse['theme'] = (int)$_GET['theme'];
} else {
    $defaultTheme = erLhcoreClassModelChatConfig::fetch('default_theme_id')->current_value;
    if ($defaultTheme > 0) {
        $outputResponse['theme'] = (int)$defaultTheme;
    }
}

if (isset($outputResponse['theme'])){
    $theme = erLhAbstractModelWidgetTheme::fetch($outputResponse['theme']);
    if ($theme instanceof erLhAbstractModelWidgetTheme) {
        if (isset($theme->bot_configuration_array['wwidth']) && $theme->bot_configuration_array['wwidth'] > 0) {
            $outputResponse['chat_ui']['wwidth'] = $theme->bot_configuration_array['wwidth'];
        }

        if (isset($theme->bot_configuration_array['wheight']) && $theme->bot_configuration_array['wheight'] > 0) {
            $outputResponse['chat_ui']['wheight'] = $theme->bot_configuration_array['wheight'];
        }
    }
}

if (erLhcoreClassModelChatConfig::fetch('use_secure_cookie')->current_value == 1) {
    $outputResponse['secure_cookie'] = true;
}

if (($domain = erLhcoreClassModelChatConfig::fetch('track_domain')->current_value) != '') {
    $outputResponse['domain'] = $domain;
}

$ts = time();
$outputResponse['v'] = 5;
$outputResponse['hash'] = sha1(erLhcoreClassIPDetect::getIP() . $ts . erConfigClassLhConfig::getInstance()->getSetting( 'site', 'secrethash' ));
$outputResponse['hash_ts'] = $ts;
$outputResponse['static'] = array(
    'screenshot' => erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value . '//' . $_SERVER['HTTP_HOST'] . erLhcoreClassDesign::design('js/html2canvas.min.js'),
    'cobrowser' => erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value . '//' . $_SERVER['HTTP_HOST'] . erLhcoreClassDesign::designJS('js/cobrowse/compiled/cobrowse.visitor.min.js'),
    'app' => erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value . '//' . $_SERVER['HTTP_HOST'] . ((isset($_GET['ie']) && $_GET['ie'] == 'true') ? erLhcoreClassDesign::design('js/widgetv2/react.app.ie.js') . '?v=' . $outputResponse['v'] : erLhcoreClassDesign::design('js/widgetv2/react.app.js') . '?v=' . $outputResponse['v']),
    'widget_css' => erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value . '//' . $_SERVER['HTTP_HOST'] . erLhcoreClassDesign::designCSS('css/widgetv2/bootstrap.min.css;css/widgetv2/widget.css;css/widgetv2/widget_override.css'),
    'widget_mobile_css' => erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value . '//' . $_SERVER['HTTP_HOST'] . erLhcoreClassDesign::designCSS('css/widgetv2/widget_mobile.css;css/widgetv2/widget_mobile_override.css'),
    'embed_css' => erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value . '//' . $_SERVER['HTTP_HOST'] . erLhcoreClassDesign::designCSS('css/widgetv2/embed.css;css/widgetv2/embed_override.css'),
);

$outputResponse['chunks_location'] = erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value . '//' . $_SERVER['HTTP_HOST'] . erLhcoreClassDesign::design('js/widgetv2');

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('widgetrestapi.settings', array('output' => & $outputResponse));

erLhcoreClassRestAPIHandler::outputResponse($outputResponse);
exit();