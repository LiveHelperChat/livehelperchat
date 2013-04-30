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
			)
	);

	$Errors = array();

	$form = new ezcInputForm( INPUT_POST, $definition );
	$Errors = array();

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

	$chatboxData->value = serialize($data);

	$chatboxData->saveThis();

	$tpl->set('updated','done');
}

$tpl->set('chatbox_data',$data);

$Result['content'] = $tpl->fetch();
$Result['path'] = array(
		array('url' => erLhcoreClassDesign::baseurl('chatbox/configuration'), 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Chatbox configuration')),
		array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Chatbox configuration')));


?>