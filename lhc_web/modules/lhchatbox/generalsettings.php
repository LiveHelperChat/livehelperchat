<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhchatbox/generalsettings.tpl.php');

$chatboxData = erLhcoreClassModelChatConfig::fetch('chatbox_data');
$data = (array)$chatboxData->data;

if ( isset($_POST['StoreChatboxSettings']) ) {

	$definition = array(
			'AutoCreation' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
			),
			'SecretHash' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'string'
			),
			'DefaultName' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
			),
			'DefaultOperatorName' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
			),
			'MessagesLimit' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'int'
			)
	);

	$Errors = array();

	$form = new ezcInputForm( INPUT_POST, $definition );
	$Errors = array();

	if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
		erLhcoreClassModule::redirect();
		exit;
	}

	if ( $form->hasValidData( 'AutoCreation' ) && $form->AutoCreation == true ) {
		$data['chatbox_auto_enabled'] = 1;
	} else {
		$data['chatbox_auto_enabled'] = 0;
	}

	if ( $form->hasValidData( 'SecretHash' ) ) {
		$data['chatbox_secret_hash'] = $form->SecretHash;
	} else {
		$data['chatbox_secret_hash'] = '';
	}

	if ( $form->hasValidData( 'DefaultName' ) ) {
		$data['chatbox_default_name'] = $form->DefaultName;
	} else {
		$data['chatbox_default_name'] = '';
	}

	if ( $form->hasValidData( 'DefaultOperatorName' ) ) {
		$data['chatbox_default_opname'] = $form->DefaultOperatorName;
	} else {
		$data['chatbox_default_opname'] = '';
	}

	if ( $form->hasValidData( 'MessagesLimit' ) ) {
		$data['chatbox_msg_limit'] = $form->MessagesLimit;
	} else {
		$data['chatbox_msg_limit'] = '';
	}

	$chatboxData->value = serialize($data);

	$chatboxData->saveThis();

	$tpl->set('updated','done');
}

$tpl->set('chatbox_data',$data);

$Result['content'] = $tpl->fetch();
$Result['path'] = array(
		array('url' =>erLhcoreClassDesign::baseurl('chatbox/configuration'), 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chatbox/list','Chatbox')),
		array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chatbox/generalsettings','Chatbox settings')));


?>