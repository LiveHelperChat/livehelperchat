<?php

$hash = (string)$Params['user_parameters']['hash'];
$format = (string)$Params['user_parameters_unordered']['format'] == 'xml' ? 'xml' : 'json';

$hashSecret = erLhcoreClassModelChatConfig::fetch('export_hash')->current_value;

try {
	if ( sha1('getchat'.$hashSecret) == $hash ) {

		$chat = erLhcoreClassModelChat::fetch((string)$Params['user_parameters']['chat_id']);

		if ($format =='json') {
				echo erLhcoreClassChatExport::chatExportJSON($chat);
				header('Content-type: application/json');
			exit;
		} else {
				echo erLhcoreClassChatExport::chatExportXML($chat);
				header('Content-type: text/xml');
			exit;
		}

	} else {
		throw new Exception('Invalid hash.');
	}

} catch (Exception $e) {

	if ($format =='json') {
		echo json_encode(array('error' => $e->getMessage()));
		header('Content-type: application/json');
		exit;
	} else {
		echo '<?xml version="1.0" encoding="utf-8" ?>',"<lhc><error><![CDATA[".htmlspecialchars($e->getMessage())."]]></error></lhc>";
		header('Content-type: text/xml');
		exit;
	}
}

exit;

?>