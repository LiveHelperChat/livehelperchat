<?php

header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');
header('Content-type: text/javascript');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT' );
header('Last-Modified: ' . gmdate( 'D, d M Y H:i:s',time()+60*60*8 ) . ' GMT' );
header('Cache-Control: no-store, no-cache, must-revalidate' );
header('Cache-Control: post-check=0, pre-check=0', false );
header('Pragma: no-cache' );

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
            exit;
        }
    }
}

if (erLhcoreClassModelChatConfig::fetch('hide_disabled_department')->current_value == 1 && is_array($Params['user_parameters_unordered']['department'])){
	try {
		erLhcoreClassChat::validateFilterIn($Params['user_parameters_unordered']['department']);
				
		$departments = erLhcoreClassModelDepartament::getList(array('filterin' => array('id' => $Params['user_parameters_unordered']['department'])));
		
		foreach ($departments as $department){
			if ($department->disabled == 1) {
				// Hide disabled department
				exit;
			}	
		}
		
	} catch (Exception $e) {
		exit;
	}
}

$tpl = erLhcoreClassTemplate::getInstance('lhchat/getstatus.tpl.php');

if ( erLhcoreClassModelChatConfig::fetch('track_online_visitors')->current_value == 1 ) {
    $str = substr(sha1(mt_rand() . microtime()),0,20);
	$tpl->set('vid', $str);
}

$validUnits = array('pixels' => 'px','percents' => '%');

$theme = false;
if (isset($Params['user_parameters_unordered']['theme']) && (int)$Params['user_parameters_unordered']['theme'] > 0){
	try {
		$theme = erLhAbstractModelWidgetTheme::fetch($Params['user_parameters_unordered']['theme']);	
	} catch (Exception $e) {
		$theme = false;
	}
} else {
	$defaultTheme = erLhcoreClassModelChatConfig::fetch('default_theme_id')->current_value;
	if ($defaultTheme > 0) {
		try {
			$theme = erLhAbstractModelWidgetTheme::fetch($defaultTheme);
		} catch (Exception $e) {
			$theme = false;
		}
	}
}

if ($theme instanceof erLhAbstractModelWidgetTheme && isset($theme->bot_configuration_array['detect_language']) && $theme->bot_configuration_array['detect_language'] == true) {
    erLhcoreClassChatValidator::setLanguageByBrowser();
}

if ($theme instanceof erLhAbstractModelWidgetTheme && isset($theme->bot_configuration_array['load_w2']) && $theme->bot_configuration_array['load_w2'] == true) {
    $tpl = erLhcoreClassTemplate::getInstance('lhchat/getstatus/getstatus_migrate.tpl.php');
    $tpl->set('uparams',$Params['user_parameters_unordered']);
    $tpl->set('depId',$Params['user_parameters_unordered']['department']);
    $tpl->set('mode','widget');
    $tpl->set('position',$Params['user_parameters_unordered']['position']);
    $tpl->set('click',$Params['user_parameters_unordered']['click']);
    $tpl->set('leaveamessage',$Params['user_parameters_unordered']['leaveamessage'] == 'true');
    $tpl->set('disable_pro_active',$Params['user_parameters_unordered']['disable_pro_active'] == 'true');
    echo $tpl->fetch();
    exit;
}

$tpl->set('referrer',isset($_GET['r']) ? rawurldecode($_GET['r']) : '');
$tpl->set('track_online_users',erLhcoreClassModelChatConfig::fetch('track_online_visitors')->current_value == 1 && (string)$Params['user_parameters_unordered']['dot'] != 'true');
$tpl->set('disable_online_tracking',(string)$Params['user_parameters_unordered']['dot'] == 'true');
$tpl->set('click',$Params['user_parameters_unordered']['click']);
$tpl->set('position',$Params['user_parameters_unordered']['position']);
$tpl->set('minimize_action',$Params['user_parameters_unordered']['ma']);
$tpl->set('identifier',(!is_null($Params['user_parameters_unordered']['identifier']) && !empty($Params['user_parameters_unordered']['identifier'])) ? (string)$Params['user_parameters_unordered']['identifier'] : false);
$tpl->set('leaveamessage',(string)$Params['user_parameters_unordered']['leaveamessage'] == 'true');
$tpl->set('hide_offline',$Params['user_parameters_unordered']['hide_offline']);
$tpl->set('bot_id',is_numeric($Params['user_parameters_unordered']['bot_id']) ? $Params['user_parameters_unordered']['bot_id'] : null);
$tpl->set('fresh',is_numeric($Params['user_parameters_unordered']['fresh']) ? true : false);

if (is_array($Params['user_parameters_unordered']['department'])){
	erLhcoreClassChat::validateFilterIn($Params['user_parameters_unordered']['department']);
	$tpl->set('department',implode('/', $Params['user_parameters_unordered']['department']));
	$tpl->set('department_array',$Params['user_parameters_unordered']['department']);	
} else {
	$tpl->set('department',false);
	$tpl->set('department_array',false);
}

// Pass user arguments
if (is_array($Params['user_parameters_unordered']['ua'])){
	$tpl->set('uarguments',implode('/', $Params['user_parameters_unordered']['ua']));
} else {
	$tpl->set('uarguments',false);
}

$tpl->set('check_operator_messages',true /*$Params['user_parameters_unordered']['check_operator_messages']*/);
$tpl->set('top_pos',(!is_null($Params['user_parameters_unordered']['top']) && (int)$Params['user_parameters_unordered']['top'] >= 0) ? (int)$Params['user_parameters_unordered']['top'] : 350);
$tpl->set('units',key_exists((string)$Params['user_parameters_unordered']['units'], $validUnits) ? $validUnits[(string)$Params['user_parameters_unordered']['units']] : 'px');
$tpl->set('disable_pro_active',(string)$Params['user_parameters_unordered']['disable_pro_active'] == 'true' || (string)$Params['user_parameters_unordered']['dot'] == 'true');
$tpl->set('priority',is_numeric($Params['user_parameters_unordered']['priority']) ? (int)$Params['user_parameters_unordered']['priority'] : false);
$tpl->set('theme',$theme);
$tpl->set('operator',is_numeric($Params['user_parameters_unordered']['operator']) ? (int)$Params['user_parameters_unordered']['operator'] : false);
$tpl->set('survey',is_numeric($Params['user_parameters_unordered']['survey']) ? (int)$Params['user_parameters_unordered']['survey'] : false);

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.getstatus',array('tpl' => & $tpl, 'theme' => $theme, 'validUnits' => $validUnits));

echo $tpl->fetch();
exit;