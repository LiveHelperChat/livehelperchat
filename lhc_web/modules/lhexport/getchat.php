<?php

$hash = (string)$Params['user_parameters']['hash'];
$format = (string)$Params['user_parameters_unordered']['format'] == 'xml' ? 'xml' : 'json';

$hashSecret = erLhcoreClassModelChatConfig::fetch('export_hash')->current_value;

try {

    if (strlen($hashSecret) < 50) {
        throw new Exception('Export hash to short!');
    }

    // Export follows the same IP restrictions as a regular back office login
    $passwordData = (array)erLhcoreClassModelChatConfig::fetch('password_data')->data;

    if (isset($passwordData['allow_login_from_ip']) && $passwordData['allow_login_from_ip'] != '' && !erLhcoreClassIPDetect::isIgnored(erLhcoreClassIPDetect::getIP(), explode(',', $passwordData['allow_login_from_ip']))) {
        throw new Exception(erTranslationClassLhTranslation::getInstance()->getTranslation('lhexport/export','Access denied because of IP restrictions'));
    }

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