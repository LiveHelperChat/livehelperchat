<?php

$hash = (string)$Params['user_parameters']['hash'];
$format = (string)$Params['user_parameters_unordered']['format'] == 'xml' ? 'xml' : 'json';

$hashSecret = erLhcoreClassModelChatConfig::fetch('export_hash')->current_value;

try {
	if ( sha1('getchat'.$hashSecret) == $hash ) {

		$chat = erLhcoreClassModelChat::fetch((string)$Params['user_parameters']['chat_id']);

		if ($format =='json') {
				header('Content-type: application/json');
				echo erLhcoreClassChatExport::chatExportJSON($chat);
			exit;
		} else {
				header('Content-type: text/xml');
				echo erLhcoreClassChatExport::chatExportXML($chat);				
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