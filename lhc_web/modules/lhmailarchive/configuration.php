<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhmailarchive/configuration.tpl.php');

$arOptions = erLhcoreClassModelChatConfig::fetch('mail_archive_options');
$data = (array)$arOptions->data;

if ( isset($_POST['StoreOptions']) ) {

    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        erLhcoreClassModule::redirect('mailarchive/configuration');
        exit;
    }

    $definition = array(
        'automatic_archiving' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        ),
        'older_than' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int'
        ),
        'archive_strategy' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int'
        ),
        'max_mails' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int'
        )
    );

    $form = new ezcInputForm( INPUT_POST, $definition );
    $Errors = array();

    if ( $form->hasValidData( 'automatic_archiving' ) && $form->automatic_archiving == true ) {
        $data['automatic_archiving'] = 1;
    } else {
        $data['automatic_archiving'] = 0;
    }

    if ( $form->hasValidData( 'older_than' )) {
        $data['older_than'] = $form->older_than;
    } else {
        $data['older_than'] = 0;
    }

    if ( $form->hasValidData( 'archive_strategy' )) {
        $data['archive_strategy'] = $form->archive_strategy ;
    } else {
        $data['archive_strategy'] = 0;
    }

    if ( $form->hasValidData( 'max_mails' )) {
        $data['max_mails'] = $form->max_mails ;
    } else {
        $data['max_mails'] = 0;
    }

    $arOptions->explain = '';
    $arOptions->type = 0;
    $arOptions->hidden = 1;
    $arOptions->identifier = 'mail_archive_options';
    $arOptions->value = serialize($data);
    $arOptions->saveThis();

    $tpl->set('updated','done');
}

$tpl->set('ar_options',$data);

$Result['content'] = $tpl->fetch();

$Result['path'] = array(
    array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Mail archive configuration')),
    array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/archive','Mail archive')));

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('mailarchive.archive_path',array('result' => & $Result));
?>