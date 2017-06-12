<?php

header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');
header('Content-type: text/javascript');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT' );
header('Last-Modified: ' . gmdate( 'D, d M Y H:i:s',time()+60*60*8 ) . ' GMT' );
header('Cache-Control: no-store, no-cache, must-revalidate' );
header('Cache-Control: post-check=0, pre-check=0', false );
header('Pragma: no-cache' );

if ($Params['user_parameters_unordered']['wopen'] != 1 || ($Params['user_parameters_unordered']['isproactive'] == 1 && $Params['user_parameters_unordered']['wopen'] == 1))
{
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
    $tpl->set('hide_offline',$Params['user_parameters_unordered']['hide_offline'] == 'true' ? true : false);
    $tpl->set('isproactive',$Params['user_parameters_unordered']['isproactive'] == 1 ? true : false);
    
    echo $tpl->fetch();
}

if (erLhcoreClassModelChatConfig::fetch('track_is_online')->current_value && $Params['user_parameters_unordered']['dot'] != 'true') {
	$ignorable_ip = erLhcoreClassModelChatConfig::fetch('ignorable_ip')->current_value;
	if ( $ignorable_ip == '' || !erLhcoreClassIPDetect::isIgnored(erLhcoreClassIPDetect::getIP(),explode(',',$ignorable_ip))) {
		if ((string)$Params['user_parameters_unordered']['vid'] != '') {
			$db = ezcDbInstance::get();				

			$resetActivity = ", operation = '', operation_chat = ''";

			// wopen do not execute any commands while widget is open
			if ($Params['user_parameters_unordered']['wopen'] != 1)
			{
    			/**
    			 * Perhaps there is some pending operations for online visitor
    			 * */
    			$stmt = $db->prepare('SELECT operation FROM lh_chat_online_user WHERE vid = :vid');			
    			$stmt->bindValue(':vid',(string)$Params['user_parameters_unordered']['vid']);
    		    $stmt->execute();
    			$operation = $stmt->fetch(PDO::FETCH_COLUMN);	
    			$resetActivity = '';
			}
			
			$stmt = $db->prepare("UPDATE lh_chat_online_user SET last_check_time = :time{$resetActivity}, user_active = :user_active WHERE vid = :vid");
			$stmt->bindValue(':time',time(),PDO::PARAM_INT);
			$stmt->bindValue(':vid',(string)$Params['user_parameters_unordered']['vid']);
			$stmt->bindValue(':user_active',(int)$Params['user_parameters_unordered']['uactiv'],PDO::PARAM_INT);
			$stmt->execute();
			
			// If nodejs is used we have to inform back office operators about changed statuses
			if ((string)$Params['user_parameters_unordered']['uaction'] == 1) {
			    if (strpos((string)$Params['user_parameters_unordered']['hash'], '_') !== false) {
			        list($chatId) = explode('_', (string)$Params['user_parameters_unordered']['hash']);
			    } elseif (strpos((string)$Params['user_parameters_unordered']['hash_resume'], '_') !== false) {
			        list($chatId) = explode('_', (string)$Params['user_parameters_unordered']['hash_resume']);
			    }
			    
			    if (isset($chatId) && is_numeric($chatId)) {
			         erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.data_changed_chat',array('chat_id' => $chatId));
			    }
			}
		}
	}
}

exit;
?>