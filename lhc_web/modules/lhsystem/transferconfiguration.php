<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhsystem/transferconfiguration.tpl.php');

$transferData = erLhcoreClassModelChatConfig::fetch('transfer_configuration');

$data = (array)$transferData->data;

if ( ezcInputForm::hasPostData() ) {
    $definition = array(
        'change_department' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        ),
        'make_pending' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        ),
        'make_unassigned' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        )
    );

    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        erLhcoreClassModule::redirect('system/smtp');
        exit;
    }

    $form = new ezcInputForm( INPUT_POST, $definition );

    if ( $form->hasValidData( 'change_department' ) && $form->change_department == true) {
        $data['change_department'] = 1;
    } else {
        $data['change_department'] = 0;
    }

    if ( $form->hasValidData( 'make_pending' ) && $form->make_pending == true) {
        $data['make_pending'] = 1;
    } else {
        $data['make_pending'] = 0;
    }

    if ( $form->hasValidData( 'make_unassigned' ) && $form->make_unassigned == true) {
        $data['make_unassigned'] = 1;
    } else {
        $data['make_unassigned'] = 0;
    }

    $transferData->value = serialize($data);
    $transferData->saveThis();

    $tpl->set('updated','done');
}

$tpl->set('transfer_data', $data);

$Result['content'] = $tpl->fetch();
$Result['path'] = array(array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','System configuration')),
    array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','Transfer configuration')));
