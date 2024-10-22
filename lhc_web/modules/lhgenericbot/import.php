<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhgenericbot/import.tpl.php' );

if (ezcInputForm::hasPostData()) {

    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        erLhcoreClassModule::redirect('genericbot/import');
        exit;
    }

    if (erLhcoreClassSearchHandler::isFile('botfile',array('json'))) {

        $dir = 'var/tmpfiles/';
        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('theme.temppath',array('dir' => & $dir));

        erLhcoreClassFileUpload::mkdirRecursive( $dir );

        $filename = erLhcoreClassSearchHandler::moveUploadedFile('botfile',$dir);
        $content = file_get_contents($dir . $filename);
        unlink($dir . $filename);
        $data = json_decode($content,true);

        if ($data !== null) {
            $botData = erLhcoreClassGenericBotValidator::importBot($data);

            if (isset($_POST['rest_api']) && $_POST['rest_api'] > 0) {
                foreach ($botData['triggers'] as $trigger) {
                    $actions = $trigger->actions_front;
                    foreach ($actions as $indexAction  => $action) {
                        if (isset($actions[$indexAction]['content']['rest_api']) && is_numeric($actions[$indexAction]['content']['rest_api'])) {
                            $actions[$indexAction]['content']['rest_api'] = (int)$_POST['rest_api'];
                        }
                    }
                    $trigger->actions_front = $actions;
                    $trigger->actions = json_encode($actions);
                    $trigger->updateThis(['update' => ['actions']]);
                }
            }
        }

        $tpl->set('updated',true);
    } else {
        $tpl->set('errors',array(erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/import','Invalid file!')));
    }
}

$Result['path'] = array(array('url' => erLhcoreClassDesign::baseurl('genericbot/list'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/list','Bots')),array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('theme/index','Import')));
$Result['content'] = $tpl->fetch();