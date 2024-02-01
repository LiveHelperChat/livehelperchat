<?php

header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s', time() + 60 * 60 * 8) . ' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

$tpl = erLhcoreClassTemplate::getInstance( isset($templateOverride) ? $templateOverride : 'lhchat/start.tpl.php');

$dep = false;

if (is_array($Params['user_parameters_unordered']['department'])) {
    $parametersDepartment = erLhcoreClassChat::extractDepartment($Params['user_parameters_unordered']['department']);
    $Params['user_parameters_unordered']['department'] = $parametersDepartment['system'];
    $dep = $Params['user_parameters_unordered']['department'];
    $Result['chat_args']['departments'] = $dep;
}

$startDataDepartment = false;

if (is_array($dep) && !empty($dep) && count($dep) == 1) {
    $dep_id = $dep[0];
    $startDataDepartment = erLhcoreClassModelChatStartSettings::findOne(array('customfilter' => array("((`dep_ids` != '' AND JSON_CONTAINS(`dep_ids`,'" . (int)$dep_id . "','$')) OR department_id = " . (int)$dep_id . ")" )));
    if ($startDataDepartment instanceof erLhcoreClassModelChatStartSettings) {
        $startDataFields = $startDataDepartment->data_array;
    }
    $Result['chat_args']['dep_id'] = $dep_id;
    $tpl->set('dep_id',$dep_id);
}

if ($startDataDepartment === false) {
    $startData = erLhcoreClassModelChatConfig::fetch('start_chat_data');
    $start_data_fields = $startDataFields = (array)$startData->data;
}

if (isset($startDataFields['requires_dep']) && $startDataFields['requires_dep'] == true && empty($dep)) {
    $Result['pagelayout'] = 'userchat';
    $tpl = erLhcoreClassTemplate::getInstance( 'lhkernel/alert_info.tpl.php');
    $tpl->set('msg',erTranslationClassLhTranslation::getInstance()->getTranslation('chat/start','Department is required!'));
    $tpl->set('hide_close_icon',true);
    $Result['hide_close_window'] = true;
    $Result['content'] = $tpl->fetch();
    return $Result;
}

if ($Params['user_parameters_unordered']['vid'] == 'undefined') {
    $Params['user_parameters_unordered']['vid'] = null;
}

if (isset($startDataFields['disable_start_chat']) && $startDataFields['disable_start_chat'] == true && empty($Params['user_parameters_unordered']['vid']) && (!is_numeric($Params['user_parameters_unordered']['id']) || $Params['user_parameters_unordered']['hash'] == '')) {
    $Result['pagelayout'] = 'userchat';
    $tpl = erLhcoreClassTemplate::getInstance( 'lhkernel/alert_info.tpl.php');
    $tpl->set('msg',erTranslationClassLhTranslation::getInstance()->getTranslation('chat/start','Disabled!'));
    $tpl->set('hide_close_icon',true);
    $Result['hide_close_window'] = true;
    $Result['content'] = $tpl->fetch();
    return $Result;
}

$vid = $Params['user_parameters_unordered']['vid'] != '' ? $Params['user_parameters_unordered']['vid'] : null;

if (empty($vid) && !((isset($_GET['cd']) && $_GET['cd'] == 1) || erLhcoreClassModelChatConfig::fetch('track_online_visitors')->current_value != 1)) {

    // Incorrect mod_rewrite rule as fetch was for an image.
    if ((isset($_SERVER['HTTP_SEC_FETCH_DEST']) && $_SERVER['HTTP_SEC_FETCH_DEST'] == 'image') ||
        (isset($_SERVER['HTTP_USER_AGENT']) && erLhcoreClassModelChatOnlineUser::isBot($_SERVER['HTTP_USER_AGENT']))
    ) {
        http_response_code(404);
        exit;
    }

    if (isset($_COOKIE['lhc_vid']) && $_COOKIE['lhc_vid'] != 'undefined') {
        $vid = $_COOKIE['lhc_vid'];
    } else {
        $vid = substr(sha1(mt_rand() . microtime()),0,20);
    }

    setcookie("lhc_vid", $vid, time()+60*60*24*365, '/', '', erLhcoreClassSystem::$httpsMode, true);
    $userInstance = erLhcoreClassModelChatOnlineUser::handleRequest(array('tag' => isset($_GET['tag']) ? $_GET['tag'] : false, 'uactiv' => 1, 'wopen' => 0, 'tpl' => & $tpl, 'tz' => (isset($_GET['tz']) ? $_GET['tz'] : null), 'message_seen_timeout' => erLhcoreClassModelChatConfig::fetch('message_seen_timeout')->current_value, 'department' =>( is_array($Params['user_parameters_unordered']['department']) ? $Params['user_parameters_unordered']['department'] : array()), 'identifier' => (isset($_GET['idnt']) ? (string)$_GET['idnt'] : ''), 'pages_count' => true, 'vid' => $vid, 'check_message_operator' => false, 'pro_active_limitation' =>  erLhcoreClassModelChatConfig::fetch('pro_active_limitation')->current_value, 'pro_active_invite' => false));
} elseif (!empty($vid)) {
    $userInstance = erLhcoreClassModelChatOnlineUser::fetchByVid($vid);
}

