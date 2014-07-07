<?php

header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');
header('Content-type: text/javascript');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT' );
header('Last-Modified: ' . gmdate( 'D, d M Y H:i:s',time()+60*60*8 ) . ' GMT' );
header('Cache-Control: no-store, no-cache, must-revalidate' );
header('Cache-Control: post-check=0, pre-check=0', false );
header('Pragma: no-cache' );

if ((int)$Params['user_parameters_unordered']['department'] > 0 && erLhcoreClassModelChatConfig::fetch('hide_disabled_department')->current_value == 1){
	try {
		$department = erLhcoreClassModelDepartament::fetch((int)$Params['user_parameters_unordered']['department']);
		if ($department->disabled == 1) {
			// Hide disabled department
			exit;
		}
	} catch (Exception $e) {
		exit;
	}
}

$tpl = erLhcoreClassTemplate::getInstance('lhchat/getstatusembed.tpl.php');
$tpl->set('leaveamessage',(string)$Params['user_parameters_unordered']['leaveamessage'] == 'true');
$tpl->set('hide_offline',$Params['user_parameters_unordered']['hide_offline']);
$tpl->set('department',(int)$Params['user_parameters_unordered']['department'] > 0 ? (int)$Params['user_parameters_unordered']['department'] : false);
$tpl->set('priority',is_numeric($Params['user_parameters_unordered']['priority']) ? (int)$Params['user_parameters_unordered']['priority'] : false);
$tpl->set('theme',is_numeric($Params['user_parameters_unordered']['theme']) ? (int)$Params['user_parameters_unordered']['theme'] : false);
$tpl->set('operator',is_numeric($Params['user_parameters_unordered']['operator']) ? (int)$Params['user_parameters_unordered']['operator'] : false);

echo $tpl->fetch();
exit;