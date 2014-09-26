<?php

if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
	die('Invalid CSFR Token');
	exit;
}

$chatbox = erLhcoreClassModelChatbox::fetch($Params['user_parameters']['id']);
$chatbox->removeThis();
erLhcoreClassModule::redirect('chatbox/list');
exit;

?>