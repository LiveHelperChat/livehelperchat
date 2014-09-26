<?php

if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
	die('Invalid CSFR Token');
	exit;
}

$question = erLhcoreClassQuestionary::getSession()->load( 'erLhcoreClassModelQuestion', $Params['user_parameters']['id']);
$question->removeThis();

erLhcoreClassModule::redirect('questionary/list');
exit;
?>