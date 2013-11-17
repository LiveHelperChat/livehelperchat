<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhxmp/xmp.tpl.php');

$xmpData = erLhcoreClassModelChatConfig::fetch('xmp_data');
$data = (array)$xmpData->data;

if ( isset($_POST['StoreXMPSettings']) || isset($_POST['StoreXMPSettingsTest']) ) {

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
			'use_xmp' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
			),
			'resource' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
			),
			'server' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
			),
			'recipients' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
			),
			'XMPMessage' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
			),
	);

	if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
		erLhcoreClassModule::redirect('xmp/configuration');
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

	if ( $form->hasValidData( 'server' )) {
		$data['server'] = $form->server;
	} else {
		$data['server'] = '';
	}

	if ( $form->hasValidData( 'resource' )) {
		$data['resource'] = $form->resource;
	} else {
		$data['resource'] = '';
	}

	if ( $form->hasValidData( 'recipients' )) {
		$data['recipients'] = $form->recipients;
	} else {
		$data['recipients'] = '';
	}

	if ( $form->hasValidData( 'port' )) {
		$data['port'] = $form->port;
	} else {
		$data['port'] = '';
	}

	if ( $form->hasValidData( 'use_xmp' ) && $form->use_xmp == true ) {
		$data['use_xmp'] = 1;
	} else {
		$data['use_xmp'] = 0;
	}

	if ( $form->hasValidData( 'username' ) ) {
		$data['username'] = $form->username;
	} else {
		$data['username'] = '';
	}

	if ( $form->hasValidData( 'password' ) && $form->password != '' ) {
		$data['password'] = $form->password;
	}

	if ( $form->hasValidData( 'XMPMessage' ) ) {
		$data['xmp_message'] = $form->XMPMessage;
	} else {
		$data['xmp_message'] = '';
	}
	
	$xmpData->value = serialize($data);
	$xmpData->saveThis();

	if (isset($_POST['StoreXMPSettingsTest'])){
		try {
			erLhcoreClassXMP::sendTestXMP($currentUser->getUserData());
			$tpl->set('message_send','done');
		} catch (Exception $e) {
			$tpl->set('errors',array($e->getMessage()));
		}
	}

	$tpl->set('updated','done');
}

$tpl->set('xmp_data',$data);

$Result['content'] = $tpl->fetch();
$Result['path'] = array(array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','System configuration')),
array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/xmp','XMPP settings')));

?>