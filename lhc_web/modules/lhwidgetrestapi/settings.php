<?php

erLhcoreClassRestAPIHandler::setHeaders();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (isset($_SERVER['HTTP_ORIGIN']) && !empty($_SERVER['HTTP_ORIGIN'])) {
    $validDomains = (string)erLhcoreClassModelChatConfig::fetch('valid_domains')->current_value;
    if (!empty($validDomains)) {
        $validDomainsList = explode(',',$validDomains);
        $validDomain = false;
        foreach ($validDomainsList as $validDomainItem) {
            if (strpos($_SERVER['HTTP_ORIGIN'],trim($validDomainItem)) !== false) {
                $validDomain = true;
            }
        }

        if ($validDomain == false) {
            erLhcoreClassRestAPIHandler::outputResponse(array('terminate' => true));
            exit;
        }
    }
}

if (isset($_SERVER['HTTP_USER_AGENT']) && erLhcoreClassModelChatOnlineUser::isBot($_SERVER['HTTP_USER_AGENT'])) {
    erLhcoreClassRestAPIHandler::outputResponse(array('terminate' => true));
    exit;
}

if (isset($_GET['dep']) && is_array($_GET['dep']) && !empty($_GET['dep'])){
    $department = (isset($_GET['dep']) && is_array($_GET['dep']) && !empty($_GET['dep']) ? $_GET['dep'] : false);
} else if (isset($_GET['dep']) && $_GET['dep'] != '') {
    $department = explode(',',$_GET['dep']);
} else {
    $department = false;
}

if (is_array($department)) {
    $parametersDepartment = erLhcoreClassChat::extractDepartment($department);
    $department = $parametersDepartment['system'];
    $departmentArgument = $parametersDepartment['argument'];
} else {
    $departmentArgument = $department;
}

$departmentUpdated = $department;

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('widgetrestapi.settings_department_verify', array('department' => & $departmentUpdated));

$outputResponse = array(
    'isOnline' => erLhcoreClassChat::isOnline($departmentUpdated, false, array(
        'online_timeout' => (int) erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data['online_timeout'],
        'ignore_user_status' => (int)erLhcoreClassModelChatConfig::fetch('ignore_user_status')->current_value
    )),
    'hideOffline' => false,
    'vid' => isset($_GET['vid']) ? $_GET['vid'] : substr(sha1(mt_rand() . microtime()),0,20)
);

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('widgetrestapi.settings_department_after_verify', array('department' => & $department, 'output' => & $outputResponse));

$ignorable_ip = erLhcoreClassModelChatConfig::fetch('ignorable_ip')->current_value;
$fullHeight = (isset($Params['user_parameters_unordered']['fullheight']) && $Params['user_parameters_unordered']['fullheight'] == 'true') ? true : false;

