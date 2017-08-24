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
            'ClamAVSocketPath' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'string'
			),
			'ClamAVSocketLength' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'int'
			),
            'MaximumFileSize' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'int'
			),
			'ActiveFileUploadUser' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
			),
			'ActiveFileUploadAdmin' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
			),
            'AntivirusFileScanEnabled' => new ezcInputFormDefinitionElement(
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

	if ( $form->hasValidData( 'AntivirusFileScanEnabled' ) && $form->AntivirusFileScanEnabled == true ) {
		$data['clamav_enabled'] = true;
	} else {
		$data['clamav_enabled'] = false;
	}

	if ( $form->hasValidData( 'AllowedFileTypes' ) && $form->AllowedFileTypes != '' ) {
		$data['ft_op'] = $form->AllowedFileTypes;
	} else {
		$Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('file/configurations','Please enter valid file type!');
	}

	if ( $form->hasValidData( 'AllowedFileTypesUser' ) && $form->AllowedFileTypesUser != '' ) {
		$data['ft_us'] = $form->AllowedFileTypesUser;
	} else {
		$Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('file/configuration','Please enter valid file type!');
	}

	if ( $form->hasValidData( 'MaximumFileSize' ) ) {
		$data['fs_max'] = $form->MaximumFileSize;
	} else {
		$Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('file/configuration','Please enter valid maximum file size!');
	}

	if ( $form->hasValidData( 'ClamAVSocketPath' ) ) {
		$data['clamd_sock'] = $form->ClamAVSocketPath;
	} else {
		$Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('file/configuration','Please enter valid maximum file size!');
	}

	if ( $form->hasValidData( 'ClamAVSocketLength' ) ) {
		$data['clamd_sock_len'] = $form->ClamAVSocketLength;
	} else {
		$Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('file/configuration','Please enter valid maximum file size!');
	}

	if (empty($Errors) ) {

		$fileData->value = serialize($data);
		$fileData->saveThis();

        // Cleanup cache to recompile templates etc.
        $CacheManager = erConfigClassLhCacheConfig::getInstance();
        $CacheManager->expireCache();

	  	$tpl->set('updated','done');
    }  else {
        $tpl->set('errors',$Errors);
    }

}

$tpl->set('file_data',$data);
$Result['content'] = $tpl->fetch();
$Result['path'] = array(
		array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','System configuration')),
		array('url' => erLhcoreClassDesign::baseurl('file/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','File configuration')));

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('file.configuration_path', array('result' => & $Result));

?>