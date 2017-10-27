<?php

$tpl = erLhcoreClassTemplate::getInstance('lhuser/import.tpl.php');

$importData = erLhcoreClassModelChatConfig::fetch('import_configuration');
$data = (array)$importData->data;

if (ezcInputForm::hasPostData()) {

    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        erLhcoreClassModule::redirect('user/import');
        exit;
    }

    $importErrors = erLhcoreClassUserValidator::validateUsersImport($data);

    if (count($importErrors) == 0) {

        $importData->identifier = 'import_configuration';
        $importData->explain = 'Import configuration';
        $importData->hidden = 1;
        $importData->type = 0;
        $importData->value = serialize($data);
        $importData->saveThis();

        try {
            if (erLhcoreClassSearchHandler::isFile('file',array('csv'))) {
                $dir = 'var/tmpfiles/';
                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('theme.temppath',array('dir' => & $dir));

                erLhcoreClassFileUpload::mkdirRecursive( $dir );

                $filename = erLhcoreClassSearchHandler::moveUploadedFile('file', $dir);
                $content = file_get_contents($dir . $filename);

                unlink($dir . $filename);

                $status = erLhcoreClassUserValidator::importUsers($data, $content);

                $tpl->set('imported', $status);
            }

            $tpl->set('updated',true);
        } catch (Exception $e) {
            $tpl->set('errors',array($e->getMessage()));
        }

    }  else {
        $tpl->set('errors',$importErrors);
    }

}

$tpl->set('importSettings',$data);

$Result['content'] = $tpl->fetch();

$Result['path'] = array(
    array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','System configuration')),
    array('url' => erLhcoreClassDesign::baseurl('user/userlist'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/edit','Users')),
    array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/import','Import'))
);

?>