$themeArray = [];

if (isset($Params['user_parameters_unordered']['theme'])) {
    $themeArray = explode(',', $Params['user_parameters_unordered']['theme']);
}

$setTheme = false;

if (count($themeArray) > 1 && isset($userInstance) && $userInstance !== false) {
    $userAttributes = $userInstance->online_attr_system_array;
    if (isset($userAttributes['lhc_theme']) && in_array($userAttributes['lhc_theme'], $themeArray) && isset($userAttributes['lhc_theme_exp']) && $userAttributes['lhc_theme_exp'] > time()) {
        $Params['user_parameters_unordered']['theme'] = $userAttributes['lhc_theme'];
    } else {
        $setTheme = true;
        $Params['user_parameters_unordered']['theme'] = $themeArray[array_rand($themeArray)];
    }
} elseif (count($themeArray) > 1) {
    $Params['user_parameters_unordered']['theme'] = $themeArray[array_rand($themeArray)];
}

if (isset($Params['user_parameters_unordered']['theme']) && ($themeId = erLhcoreClassChat::extractTheme($Params['user_parameters_unordered']['theme'])) !== false) {
    $Params['user_parameters_unordered']['theme'] = $themeId;
}

if (!is_numeric($Params['user_parameters_unordered']['theme'])) {

    if (isset($dep_id) && $dep_id > 0) {
        $departmentObject = erLhcoreClassModelDepartament::fetch($dep_id);
        if (is_object($departmentObject)) {

            if (isset($departmentObject->bot_configuration_array['theme_ind']) && $departmentObject->bot_configuration_array['theme_ind'] != 0) {
                $Params['user_parameters_unordered']['theme'] = explode(',', $departmentObject->bot_configuration_array['theme_ind']);
            }

            if (!isset($Params['user_parameters_unordered']['theme']) && isset($departmentObject->bot_configuration_array['theme_default']) && $departmentObject->bot_configuration_array['theme_default'] != 0) {
                $Params['user_parameters_unordered']['theme'] = explode(',', $departmentObject->bot_configuration_array['theme_default']);
            }

            if (isset($Params['user_parameters_unordered']['theme']) && count($Params['user_parameters_unordered']['theme']) > 1 && isset($userInstance) && $userInstance !== false) {
                $userAttributes = $userInstance->online_attr_system_array;
                if (isset($userAttributes['lhc_theme']) && in_array($userAttributes['lhc_theme'],$Params['user_parameters_unordered']['theme']) && isset($userAttributes['lhc_theme_exp']) && $userAttributes['lhc_theme_exp'] > time()) {
                    $Params['user_parameters_unordered']['theme'] = $userAttributes['lhc_theme'];
                } else {
                    $setTheme = true;
                    $Params['user_parameters_unordered']['theme'] =$Params['user_parameters_unordered']['theme'][array_rand($Params['user_parameters_unordered']['theme'])];
                }
            } elseif (isset($Params['user_parameters_unordered']['theme'])) {
                $Params['user_parameters_unordered']['theme'] = $Params['user_parameters_unordered']['theme'][array_rand($Params['user_parameters_unordered']['theme'])];
            }
        }
    }

    if (!is_numeric($Params['user_parameters_unordered']['theme'])) {
        $defaultTheme = erLhcoreClassModelChatConfig::fetch('default_theme_id')->current_value;
        if ($defaultTheme != '0' && $defaultTheme != '') {
            $themeArray = explode(',', $defaultTheme);
            if (count($themeArray) > 1 && isset($userInstance) && $userInstance !== false) {
                $userAttributes = $userInstance->online_attr_system_array;
                if (isset($userAttributes['lhc_theme']) && in_array($userAttributes['lhc_theme'], $themeArray) && isset($userAttributes['lhc_theme_exp']) && $userAttributes['lhc_theme_exp'] > time()) {
                    $Params['user_parameters_unordered']['theme'] = $userAttributes['lhc_theme'];
                } else {
                    $setTheme = true;
                    $Params['user_parameters_unordered']['theme'] = $themeArray[array_rand($themeArray)];
                }
            } else {
                $Params['user_parameters_unordered']['theme'] = $themeArray[array_rand($themeArray)];
            }
        }
    }
}

