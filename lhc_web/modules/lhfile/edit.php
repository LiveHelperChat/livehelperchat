<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhfile/edit.tpl.php');

$file = erLhcoreClassModelChatFile::fetch((int)$Params['user_parameters']['file_id']);

if (ezcInputForm::hasPostData()) {
    $definition = array(
        'persistent' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        ),
    );

    $form = new ezcInputForm( INPUT_POST, $definition );
    $Errors = array();

    if ( $form->hasValidData( 'persistent' ) && $form->persistent == true)	{
        $file->persistent = 1;
    } else {
        $file->persistent = 0;
    }

    $tpl->set('file_uploaded',true);
    
    $file->saveThis();
}


$tpl->set('item', $file);

$Result['content'] = $tpl->fetch();

$Result['path'] = array(
    array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','System configuration')),
    array('url' => erLhcoreClassDesign::baseurl('file/list'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','List of files')),
    array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Edit file')));

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('file.edit_path', array('result' => & $Result));

?>