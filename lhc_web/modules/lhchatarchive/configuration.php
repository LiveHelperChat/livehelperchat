<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhchatarchive/configuration.tpl.php');

$arOptions = erLhcoreClassModelChatConfig::fetch('archive_options');
$data = (array)$arOptions->data;

if ( isset($_POST['StoreOptions']) ) {

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
        'max_chats' => new ezcInputFormDefinitionElement(
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

    if ( $form->hasValidData( 'max_chats' )) {
        $data['max_chats'] = $form->max_chats ;
    } else {
        $data['max_chats'] = 0;
    }

    $arOptions->explain = '';
    $arOptions->type = 0;
    $arOptions->hidden = 1;
    $arOptions->identifier = 'archive_options';
    $arOptions->value = serialize($data);
    $arOptions->saveThis();

    $tpl->set('updated','done');
}

$tpl->set('ar_options',$data);

$Result['content'] = $tpl->fetch();

$Result['path'] = array(
    array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Archive configuration')),
    array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/archive','Chat archive')));

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chatarchive.archive_path',array('result' => & $Result));
?>