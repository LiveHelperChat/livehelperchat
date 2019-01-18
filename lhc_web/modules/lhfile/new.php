<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhfile/new.tpl.php');
$tpl->set('file_uploaded', false);

if (isset($_POST['UploadFileAction'])) {

    $errors = array();
    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('file.before_file_new_admin.file_store', array('errors' => & $errors));

    if (empty($errors)) {
        $fileData = erLhcoreClassModelChatConfig::fetch('file_configuration');
        $data = (array)$fileData->data;

        try {
            $path = 'var/storage/' . date('Y') . 'y/' . date('m') . '/' . date('d') . '/au/';

            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('file.new.file_path', array('path' => & $path));

            $upload_handler = new erLhcoreClassFileUploadAdmin(array('user_id' => $currentUser->getUserID(), 'file_name_manual' => $_POST['Name'], 'upload_dir' => $path, 'download_via_php' => true, 'max_file_size' => $data['fs_max'] * 1024, 'accept_file_types_lhc' => '/\.(' . $data['ft_op'] . ')$/i'));

            if ($upload_handler->uploadedFile instanceof erLhcoreClassModelChatFile) {
                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('file.file_new_admin.file_store', array('chat_file' => $upload_handler->uploadedFile));
            }

            $tpl->set('file_uploaded', true);
        } catch (Exception $e) {
            $tpl->set('errors', array($e->getMessage()));
        }
    } else {
        $tpl->set('errors', $errors);
    }
}

$tpl->set('mode',$Params['user_parameters_unordered']['mode']);


$Result['content'] = $tpl->fetch();

if ($Params['user_parameters_unordered']['mode'] == 'reloadparent') {
	$Result['pagelayout'] = 'popup';
}

$Result['path'] = array(
array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','System configuration')),
array('url' => erLhcoreClassDesign::baseurl('file/list'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','List of files')),
array('url' => erLhcoreClassDesign::baseurl('file/new'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','New file')));

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('file.new_path', array('result' => & $Result));

?>