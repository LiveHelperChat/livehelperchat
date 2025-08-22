<?php

$tpl = erLhcoreClassTemplate::getInstance('lhfile/configuration.tpl.php');

$fileData = erLhcoreClassModelChatConfig::fetch('file_configuration');
$data = (array)$fileData->data;


if (isset($_POST['StoreFileConfiguration'])) {

    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        erLhcoreClassModule::redirect('file/configuration');
        exit;
    }

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
        'mail_img_verify_skip' => new ezcInputFormDefinitionElement(
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
        'AllowOnlyOneFileUpload' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        ),
        'ActiveFileUploadAdmin' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        ),
        'file_preview' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        ),
        'AntivirusFileScanEnabled' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        ),
        'typeDelete' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'string', null, FILTER_REQUIRE_ARRAY
        ),
        'typeChatDelete' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'string', null, FILTER_REQUIRE_ARRAY
        ),
        'mdays_older' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 1)
        ),
        'MaximumResolution' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 1)
        ),
        'MaximumResolutionMail' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 1)
        ),
        'mdays_older_visitor' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 1)
        ),
        'soundLength' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 1)
        ),
        'removeMetaTag' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        ),
        'soundMessages' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        ),
        'soundMessagesOp' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        ),
        'chat_file_policy_v' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0, 'max_range' => 2)
        ),
        'chat_file_policy_o' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0, 'max_range' => 1)
        ),
        'mail_file_policy' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0, 'max_range' => 1)
        ),
        'img_download_policy' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0, 'max_range' => 1)
        ),
        'mail_img_download_policy' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0, 'max_range' => 1)
        ),
        'img_verify_min_dim' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 10, 'max_range' => 10000)
        ),
        'mail_img_verify_min_dim' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 10, 'max_range' => 10000)
        ),
    );

    $Errors = array();

    $form = new ezcInputForm(INPUT_POST, $definition);
    $Errors = array();


    if ($form->hasValidData('typeDelete') && is_array($form->typeDelete)) {
        $data['mtype_delete'] = $form->typeDelete;
    } else {
        $data['mtype_delete'] = array();
    }

    if ($form->hasValidData('typeChatDelete') && is_array($form->typeChatDelete)) {
        $data['mtype_cdelete'] = $form->typeChatDelete;
    } else {
        $data['mtype_cdelete'] = array();
    }

    if ($form->hasValidData('mdays_older')) {
        $data['mdays_older'] = $form->mdays_older;
    } else {
        $data['mdays_older'] = null;
    }

    if ($form->hasValidData('img_verify_min_dim')) {
        $data['img_verify_min_dim'] = $form->img_verify_min_dim;
    } else {
        $data['img_verify_min_dim'] = 10;
    }

    if ($form->hasValidData('mail_img_verify_min_dim')) {
        $data['mail_img_verify_min_dim'] = $form->mail_img_verify_min_dim;
    } else {
        $data['mail_img_verify_min_dim'] = 10;
    }
    
    if ($form->hasValidData('mdays_older_visitor')) {
        $data['mdays_older_visitor'] = $form->mdays_older_visitor;
    } else {
        $data['mdays_older_visitor'] = null;
    }

    foreach (['chat_file_policy_v','chat_file_policy_o','mail_file_policy','img_download_policy','mail_img_download_policy'] as $policy) {
        if ($form->hasValidData($policy)) {
            $data[$policy] = $form->{$policy};
        } else {
            $data[$policy] = 0;
        }
    }

    if ($form->hasValidData('ActiveFileUploadUser') && $form->ActiveFileUploadUser == true) {
        $data['active_user_upload'] = true;
    } else {
        $data['active_user_upload'] = false;
    }

    if ($form->hasValidData('file_preview') && $form->file_preview == true) {
        $data['file_preview'] = true;
    } else {
        $data['file_preview'] = false;
    }

    if ($form->hasValidData('MaximumResolution')) {
        $data['max_res'] = $form->MaximumResolution;
    } else {
        $data['max_res'] = '';
    }

    if ($form->hasValidData('mail_img_verify_skip')) {
        $data['mail_img_verify_skip'] = $form->mail_img_verify_skip;
    } else {
        $data['mail_img_verify_skip'] = '';
    }
    
    if ($form->hasValidData('MaximumResolutionMail')) {
        $data['max_res_mail'] = $form->MaximumResolutionMail;
    } else {
        $data['max_res_mail'] = '';
    }

    if ($form->hasValidData('ActiveFileUploadAdmin') && $form->ActiveFileUploadAdmin == true) {
        $data['active_admin_upload'] = true;
    } else {
        $data['active_admin_upload'] = false;
    }

    if ($form->hasValidData('AllowOnlyOneFileUpload') && $form->AllowOnlyOneFileUpload == true) {
        $data['one_file_upload'] = true;
    } else {
        $data['one_file_upload'] = false;
    }

    if ($form->hasValidData('removeMetaTag') && $form->removeMetaTag == true) {
        $data['remove_meta'] = true;
    } else {
        $data['remove_meta'] = false;
    }

    if ($form->hasValidData('AntivirusFileScanEnabled') && $form->AntivirusFileScanEnabled == true) {
        $data['clamav_enabled'] = true;
    } else {
        $data['clamav_enabled'] = false;
    }

    if ($form->hasValidData('soundMessages') && $form->soundMessages == true) {
        $data['sound_messages'] = true;
    } else {
        $data['sound_messages'] = false;
    }

    if ($form->hasValidData('soundMessagesOp') && $form->soundMessagesOp == true) {
        $data['sound_messages_op'] = true;
    } else {
        $data['sound_messages_op'] = false;
    }

    if ($form->hasValidData('soundLength')) {
        $data['sound_length'] = $form->soundLength;
    } else {
        $data['sound_length'] = 30;
    }

    if ($form->hasValidData('AllowedFileTypes') && $form->AllowedFileTypes != '') {
        $data['ft_op'] = $form->AllowedFileTypes;
    } else {
        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('file/configurations', 'Please enter valid file type!');
    }

    if ($form->hasValidData('AllowedFileTypesUser') && $form->AllowedFileTypesUser != '') {
        $data['ft_us'] = $form->AllowedFileTypesUser;
    } else {
        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('file/configuration', 'Please enter valid file type!');
    }

    if ($form->hasValidData('MaximumFileSize')) {
        $data['fs_max'] = $form->MaximumFileSize;
    } else {
        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('file/configuration', 'Please enter valid maximum file size!');
    }

    if ($form->hasValidData('ClamAVSocketPath')) {
        $data['clamd_sock'] = $form->ClamAVSocketPath;
    } else {
        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('file/configuration', 'Please enter valid maximum file size!');
    }

    if ($form->hasValidData('ClamAVSocketLength')) {
        $data['clamd_sock_len'] = $form->ClamAVSocketLength;
    } else {
        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('file/configuration', 'Please enter valid maximum file size!');
    }

    if (empty($Errors)) {

        $fileData->value = serialize($data);
        $fileData->saveThis();

        // Cleanup cache to recompile templates etc.
        $CacheManager = erConfigClassLhCacheConfig::getInstance();
        $CacheManager->expireCache();

        $tpl->set('updated', 'done');
    } else {
        $tpl->set('errors', $Errors);
    }

}

$tpl->set('file_data', $data);
$Result['content'] = $tpl->fetch();
$Result['path'] = array(
    array('url' => erLhcoreClassDesign::baseurl('system/configuration'), 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration', 'System configuration')),
    array('url' => erLhcoreClassDesign::baseurl('file/configuration'), 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration', 'File configuration')));

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('file.configuration_path', array('result' => & $Result));

?>