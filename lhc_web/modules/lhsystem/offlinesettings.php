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
        ),
        'do_not_send' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        ),
        'offline_message' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'subject_id' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 1)
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

    if ( $form->hasValidData( 'offline_message' )) {
        $data['offline_message'] = $form->offline_message;
    } else {
        $data['offline_message'] = '';
    }

    if ( $form->hasValidData( 'do_not_send' ) && $form->do_not_send == true) {
        $data['do_not_send'] = 1;
    } else {
        $data['do_not_send'] = 0;
    }

    if ( $form->hasValidData( 'closeOffline' ) && $form->closeOffline == true) {
        $data['close_offline'] = 1;
    } else {
        $data['close_offline'] = 0;
    }

    if ( $form->hasValidData( 'subject_id' ) && erLhAbstractModelSubject::getCount(array('filter' => array('id' => (int)$form->subject_id)) ) > 0) {
        $data['subject_id'] = (int)$form->subject_id;
    } else {
        $data['subject_id'] = 0;
    }

    $offlineData->explain = '';
    $offlineData->type = 0;
    $offlineData->hidden = 1;
    $offlineData->identifier = 'offline_settings';
    $offlineData->value = serialize($data);
    $offlineData->saveThis();

    $tpl->set('updated','done');
}

$tpl->set('settings',$data);

$Result['content'] = $tpl->fetch();
$Result['path'] = array(array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','System configuration')))

?>