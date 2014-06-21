<?php

header('P3P: CP="NOI ADM DEV COM NAV OUR STP"');
header('Content-type: text/javascript');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT' );
header('Last-Modified: ' . gmdate( 'D, d M Y H:i:s',time()+60*60*8 ) . ' GMT' );
header('Cache-Control: no-store, no-cache, must-revalidate' );
header('Cache-Control: post-check=0, pre-check=0', false );
header('Pragma: no-cache' );

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

$tpl = erLhcoreClassTemplate::getInstance('lhchatbox/getstatus.tpl.php');
$tpl->set('position',$Params['user_parameters_unordered']['position']);
$tpl->set('top_pos',(!is_null($Params['user_parameters_unordered']['top']) && (int)$Params['user_parameters_unordered']['top'] >= 0) ? (int)$Params['user_parameters_unordered']['top'] : 400);
$tpl->set('units',key_exists((string)$Params['user_parameters_unordered']['units'], $validUnits) ? $validUnits[(string)$Params['user_parameters_unordered']['units']] : 'px');
$tpl->set('widthwidget',(!is_null($Params['user_parameters_unordered']['width']) && (int)$Params['user_parameters_unordered']['width'] > 0) ? (int)$Params['user_parameters_unordered']['width'] : 300);
$tpl->set('heightwidget',(!is_null($Params['user_parameters_unordered']['height']) && (int)$Params['user_parameters_unordered']['height'] > 0) ? (int)$Params['user_parameters_unordered']['height'] : 300);
$tpl->set('heightchatcontent',(!is_null($Params['user_parameters_unordered']['chat_height']) && (int)$Params['user_parameters_unordered']['chat_height'] > 0) ? (int)$Params['user_parameters_unordered']['chat_height'] : 220);
$tpl->set('show_content',(!is_null($Params['user_parameters_unordered']['sc']) && (int)$Params['user_parameters_unordered']['sc'] == 'true') ? true : false);
$tpl->set('show_content_min',(!is_null($Params['user_parameters_unordered']['scm']) && (int)$Params['user_parameters_unordered']['scm']  == 'true') ? true : false);
$tpl->set('disable_min',(!is_null($Params['user_parameters_unordered']['dmn']) && (int)$Params['user_parameters_unordered']['dmn']  == 'true') ? true : false);
$tpl->set('noresponse',(string)$Params['user_parameters_unordered']['noresponse'] == 'true');
$tpl->set('theme',$theme);

echo $tpl->fetch();
exit;