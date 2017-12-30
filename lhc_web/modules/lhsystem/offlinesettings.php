<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhsystem/offlinesettings.tpl.php');

$offlineData = erLhcoreClassModelChatConfig::fetch('offline_settings');
$data = (array)$offlineData->data;

if ( isset($_POST['saveSettings']) ) {

    $definition = array(
        'doNotsaveOffline' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        ),
        'closeOffline' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        )
    );

    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        erLhcoreClassModule::redirect('system/offlinesettings');
        exit;
    }

    $Errors = array();

    $form = new ezcInputForm( INPUT_POST, $definition );
    $Errors = array();

    if ( $form->hasValidData( 'doNotsaveOffline' ) && $form->doNotsaveOffline == true) {
        $data['do_not_save_offline'] = 1;
    } else {
        $data['do_not_save_offline'] = 0;
    }

    if ( $form->hasValidData( 'closeOffline' ) && $form->closeOffline == true) {
        $data['close_offline'] = 1;
    } else {
        $data['close_offline'] = 0;
    }

    $offlineData->explain = '';
    $offlineData->type = 0;
    $offlineData->identifier = 'offline_settings';
    $offlineData->value = serialize($data);
    $offlineData->saveThis();

    $tpl->set('updated','done');
}

$tpl->set('settings',$data);

$Result['content'] = $tpl->fetch();
$Result['path'] = array(array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','System configuration')))

?>