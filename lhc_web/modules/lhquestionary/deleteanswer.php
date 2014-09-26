<?php

if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
	die('Invalid CSFR Token');
	exit;
}

$question = erLhcoreClassModelQuestionAnswer::fetch((int)$Params['user_parameters']['id']);
$question->removeThis();

erLhcoreClassModule::redirect('questionary/edit',"/{$question->question_id}/(tab)/answers");
exit;
?>