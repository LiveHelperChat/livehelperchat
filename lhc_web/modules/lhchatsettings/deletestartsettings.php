<?php

if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
	die('Invalid CSFR Token');
	exit;
}

$item = erLhcoreClassModelChatStartSettings::fetch((int)$Params['user_parameters']['id']);
$item->removeThis();

erLhcoreClassModule::redirect('chatsettings/startsettingslist');
exit;

?>