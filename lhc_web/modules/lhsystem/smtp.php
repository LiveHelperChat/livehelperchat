<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhsystem/smtp.tpl.php');

$smtpData = erLhcoreClassModelChatConfig::fetch('smtp_data');
$data = (array)$smtpData->data;

if ( isset($_POST['StoreMailSettings']) || isset($_POST['StoreMailSettingsTest']) ) {
    $definition = array(
        'sender' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'validate_email'
        ),
        'default_from' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'validate_email'
        ),
        'default_from_name' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )       
    );
    
    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        erLhcoreClassModule::redirect('system/smtp');
        exit;
    }
    
    $Errors = array();
    
    $form = new ezcInputForm( INPUT_POST, $definition );
    $Errors = array();
    
    if ( $form->hasValidData( 'sender' )) {
        $data['sender'] = $form->sender;
    } else {
        $data['sender'] = '';
    }
    
    if ( $form->hasValidData( 'default_from' )) {
        $data['default_from'] = $form->default_from;
    } else {
        $data['default_from'] = '';
    }
    
    if ( $form->hasValidData( 'default_from_name' ) ) {
        $data['default_from_name'] = $form->default_from_name;
    } else {
        $data['default_from_name'] = '';
    }
        
    $smtpData->value = serialize($data);
    $smtpData->saveThis();

    if (isset($_POST['StoreMailSettingsTest'])){
        try {
            erLhcoreClassChatMail::sendTestMail($currentUser->getUserData());
        } catch (Exception $e) {
            $tpl->set('errors',array($e->getMessage()));
        }
    }
    
    $tpl->set('updated','done');
}

if ( isset($_POST['StoreSMTPSettings']) || isset($_POST['StoreSMTPSettingsTest']) ) {

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

	if (isset($_POST['StoreSMTPSettingsTest'])){
		try {
			erLhcoreClassChatMail::sendTestMail($currentUser->getUserData());
		} catch (Exception $e) {
			$tpl->set('errors',array($e->getMessage()));
		}
	}

	$tpl->set('updated','done');
}

$tpl->set('smtp_data',$data);

$Result['content'] = $tpl->fetch();
$Result['path'] = array(array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','System configuration')),
array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','Mail settings')));

?>