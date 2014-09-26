<?php

$hash = (string)$Params['user_parameters']['hash'];
$format = (string)$Params['user_parameters_unordered']['format'] == 'xml' ? 'xml' : 'json';

$hashSecret = erLhcoreClassModelChatConfig::fetch('export_hash')->current_value;

try {
	if ( sha1('getcount'.$hashSecret) == $hash ) {

		$filter = array();

		if (is_array($Params['user_parameters_unordered']['status'])){
			foreach ($Params['user_parameters_unordered']['status'] as $status) {
				$filter['filterin']['status'][] = (int)$status;
			}
		}

		$totalChats = erLhcoreClassChat::getCount($filter);

		if ($format =='json') {
			header('Content-type: application/json');
			echo json_encode(array('count' => $totalChats));			
			exit;
		} else {
			header('Content-type: text/xml');
			echo '<?xml version="1.0" encoding="utf-8" ?>',"<lhc><count>{$totalChats}</count></lhc>";				  
			exit;
		}

	} else {
		throw new Exception('Invalid hash.');
	}

} catch (Exception $e) {

	if ($format =='json') {
		header('Content-type: application/json');
		echo json_encode(array('error' => $e->getMessage()));
		exit;
	} else {
		header('Content-type: text/xml');
		echo '<?xml version="1.0" encoding="utf-8" ?>',"<lhc><error><![CDATA[".htmlspecialchars($e->getMessage())."]]></error></lhc>";
		exit;
	}
}

exit;

?>