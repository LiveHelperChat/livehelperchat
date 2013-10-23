<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhfile/configuration.tpl.php');

$fileData = erLhcoreClassModelChatConfig::fetch('file_configuration');
$data = (array)$fileData->data;


if ( isset($_POST['StoreFileConfiguration']) ) {
	$definition = array(
			'AllowedFileTypes' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'string'
			),
			'AllowedFileTypesUser' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'string'
			),
			'MaximumFileSize' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'int'
			),
			'ActiveFileUploadUser' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
			),
			'ActiveFileUploadAdmin' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
			)
	);

	$Errors = array();

	$form = new ezcInputForm( INPUT_POST, $definition );
	$Errors = array();

	if ( $form->hasValidData( 'ActiveFileUploadUser' ) && $form->ActiveFileUploadUser == true ) {
		$data['active_user_upload'] = true;
	} else {
		$data['active_user_upload'] = false;
	}

	if ( $form->hasValidData( 'ActiveFileUploadAdmin' ) && $form->ActiveFileUploadAdmin == true ) {
		$data['active_admin_upload'] = true;
	} else {
		$data['active_admin_upload'] = false;
	}

	if ( $form->hasValidData( 'AllowedFileTypes' ) && $form->AllowedFileTypes != '' ) {
		$data['ft_op'] = $form->AllowedFileTypes;
	} else {
		$Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Please enter valid file type!');
	}

	if ( $form->hasValidData( 'AllowedFileTypesUser' ) && $form->AllowedFileTypesUser != '' ) {
		$data['ft_us'] = $form->AllowedFileTypesUser;
	} else {
		$Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Please enter valid file type!');
	}

	if ( $form->hasValidData( 'MaximumFileSize' ) ) {
		$data['fs_max'] = $form->MaximumFileSize;
	} else {
		$Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Please enter valid maximum file size!');
	}

	if (empty($Errors) ) {

		$fileData->value = serialize($data);
		$fileData->saveThis();

	  	$tpl->set('updated','done');
    }  else {
        $tpl->set('errors',$Errors);
    }

}

$tpl->set('file_data',$data);
$Result['content'] = $tpl->fetch();
$Result['path'] = array(array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','File configuration')))

?>