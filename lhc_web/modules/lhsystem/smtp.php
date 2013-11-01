<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhsystem/smtp.tpl.php');

$smtpData = erLhcoreClassModelChatConfig::fetch('smtp_data');
$data = (array)$smtpData->data;

if ( isset($_POST['StoreSMTPSettings']) ) {

	$definition = array(
			'host' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
			),
			'username' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
			),
			'password' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
			),
			'port' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
			),
			'use_smtp' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
			),
	);

	if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
		erLhcoreClassModule::redirect('system/smtp');
		exit;
	}

	$Errors = array();

	$form = new ezcInputForm( INPUT_POST, $definition );
	$Errors = array();

	if ( $form->hasValidData( 'host' )) {
		$data['host'] = $form->host;
	} else {
		$data['host'] = '';
	}

	if ( $form->hasValidData( 'port' )) {
		$data['port'] = $form->port;
	} else {
		$data['port'] = '';
	}

	if ( $form->hasValidData( 'use_smtp' ) && $form->use_smtp == true ) {
		$data['use_smtp'] = 1;
	} else {
		$data['use_smtp'] = 0;
	}

	if ( $form->hasValidData( 'username' ) ) {
		$data['username'] = $form->username;
	} else {
		$data['username'] = '';
	}

	if ( $form->hasValidData( 'password' ) ) {
		$data['password'] = $form->password;
	} else {
		$data['password'] = '';
	}

	$smtpData->value = serialize($data);
	$smtpData->saveThis();

	$tpl->set('updated','done');
}

$tpl->set('smtp_data',$data);

$Result['content'] = $tpl->fetch();
$Result['path'] = array(array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','System configuration')),
array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','SMTP settings')));

?>