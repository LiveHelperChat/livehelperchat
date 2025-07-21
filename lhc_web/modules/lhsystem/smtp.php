<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhsystem/smtp.tpl.php');

$smtpData = erLhcoreClassModelChatConfig::fetch('smtp_data');
$data = (array)$smtpData->data;

$tab = 'mailsettings'; // Default tab

if ( isset($_POST['StoreMailSettings']) || isset($_POST['StoreMailSettingsTest']) || isset($_POST['StoreSMTPSettings']) || isset($_POST['StoreSMTPSettingsTest']) ) {
    
    // Determine which tab was used based on button clicked
    if (isset($_POST['StoreSMTPSettings']) || isset($_POST['StoreSMTPSettingsTest'])) {
        $tab = 'SMTP';
    } else {
        $tab = 'mailsettings';
    }
    
    $definition = array(
        'sender' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'validate_email'
        ),
        'default_from' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'validate_email'
        ),
        'default_from_name' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
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
        'bindip' => new ezcInputFormDefinitionElement(
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

    if ( $form->hasValidData( 'bindip' ) ) {
        $data['bindip'] = $form->bindip;
    } else {
        $data['bindip'] = '';
    }

    $smtpData->value = serialize($data);
    $smtpData->saveThis();

    if (isset($_POST['StoreMailSettingsTest']) || isset($_POST['StoreSMTPSettingsTest'])) {
        $output = erLhcoreClassChatMail::sendTestMail($currentUser->getUserData());
        if ($output['error'] !== false) {
            $tpl->set('errors',array($output['error']->getMessage()));
        }
    }
    
    $tpl->set('updated','done');

    if (isset($_POST['StoreMailSettingsTest']) || isset($_POST['StoreSMTPSettingsTest'])) {
        $tpl->set('content_connection', isset($output['debug']) ? $output['debug'] : '');
    }
}

$tpl->set('smtp_data',$data);
$tpl->set('tab', $tab);

$Result['content'] = $tpl->fetch();
$Result['path'] = array(array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','System configuration')),
array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','Mail settings')));

?>