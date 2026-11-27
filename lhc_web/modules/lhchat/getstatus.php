<?php

header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');
header('Content-type: text/javascript');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT' );
header('Last-Modified: ' . gmdate( 'D, d M Y H:i:s',time()+60*60*8 ) . ' GMT' );
header('Cache-Control: no-store, no-cache, must-revalidate' );
header('Cache-Control: post-check=0, pre-check=0', false );
header('Pragma: no-cache' );

// This script is included with a <script> tag from the customer site, and such
// requests never send an Origin header. Validation is therefore only possible
// when the browser does provide one.
if (isset($_SERVER['HTTP_ORIGIN']) && !empty($_SERVER['HTTP_ORIGIN'])) {
    $validDomains = (string)erLhcoreClassModelChatConfig::fetch('valid_domains')->current_value;
    if (trim($validDomains) !== '' && !erLhcoreClassChatValidator::isAllowedWidgetOrigin($_SERVER['HTTP_ORIGIN'], $validDomains)) {
        exit;
    }
}

if (erLhcoreClassModelChatConfig::fetch('hide_disabled_department')->current_value == 1 && is_array($Params['user_parameters_unordered']['department'])){
	try {
        $parametersDepartment = erLhcoreClassChat::extractDepartment($Params['user_parameters_unordered']['department']);
        $Params['user_parameters_unordered']['department'] = $parametersDepartment['system'];
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

$theme = false;
if (isset($Params['user_parameters_unordered']['theme']) && ($themeId = erLhcoreClassChat::extractTheme($Params['user_parameters_unordered']['theme'])) !== false) {
	try {
		$theme = erLhAbstractModelWidgetTheme::fetch($themeId);
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
