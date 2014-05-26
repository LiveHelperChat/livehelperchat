<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhdocshare/configuration.tpl.php');

$docSharer = erLhcoreClassModelChatConfig::fetch('doc_sharer');
$data = (array)$docSharer->data;

if ( isset($_POST['StoreConfiguration']) ) {

    $definition = array(
        'LibreOfficePath' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'string'
        ),
        'SupportedExtensions' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'string'
        ),
        'BackgroundProcess' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        ),
        'MaxFileSize' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 2)
        )       
    );

    $Errors = array();

    $form = new ezcInputForm( INPUT_POST, $definition );
    $Errors = array();

    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
    	erLhcoreClassModule::redirect('docshare/configuration');
    	exit;
    }

    if ( $form->hasValidData( 'LibreOfficePath' ) ) {
    	$data['libre_office_path'] = $form->LibreOfficePath;
    } else {
    	$data['libre_office_path'] = '/usr/bin/libreoffice';
    }

    if ( $form->hasValidData( 'SupportedExtensions' ) ) {
    	$data['supported_extension'] = $form->SupportedExtensions;
    } else {
    	$data['supported_extension'] = 'ppt,pptx,doc,odp,epub,mobi,docx,xlsx,txt,xls,xlsx,pdf,rtf,odt';
    }

    if ( $form->hasValidData( 'BackgroundProcess' ) &&  $form->BackgroundProcess == true) {
    	$data['background_process'] = 1;
    } else {
    	$data['background_process'] = 0;
    }

    if ( $form->hasValidData( 'MaxFileSize' )) {
    	$data['max_file_size'] = $form->MaxFileSize;
    } else {
    	$data['max_file_size'] = 2;
    }

    if (count($Errors) == 0) {
        $docSharer->value = serialize($data);
        $docSharer->saveThis();
        $tpl->set('updated','done');
    }  else {
        $tpl->set('errors',$Errors);
    }
}

$tpl->set('docsharer_data',$data);

$Result['content'] = $tpl->fetch();
$Result['path'] = array(
array('url' => erLhcoreClassDesign::baseurl('docshare/index'), 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('docshare/index','Documents sharer')),
array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Documents sharer configuration')));

?>