$online = erLhcoreClassChat::isOnline($dep, false, array(
    'online_timeout' => (int) erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data['online_timeout'],
    'ignore_user_status' => (int)erLhcoreClassModelChatConfig::fetch('ignore_user_status')->current_value
));

$leaveamessage = $Params['user_parameters_unordered']['leaveamessage'] === 'true' || (isset($startDataFields['force_leave_a_message']) && $startDataFields['force_leave_a_message'] == true);
$tpl->set('leaveamessage',$leaveamessage);
$tpl->set('department',is_array($Params['user_parameters_unordered']['department']) ? $parametersDepartment['argument'] : array());
$tpl->set('id',$Params['user_parameters_unordered']['id'] > 0 ? (int)$Params['user_parameters_unordered']['id'] : null);
$tpl->set('hash',$Params['user_parameters_unordered']['hash'] != '' ? $Params['user_parameters_unordered']['hash'] : null);
$tpl->set('isMobile',$Params['user_parameters_unordered']['mobile'] == 'true');
$tpl->set('theme',$Params['user_parameters_unordered']['theme'] > 0 ? (int)$Params['user_parameters_unordered']['theme'] : null);
$tpl->set('vid',$vid);
$tpl->set('identifier',$Params['user_parameters_unordered']['identifier'] != '' ? $Params['user_parameters_unordered']['identifier'] : null);
$tpl->set('inv',$Params['user_parameters_unordered']['inv'] != '' ? $Params['user_parameters_unordered']['inv'] : null);
$tpl->set('survey',$Params['user_parameters_unordered']['survey'] != '' ? $Params['user_parameters_unordered']['survey'] : null);
$tpl->set('priority',$Params['user_parameters_unordered']['priority'] != '' ? $Params['user_parameters_unordered']['priority'] : null);
$tpl->set('operator',$Params['user_parameters_unordered']['operator'] != '' ? (int)$Params['user_parameters_unordered']['operator'] : null);
$tpl->set('bot',$Params['user_parameters_unordered']['bot'] != '' ? (int)$Params['user_parameters_unordered']['bot'] : null);
$tpl->set('trigger',$Params['user_parameters_unordered']['trigger'] != '' ? (int)$Params['user_parameters_unordered']['trigger'] : null);
$tpl->set('online',$online);
$tpl->set('font_size',$Params['user_parameters_unordered']['fs'] != '' ? (int)$Params['user_parameters_unordered']['fs'] : null);
$tpl->set('mode',$Params['user_parameters_unordered']['mode'] != '' && in_array($Params['user_parameters_unordered']['mode'],['embed','popup','widget']) ? $Params['user_parameters_unordered']['mode']  : 'popup');
$tpl->set('sound',is_numeric($Params['user_parameters_unordered']['sound']) ? (int)$Params['user_parameters_unordered']['sound'] : (int) erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data['new_message_sound_user_enabled']);
$tpl->set('app_scope', 'lhc');

if ($Params['user_parameters_unordered']['scope'] != ''){
    $Result['app_scope'] = strip_tags($Params['user_parameters_unordered']['scope']);
    $tpl->set('app_scope', strip_tags($Params['user_parameters_unordered']['scope']));
}

$ts = time();
$tpl->set('captcha',array(
    'hash' => sha1(erLhcoreClassIPDetect::getIP() . $ts . erConfigClassLhConfig::getInstance()->getSetting( 'site', 'secrethash' )),
    'ts' => $ts
));

$referrer = erLhcoreClassModelChatOnlineUser::getReferer();

$tpl->set('domain_lhc',null);

