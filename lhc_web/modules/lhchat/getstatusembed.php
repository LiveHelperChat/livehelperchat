<?php

header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');
header('Content-type: text/javascript');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT' );
header('Last-Modified: ' . gmdate( 'D, d M Y H:i:s',time()+60*60*8 ) . ' GMT' );
header('Cache-Control: no-store, no-cache, must-revalidate' );
header('Cache-Control: post-check=0, pre-check=0', false );
header('Pragma: no-cache' );

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


$tpl = erLhcoreClassTemplate::getInstance('lhchat/getstatusembed.tpl.php');
$tpl->set('leaveamessage',(string)$Params['user_parameters_unordered']['leaveamessage'] == 'true');
$tpl->set('hide_offline',$Params['user_parameters_unordered']['hide_offline']);

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

$tpl->set('priority',is_numeric($Params['user_parameters_unordered']['priority']) ? (int)$Params['user_parameters_unordered']['priority'] : false);
$tpl->set('theme',is_numeric($Params['user_parameters_unordered']['theme']) ? (int)$Params['user_parameters_unordered']['theme'] : false);
$tpl->set('operator',is_numeric($Params['user_parameters_unordered']['operator']) ? (int)$Params['user_parameters_unordered']['operator'] : false);

echo $tpl->fetch();
exit;