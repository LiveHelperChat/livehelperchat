<?php

header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');
header('Content-type: text/javascript');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT' );
header('Last-Modified: ' . gmdate( 'D, d M Y H:i:s',time()+60*60*8 ) . ' GMT' );
header('Cache-Control: no-store, no-cache, must-revalidate' );
header('Cache-Control: post-check=0, pre-check=0', false );
header('Pragma: no-cache' );

$tpl = erLhcoreClassTemplate::getInstance('lhchat/chatcheckstatus.tpl.php');

if (is_array($Params['user_parameters_unordered']['department'])){
	erLhcoreClassChat::validateFilterIn($Params['user_parameters_unordered']['department']);
	$tpl->set('department',implode('/', $Params['user_parameters_unordered']['department']));
	$tpl->set('department_array',$Params['user_parameters_unordered']['department']);
} else {
	$tpl->set('department',false);
	$tpl->set('department_array',false);
}

$tpl->set('status',$Params['user_parameters_unordered']['status'] == 'true' ? true : false);

if (erLhcoreClassModelChatConfig::fetch('track_is_online')->current_value) {
	$ignorable_ip = erLhcoreClassModelChatConfig::fetch('ignorable_ip')->current_value;
	if ( $ignorable_ip == '' || !erLhcoreClassIPDetect::isIgnored(erLhcoreClassIPDetect::getIP(),explode(',',$ignorable_ip))) {
		if ((string)$Params['user_parameters_unordered']['vid'] != '') {
			$db = ezcDbInstance::get();				

			/**
			 * Perhaps there is some pending operations for online visitor
			 * */
			$stmt = $db->prepare('SELECT operation FROM lh_chat_online_user WHERE vid = :vid');			
			$stmt->bindValue(':vid',(string)$Params['user_parameters_unordered']['vid']);
		    $stmt->execute();
			$operation = $stmt->fetch(PDO::FETCH_COLUMN);
			echo $operation;
			
			$stmt = $db->prepare("UPDATE lh_chat_online_user SET last_check_time = :time, operation = '', operation_chat = '' WHERE vid = :vid");
			$stmt->bindValue(':time',time(),PDO::PARAM_INT);
			$stmt->bindValue(':vid',(string)$Params['user_parameters_unordered']['vid']);
			$stmt->execute();
		}
	}
}

echo $tpl->fetch();

exit;
?>