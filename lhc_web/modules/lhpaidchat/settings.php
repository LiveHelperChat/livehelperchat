<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhpaidchat/settings.tpl.php');

$paidchatData = erLhcoreClassModelChatConfig::fetch('paidchat_data');
$data = (array)$paidchatData->data;

if ( isset($_POST['StoreChatboxSettings']) ) {

	$definition = array(			
			'SecretHash' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'string'
			),	
    	    'PaidEnabled' => new ezcInputFormDefinitionElement(
    	        ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
    	    ),
    	    'ClosedReadDenied' => new ezcInputFormDefinitionElement(
    	        ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
    	    )
	);

	$form = new ezcInputForm( INPUT_POST, $definition );
	$Errors = array();

	if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
		erLhcoreClassModule::redirect();
		exit;
	}

	if ( $form->hasValidData( 'PaidEnabled' ) && $form->PaidEnabled == true ) {
	    $data['paidchat_enabled'] = 1;
	} else {
	    $data['paidchat_enabled'] = 0;
	}

	if ( $form->hasValidData( 'ClosedReadDenied' ) && $form->ClosedReadDenied == true ) {
	    $data['paidchat_read_denied'] = 1;
	} else {
	    $data['paidchat_read_denied'] = 0;
	}

	if ( $form->hasValidData( 'SecretHash' ) ) {
		$data['paidchat_secret_hash'] = $form->SecretHash;
	} else {
		$data['paidchat_secret_hash'] = '';
	}

	$paidchatData->value = serialize($data);
	$paidchatData->saveThis();

	$tpl->set('updated','done');
}

$tpl->set('paidchat_data',$data);

$Result['content'] = $tpl->fetch();
$Result['path'] = array(	
        array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','System configuration')),
		array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chatbox/generalsettings','Paid chat settings')));

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('paidchat.settings_path',array('result' => & $Result));

?>