if (!empty($referrer)) {
    $partsReferer = parse_url($referrer);
    if (isset($partsReferer['host'])) {
        $tpl->set('domain_lhc',$partsReferer['host']);
    }
}

// Prefill by get
if (isset($_GET['prefill']) && is_array($_GET['prefill']) && !empty($_GET['prefill'])) {
    $prefillOptions = array();
    foreach ($_GET['prefill'] as $field => $value) {
        if ($field == 'email') {
            $prefillOptions[] = array('Email' => $value);
        } else if ($field == 'username') {
            $prefillOptions[] = array('Username' => $value);
        }else if ($field == 'phone') {
            $prefillOptions[] = array('Phone' => $value);
        } else if ($field == 'question') {
            $prefillOptions[] = array('Question' => $value);
        }
    }
    $tpl->set('prefill',$prefillOptions);
}

if (isset($_GET['value_items_admin']) && is_array($_GET['value_items_admin']) && !empty($_GET['value_items_admin'])) {
    $options = array();
    foreach ($_GET['value_items_admin'] as $field => $value) {
        $options[] = array('index' => $field, 'value' => $value);
    }
    $tpl->set('prefill_admin',$options);
}

if (isset($_GET['name']) && is_array($_GET['name']) && !empty($_GET['name'])) {
    $attributes = array();
    foreach ($_GET['name'] as $index => $value) {
        $attributes[] = array(
            'show' => (((isset($_GET['sh'][$index]) && ($_GET['sh'][$index] == 'on' || $_GET['sh'][$index] == 'off')) ? $_GET['sh'][$index] : 'b')),
            'value' => $_GET['value'][$index],
            'index' => $index,
            'name' => $value,
            'class' => 'form-control form-control-sm',
            'type' => isset($_GET['type'][$index]) ? $_GET['type'][$index] : 'hidden',
            'identifier' => ('additional_' . $index),
            'placeholder' => (isset($_GET['placeholder'][$index]) ? $_GET['placeholder'][$index] : ''),
            'width' => (isset($_GET['size'][$index]) ? $_GET['size'][$index] : 6),
            'encrypted' => (isset($_GET['encattr'][$index]) && $_GET['encattr'][$index] === 't'),
            'required' => (isset($_GET['req'][$index]) && $_GET['req'][$index] === 't'),
            'label' => $value,
        );
    }

    $tpl->set('custom_fields',$attributes);
}

if (isset($_GET['jsvar']) && is_array($_GET['jsvar']) && !empty($_GET['jsvar'])) {
    $tpl->set('jsVars',$_GET['jsvar']);
}

if (isset($Params['user_parameters_unordered']['theme']) && is_numeric($Params['user_parameters_unordered']['theme'])) {
    $themeObject = erLhAbstractModelWidgetTheme::fetch($Params['user_parameters_unordered']['theme']);

    if ($themeObject instanceof erLhAbstractModelWidgetTheme) {

        if ($setTheme === true && isset($themeObject->bot_configuration_array['theme_expires']) && (int)$themeObject->bot_configuration_array['theme_expires'] > 0 && isset($userInstance) && $userInstance !== false) {
            $userAttributes['lhc_theme'] = $Params['user_parameters_unordered']['theme'];
            $userAttributes['lhc_theme_exp'] = time() + $themeObject->bot_configuration_array['theme_expires'];
            $userInstance->online_attr_system_array = $userAttributes;
            $userInstance->online_attr_system = json_encode($userAttributes);
            $userInstance->updateThis(['update' => ['online_attr_system']]);
        }

        $Result['theme'] = $themeObject;
        $Result['theme_v'] = $themeObject->modified;
    } else {
        $Result['theme_v'] = time();
    }
}

if ($Params['user_parameters_unordered']['mobile'] == 'true') {
    $Result['mobile'] = true;
}

$Result['content'] = $tpl->fetch();

if ($leaveamessage === false && $online === false){
    $Result['pagelayout'] = 'userchat';
} else {
    if (isset($Result['theme']) && is_object($Result['theme'])) {
        $Result['theme_obj'] = $Result['theme'];
        $Result['theme'] = $Result['theme']->alias != '' ? $Result['theme']->alias : $Result['theme']->id;
    }
    $Result['pagelayout'] = 'userchat2';
}

if (isset($pagelayoutOverride)) {
    $Result['pagelayout'] = $pagelayoutOverride;
}


?>
