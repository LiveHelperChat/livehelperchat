<?php

if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
	die('Invalid CSFR Token');
	exit;
}

$faq = erLhcoreClassFaq::getSession()->load( 'erLhcoreClassModelFaq', $Params['user_parameters']['id']);
erLhcoreClassFaq::getSession()->delete($faq);

erLhcoreClassModule::redirect('faq/list');
exit;
?>