if ( $ignorable_ip == '' || !erLhcoreClassIPDetect::isIgnored(erLhcoreClassIPDetect::getIP(),explode(',',$ignorable_ip))) {

    $jsVars = array();

    $mapFieldsToPrefill = [
        'lhc.email' => 'Email',
        'lhc.nick' => 'Username',
        'lhc.phone' => 'Phone',
        'lhc.question' => 'Question',
    ];

    // Additional javascript variables
    if (is_array($department) && !empty($department)) {
        foreach (erLhAbstractModelChatVariable::getList(array('ignore_fields' => array('dep_id','var_name','type'), 'customfilter' => array('dep_id = 0 OR dep_id IN (' . implode(',',$department) .')'))) as $jsVar) {
            $itemVar = array('id' => $jsVar->id,'var' => $jsVar->js_variable);
            if (key_exists($jsVar->var_identifier,$mapFieldsToPrefill)) {
                $itemVar['type'] = $mapFieldsToPrefill[$jsVar->var_identifier];
            }
            $jsVars[] = $itemVar;
        }
    } else {
        foreach (erLhAbstractModelChatVariable::getList(array('ignore_fields' => array('dep_id','var_name','type'), 'filter' => array('dep_id' => 0))) as $jsVar) {
            $itemVar =  array('id' => $jsVar->id, 'var' => $jsVar->js_variable);
            if (key_exists($jsVar->var_identifier,$mapFieldsToPrefill)) {
                $itemVar['type'] = $mapFieldsToPrefill[$jsVar->var_identifier];
            }
            $jsVars[] = $itemVar;
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

    if ((isset($_GET['cd']) && $_GET['cd'] == 1) || erLhcoreClassModelChatConfig::fetch('track_online_visitors')->current_value != 1) {
        $userInstance = false;
    } else {
        $userInstance = erLhcoreClassModelChatOnlineUser::handleRequest(array('inject_html' => & $injectInvitation, 'tag' => isset($_GET['tag']) ? $_GET['tag'] : false, 'uactiv' => 1, 'wopen' => 0 /*@todo add support if request is made and widget is open, chat is going*/, 'tpl' => & $tpl, 'tz' => (isset($_GET['tz']) ? $_GET['tz'] : null), 'message_seen_timeout' => erLhcoreClassModelChatConfig::fetch('message_seen_timeout')->current_value, 'department' => $department, 'identifier' => (isset($_GET['idnt']) ? (string)$_GET['idnt'] : ''), 'pages_count' => true, 'vid' => $outputResponse['vid'], 'check_message_operator' => true, 'pro_active_limitation' =>  erLhcoreClassModelChatConfig::fetch('pro_active_limitation')->current_value, 'pro_active_invite' => $proactiveInviteActive));
    }

    // Exit if not required
    $statusGeoAdjustment = erLhcoreClassChat::getAdjustment(erLhcoreClassModelChatConfig::fetch('geoadjustment_data')->data_value,'',false, $userInstance);

    if ($statusGeoAdjustment['status'] == 'offline' || $statusGeoAdjustment['status'] == 'hidden') {

        if ($statusGeoAdjustment['status'] == 'hidden') {
            $outputResponse['hideOffline'] = true;
        }

        $outputResponse['isOnline'] = false;
    }

    if ($userInstance !== false) {

        if (erLhcoreClassModelChatConfig::fetch('track_footprint')->current_value == 1 && erLhcoreClassModelChatOnlineUser::getReferer() != '') {
            erLhcoreClassModelChatOnlineUserFootprint::addPageView($userInstance);
        }

        if ($userInstance->operation != '') {
            $outputResponse['operation'] = explode("\n", trim($userInstance->operation_chat));
            $userInstance->operation = '';
            $userInstance->operation_chat = '';
            $userInstance->updateThis(array('update' => array('operation','operation_chat')));
        }

        if ($userInstance->invitation_id == -1) {
            $userInstance->invitation_id = 0;
            $userInstance->invitation_assigned = true;
            $userInstance->saveThis();
        }
    }
}

$themeArray = [];

if (isset($_GET['theme'])) {
    $themeArray = explode(',', $_GET['theme']);
}

$setTheme = false;

if (count($themeArray) > 1 && isset($userInstance) && $userInstance !== false) {
    $userAttributes = $userInstance->online_attr_system_array;
    if (isset($userAttributes['lhc_theme']) && in_array($userAttributes['lhc_theme'],$themeArray) && isset($userAttributes['lhc_theme_exp']) && $userAttributes['lhc_theme_exp'] > time()) {
        $_GET['theme'] = $userAttributes['lhc_theme'];
    } else {
        $setTheme = true;
        $_GET['theme'] = $themeArray[array_rand($themeArray)];
    }
} elseif (count($themeArray) > 1) {
    $_GET['theme'] = $themeArray[array_rand($themeArray)];
}

$startDataDepartment = false;

if (is_array($department) && !empty($department) && count($department) == 1) {
    $dep_id = $department[0];
    $startDataDepartment = erLhcoreClassModelChatStartSettings::findOne(array('customfilter' => array("((`dep_ids` != '' AND JSON_CONTAINS(`dep_ids`,'" . (int)$dep_id . "','$')) OR department_id = " . (int)$dep_id . ")" )));
    if ($startDataDepartment instanceof erLhcoreClassModelChatStartSettings) {
        $startDataFields = $startDataDepartment->data_array;
    }
}

if (($themeId = erLhcoreClassChat::extractTheme()) !== false) {
    $outputResponse['theme'] = $themeId;
} else {

    if (isset($dep_id) && $dep_id > 0) {
        $departmentObject = erLhcoreClassModelDepartament::fetch($dep_id);
        if (is_object($departmentObject)) {

            if (isset($departmentObject->bot_configuration_array['theme_ind']) && $departmentObject->bot_configuration_array['theme_ind'] != 0) {
                $outputResponse['theme'] = explode(',', $departmentObject->bot_configuration_array['theme_ind']);
            }
            
            if (!isset($outputResponse['theme']) && isset($departmentObject->bot_configuration_array['theme_default']) && $departmentObject->bot_configuration_array['theme_default'] != 0) {
                $outputResponse['theme'] = explode(',', $departmentObject->bot_configuration_array['theme_default']);
            }

            if (isset($outputResponse['theme']) && count($outputResponse['theme']) > 1 && isset($userInstance) && $userInstance !== false) {
                $userAttributes = $userInstance->online_attr_system_array;
                if (isset($userAttributes['lhc_theme']) && in_array($userAttributes['lhc_theme'],$outputResponse['theme']) && isset($userAttributes['lhc_theme_exp']) && $userAttributes['lhc_theme_exp'] > time()) {
                    $outputResponse['theme'] = $userAttributes['lhc_theme'];
                } else {
                    $setTheme = true;
                    $outputResponse['theme'] = $outputResponse['theme'][array_rand($outputResponse['theme'])];
                }
            } elseif (isset($outputResponse['theme'])) {
                $outputResponse['theme'] = $outputResponse['theme'][array_rand($outputResponse['theme'])];
            }
        }
    }

    if (!isset($outputResponse['theme'])) {
        $defaultTheme = erLhcoreClassModelChatConfig::fetch('default_theme_id')->current_value;
        if ($defaultTheme != '0' && $defaultTheme != '') {
            $themeArray = explode(',', $defaultTheme);
            if (count($themeArray) > 1 && isset($userInstance) && $userInstance !== false) {
                $userAttributes = $userInstance->online_attr_system_array;
                if (isset($userAttributes['lhc_theme']) && in_array($userAttributes['lhc_theme'],$themeArray) && isset($userAttributes['lhc_theme_exp']) && $userAttributes['lhc_theme_exp'] > time()) {
                    $outputResponse['theme']  = $userAttributes['lhc_theme'];
                } else {
                    $setTheme = true;
                    $outputResponse['theme']  = $themeArray[array_rand($themeArray)];
                }
            } else {
                $outputResponse['theme']  = $themeArray[array_rand($themeArray)];
            }
        }
    }
}

$pageCSS = false;

$outputResponse['chat_ui']['status_delay'] = 0;

if (isset($outputResponse['theme'])) {
    $theme = erLhAbstractModelWidgetTheme::fetch($outputResponse['theme']);

    if ($theme instanceof erLhAbstractModelWidgetTheme) {

        $outputResponse['theme'] = $theme->alias != '' ? $theme->alias : $theme->id;

        if ($setTheme === true && isset($theme->bot_configuration_array['theme_expires']) && (int)$theme->bot_configuration_array['theme_expires'] > 0 && isset($userInstance) && $userInstance !== false) {
            $userAttributes['lhc_theme'] = $outputResponse['theme'];
            $userAttributes['lhc_theme_exp'] = time() + $theme->bot_configuration_array['theme_expires'];
            $userInstance->online_attr_system_array = $userAttributes;
            $userInstance->online_attr_system = json_encode($userAttributes);
            $userInstance->updateThis(['update' => ['online_attr_system']]);
        }

        if (isset($theme->bot_configuration_array['wwidth']) && $theme->bot_configuration_array['wwidth'] > 0) {
            $outputResponse['chat_ui']['wwidth'] = (int)$theme->bot_configuration_array['wwidth'];
        }

        if ($theme->show_status_delay > 0) {
            $outputResponse['chat_ui']['status_delay'] = (int)$theme->show_status_delay * 1000;
        }

        if (isset($theme->bot_configuration_array['wheight']) && $theme->bot_configuration_array['wheight'] > 0) {
            $outputResponse['chat_ui']['wheight'] = (int)$theme->bot_configuration_array['wheight'];
        }

        if (isset($theme->bot_configuration_array['fscreen_embed']) && $theme->bot_configuration_array['fscreen_embed'] == 1) {
            $outputResponse['chat_ui']['fscreen'] = (int)$theme->bot_configuration_array['fscreen_embed'];
        }

        if (isset($theme->bot_configuration_array['wright']) && is_numeric($theme->bot_configuration_array['wright'])) {
            $outputResponse['chat_ui']['wright'] = (int)$theme->bot_configuration_array['wright'];
        }

        if (isset($theme->bot_configuration_array['hide_iframe']) && $theme->bot_configuration_array['hide_iframe'] == 1) {
            $outputResponse['chat_ui']['hide_iframe'] = 1;
        }

        if (isset($theme->bot_configuration_array['hide_parent']) && $theme->bot_configuration_array['hide_parent'] == 1) {
            $outputResponse['chat_ui']['hide_parent'] = 1;
        }

        if ($theme->widget_pbottom != 0) {
            $outputResponse['chat_ui']['sbottom'] = (int)$theme->widget_pbottom;
        }

        if ($theme->widget_pright != 0) {
            $outputResponse['chat_ui']['sright'] = (int)$theme->widget_pright;
        }

        if (isset($theme->bot_configuration_array['wright_inv']) && is_numeric($theme->bot_configuration_array['wright_inv'])) {
            $outputResponse['chat_ui']['wright_inv'] = (int)$theme->bot_configuration_array['wright_inv'];
        }

        if (isset($theme->bot_configuration_array['wbottom']) && is_numeric($theme->bot_configuration_array['wbottom'])) {
            $outputResponse['chat_ui']['wbottom'] = (int)$theme->bot_configuration_array['wbottom'];
        }

        $outputResponse['theme_v'] = $theme->modified;

        if ($theme->custom_container_css !== ''){
            $outputResponse['cont_css'] =  str_replace(array("\n","\r"), '', $theme->custom_container_css);
        }

        if (isset($theme->bot_configuration_array['kcw']) && $theme->bot_configuration_array['kcw'] == 1) {
            $outputResponse['chat_ui']['kcw'] = 1;
        }

        if (isset($theme->bot_configuration_array['custom_page_css']) && $theme->bot_configuration_array['custom_page_css'] != '') {
            $pageCSS = true;
        }

        if ($theme instanceof erLhAbstractModelWidgetTheme && isset($theme->bot_configuration_array['detect_language']) && $theme->bot_configuration_array['detect_language'] == true) {
            $siteaccess = erLhcoreClassChatValidator::setLanguageByBrowser(true);
            if ($siteaccess != '') {
                $outputResponse['siteaccess'] = $siteaccess . '/';
                erLhcoreClassSystem::setSiteAccess($siteaccess);
            }
        }

        if (isset($theme->bot_configuration_array['header_html']) && $theme->bot_configuration_array['header_html'] != '') {
            $outputResponse['chat_ui']['hhtml'] = $theme->bot_configuration_array['header_html'];
        }

        if (isset($theme->bot_configuration_array['close_in_status']) && $theme->bot_configuration_array['close_in_status'] == true) {
            $outputResponse['chat_ui']['clinst'] = true;
        }

        if ($theme->enable_widget_embed_override == 1) {
           $outputResponse['chat_ui']['leaveamessage'] = $theme->widget_show_leave_form == 1;

           if ($theme->widget_popheight > 0 && $theme->widget_popwidth > 0) {
               $outputResponse['pdim'] = ['pheight' => $theme->widget_popheight, 'pwidth' => $theme->widget_popwidth];
           }

           if ($theme->widget_survey > 0) {
               $outputResponse['survey_id'] = $theme->widget_survey;
           }

           if ($theme->widget_position != '') {
               $outputResponse['wposition'] = $theme->widget_position;
           }
        }

        $outputResponse['chat_ui']['sound_enabled'] = (isset($theme->bot_configuration_array['disable_sound']) && $theme->bot_configuration_array['disable_sound'] == 1) ? 0 : 1;
    }
}

if ((int)erLhcoreClassModelChatConfig::fetch('checkstatus_timeout')->current_value > 0){
    $outputResponse['chat_ui']['check_status'] = (int)erLhcoreClassModelChatConfig::fetch('checkstatus_timeout')->current_value;

    if ((int)erLhcoreClassModelChatConfig::fetch('track_activity')->current_value > 0) {
        $outputResponse['chat_ui']['track_activity'] = true;
    }

    if ((int)erLhcoreClassModelChatConfig::fetch('track_mouse_activity')->current_value > 0) {
        $outputResponse['chat_ui']['track_mouse'] = true;
    }
}

$soundData = erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data_value;

$outputResponse['chat_ui']['proactive_interval'] = (int)($soundData['check_for_operator_msg']);

if (!isset($outputResponse['chat_ui']['sound_enabled'])) {
    $outputResponse['chat_ui']['sound_enabled'] = (int)($soundData['new_message_sound_user_enabled']);
}

if (erLhcoreClassModelChatConfig::fetch('use_secure_cookie')->current_value == 1) {
    $outputResponse['secure_cookie'] = true;
}

if (($domain = erLhcoreClassModelChatConfig::fetch('track_domain')->current_value) != '') {
    $outputResponse['domain'] = $domain;
}



if ($startDataDepartment === false) {
    $startData = erLhcoreClassModelChatConfig::fetch('start_chat_data');
    $start_data_fields = $startDataFields = (array)$startData->data;
}

$disableNeedHelp = false;

if (isset($start_data_fields['pre_conditions']) && !empty($start_data_fields['pre_conditions'])) {
    $preConditions = json_decode($start_data_fields['pre_conditions'], true);
    if (
        (isset($preConditions['online']) && !empty($preConditions['online'])) ||
        (isset($preConditions['offline']) && !empty($preConditions['offline'])) ||
        (isset($preConditions['disable']) && !empty($preConditions['disable'])) ) {
        $outcome = erLhcoreClassChatValidator::validatePreconditions($preConditions, ['is_online' => (int)$outputResponse['isOnline'], 'online_user' => (isset($userInstance) ? $userInstance : false)]);
        if ($outcome['mode'] == 'terminate') {
            erLhcoreClassRestAPIHandler::outputResponse(array('terminate' => true));
            exit;
        } else if ($outcome['mode'] == 'offline') {
            $outputResponse['isOnline'] = false;
        } else if ($outcome['mode'] == 'disable') {
            $disableNeedHelp = true;
            $outputResponse['disable_proactive'] = true;
        }
    }
}

$needHelpTimeout = isset($theme) && $theme instanceof erLhAbstractModelWidgetTheme ? $theme->show_need_help_timeout : erLhcoreClassModelChatConfig::fetch('need_help_tip_timeout')->current_value;

if ($disableNeedHelp === false && ((isset($theme) && $theme instanceof erLhAbstractModelWidgetTheme && $theme->show_need_help == 1 && (!isset($theme->bot_configuration_array['hide_mobile_nh']) || $theme->bot_configuration_array['hide_mobile_nh'] == false || (isset($userInstance) && $userInstance !== false && $theme->bot_configuration_array['hide_mobile_nh'] == true && in_array($userInstance->device_type,array(1,3))) )) || (!isset($theme) && erLhcoreClassModelChatConfig::fetch('need_help_tip')->current_value == 1)) && $needHelpTimeout > 0 && (!isset($_GET['hnh']) || $_GET['hnh'] < (time() - ($needHelpTimeout * 24 * 3600))))
{
    $configInstance = erConfigClassLhConfig::getInstance();

    $nhCloseVisible = true;
    if (isset($theme) && $theme instanceof erLhAbstractModelWidgetTheme && isset($theme->bot_configuration_array['hide_close_nh']) && $theme->bot_configuration_array['hide_close_nh'] == true) {
        $nhCloseVisible = false;
    }

    $outputResponse['nh']['html'] = '<div class="container-fluid overflow-auto fade-in p-3 pb-4 {dev_type}" >
<div class="shadow rounded bg-white nh-background">
    <div class="p-2" id="start-chat-btn" style="cursor: pointer">
        ' . ($nhCloseVisible === false ? '' : '<button type="button" id="close-need-help-btn" class="btn-close position-absolute" style="' . ($configInstance->getDirLanguage('dir_language') == 'ltr' ? 'right' : 'left') . ':30px;top:25px;" aria-label="Close">
          
        </button>') . '
        <div class="d-flex">
          <div class="p-1"><img style="min-width: 50px;" alt="Customer service" class="img-fluid rounded-circle" src="{{need_help_image_url}}"/></div>
          <div class="p-1 flex-grow-1"><h6 class="mb-0">{{need_help_header}}</h6>
            <p class="mb-1" style="font-size: 14px">{{need_help_body}}</p></div>
        </div>
    </div>
</div>
</div>';

    $outputResponse['nh']['delay'] = 1500;

    $translationInstance = erTranslationClassLhTranslation::getInstance();

    if (isset($theme) && $theme instanceof erLhAbstractModelWidgetTheme) {

        if ($theme->show_need_help_delay > 0) {
            $outputResponse['nh']['delay'] = (int)$theme->show_need_help_delay * 1000;
        }

        if (isset($theme->bot_configuration_array['always_present_nh']) && $theme->bot_configuration_array['always_present_nh'] == true) {
            $outputResponse['nh']['ap'] = true;
        }

        $theme->translate();

        if (isset($theme->bot_configuration_array['need_help_html']) && !empty($theme->bot_configuration_array['need_help_html'])){
            $outputResponse['nh']['html'] = $theme->bot_configuration_array['need_help_html'];
        }

        $replaceVars = $theme->replace_array;

        if ($theme->need_help_image_url === false) {
            if ((isset($theme->bot_configuration_array['nh_avatar']) && $theme->bot_configuration_array['nh_avatar'] != '')) {
                $replaceVars['replace'][8] = erLhcoreClassSystem::getHost() .erLhcoreClassDesign::baseurldirect('widgetrestapi/avatar') . '/' . $theme->bot_configuration_array['nh_avatar'];
            } else {
                $replaceVars['replace'][8] = erLhcoreClassSystem::getHost() . erLhcoreClassDesign::design('images/general/operator.png');
            }
        }

        $replaceVars['search'][] = '{{need_help_header}}';
        $replaceVars['search'][] = '{{need_help_body}}';

        $replaceVars['replace'][] = $theme->need_help_header != '' ? $theme->need_help_header : $translationInstance->getTranslation('chat/getstatus', 'Need help?');
        $replaceVars['replace'][] = $theme->need_help_text != '' ? $theme->need_help_text : $translationInstance->getTranslation('chat/getstatus', 'Our staff are always ready to help!');
    } else {
        $replaceVars = array(
            'search' => array(
                '{{need_help_image_url}}',
                '{{need_help_header}}',
                '{{need_help_body}}',
            ),
            'replace' => array(
                erLhcoreClassSystem::getHost() . erLhcoreClassDesign::design('images/general/operator.png'),
                $translationInstance->getTranslation('chat/getstatus', 'Need help?'),
                $translationInstance->getTranslation('chat/getstatus', 'Our staff are always ready to help!')
            )
        );
    }

    $outputResponse['nh']['html'] = str_replace($replaceVars['search'], $replaceVars['replace'], $outputResponse['nh']['html']);

    $attrDimensions = array(
        'nh_bottom' => 'bottom',
        'nh_right' => 'right',
        'nh_height' => 'height',
        'nh_width' => 'width',
    );

    foreach ($attrDimensions as $attrDimension => $attrName){
        if (isset($theme) && $theme instanceof erLhAbstractModelWidgetTheme && isset($theme->bot_configuration_array[$attrDimension]) && is_numeric($theme->bot_configuration_array[$attrDimension])){
            $outputResponse['nh']['dimensions'][$attrName] = (int)$theme->bot_configuration_array[$attrDimension] . 'px';
        }
    }
}

if (!isset($outputResponse['chat_ui']['leaveamessage'])) {
    $outputResponse['chat_ui']['leaveamessage'] = (isset($startDataFields['force_leave_a_message']) && $startDataFields['force_leave_a_message'] == true) ? true : false;
}

$outputResponse['chat_ui']['mobile_popup'] = isset($startDataFields['mobile_popup']) && $startDataFields['mobile_popup'] == true;

if (isset($startDataFields['lazy_load']) && $startDataFields['lazy_load'] == true) {
    $outputResponse['ll'] = true;
}

$ts = time();

// Wrapper version
$outputResponse['wv'] = 208;

// React APP versions
$outputResponse['v'] = 277;

$outputResponse['hash'] = sha1(erLhcoreClassIPDetect::getIP() . $ts . erConfigClassLhConfig::getInstance()->getSetting( 'site', 'secrethash' ));
$outputResponse['hash_ts'] = $ts;

if (is_array($department) && !empty($department)) {
    $outputResponse['department'] = $departmentArgument;
}

$gaOptions = erLhcoreClassModelChatConfig::fetch('ga_options')->data_value;

if (isset($gaOptions['ga_enabled']) && $gaOptions['ga_enabled'] == true) {
    $optionEvents = array(
        'showWidget',
        'closeWidget',
        'openPopup',
        'endChat',
        'chatStarted',
        'offlineMessage',
        'showInvitation',
        'hideInvitation',
        'nhClicked',
        'nhClosed',
        'nhShow',
        'nhHide',
        'fullInvitation',
        'cancelInvitation',
        'readInvitation',
        'clickAction',
        'botTrigger',
    );

    $continueTrack = false;

    if ((isset($gaOptions['ga_all']) &&  $gaOptions['ga_all'] == true) || (isset($gaOptions['ga_dep']) && is_array($department) && count(array_intersect($department,$gaOptions['ga_dep'])) > 0)) {
        $continueTrack = true;
    }

    if (isset($dep_id) && $dep_id > 0) {
        $gaByDep = erLhcoreClassModelChatEventTrack::findOne(array('filter' => array('department_id' => $dep_id)));
        if ($gaByDep instanceof erLhcoreClassModelChatEventTrack) {
            $gaOptions = $gaByDep->data_array;
            $continueTrack = true;
        }
    }

    if ($continueTrack == true) {
        foreach ($optionEvents as $optionEvent) {
            if (isset($gaOptions[$optionEvent .'_on']) && $gaOptions[$optionEvent .'_on'] == 1) {
                $outputResponse['ga']['events'][] = array(
                    'ev' => $optionEvent,
                    'ec' => $gaOptions[$optionEvent .'_category'],
                    'ea' => $gaOptions[$optionEvent .'_action'],
                    'el' => (isset($gaOptions[$optionEvent .'_label']) ? $gaOptions[$optionEvent .'_label'] : ''),
                );
            }
        }

        $outputResponse['ga']['events'][] = array(
            'ev' => 'trackingEvent',
            'ec' => 'Widget',
            'ea' => 'Click',
            'el' => '',
        );

        $outputResponse['ga']['js'] = $gaOptions['ga_js'];
    }
}

$host = erLhcoreClassSystem::getHost();

$outputResponse['static'] = array(
    'screenshot' =>  $host . erLhcoreClassDesign::design('js/html2canvas.min.js'). '?v=' . $outputResponse['v'],
    'app' => $host . ((isset($_GET['ie']) && $_GET['ie'] == 'true') ? erLhcoreClassDesign::design('js/widgetv2/react.app.ie.js') . '?v=' . $outputResponse['v'] : erLhcoreClassDesign::design('js/widgetv2/react.app.js') . '?v=' . $outputResponse['v']),
    'vendor' => $host . ((isset($_GET['ie']) && $_GET['ie'] == 'true') ? erLhcoreClassDesign::design('js/widgetv2/vendor.ie.js') . '?v=a3' : erLhcoreClassDesign::design('js/widgetv2/vendor.js') . '?v=a3'),
    'widget_css' => $host . (erConfigClassLhConfig::getInstance()->getDirLanguage('dir_language') == 'ltr' ? erLhcoreClassDesign::designCSS('css/widgetv2/bootstrap.min.css;css/widgetv2/widget.css;css/widgetv2/widget_override.css') : erLhcoreClassDesign::designCSS('css/widgetv2/bootstrap.min.rtl.css;css/widgetv2/widget.css;css/widgetv2/widget_rtl.css;css/widgetv2/widget_override_rtl.css')),
    'dir' => erConfigClassLhConfig::getInstance()->getDirLanguage('dir_language'),
    'cl' => erConfigClassLhConfig::getInstance()->getDirLanguage('content_language'),
    'widget_mobile_css' => $host . erLhcoreClassDesign::designCSS('css/widgetv2/widget_mobile.css;css/widgetv2/widget_mobile_override.css'),
    'embed_css' => $host . erLhcoreClassDesign::designCSS('css/widgetv2/embed.css;css/widgetv2/embed_override.css'),
    'status_css' => $host . erLhcoreClassDesign::designCSS('css/widgetv2/status.css;css/widgetv2/status_override.css'),
    'font_status' => $host . erLhcoreClassDesign::design('fonts/MaterialIcons-lhc-v5.woff2'),
    'chunk_js' => $host . erLhcoreClassDesign::design('js/widgetv2'),
    'page_css' => $pageCSS,
    'ex_js' => [],
    'ex_cb_js' => []
);

$outputResponse['chunks_location'] = $host . erLhcoreClassDesign::design('js/widgetv2');
$outputResponse['domain_lhc'] = str_replace(['http://','https://'],'',$host);

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('widgetrestapi.settings', array('output' => & $outputResponse));

erLhcoreClassRestAPIHandler::outputResponse($outputResponse);
exit();