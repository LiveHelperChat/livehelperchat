<?php

$tpl = erLhcoreClassTemplate::getInstance('lhgenericbot/edit.tpl.php');

$bot =  erLhcoreClassModelGenericBotBot::fetch((int)$Params['user_parameters']['id']);

if ( isset($_POST['Cancel_bot']) ) {
    erLhcoreClassModule::redirect('genericbot/list');
    exit;
}

if ( isset($_POST['Delete_bot']) ) {

    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        erLhcoreClassModule::redirect('genericbot/list');
        exit;
    }

    $bot->removeThis();
    erLhcoreClassModule::redirect('genericbot/list');
    exit;
}

if (isset($_POST['DeletePhoto'])) {
    $bot->removeFile();
}

if (isset($_POST['Update_bot']) || isset($_POST['Save_bot'])  )
{
    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        erLhcoreClassModule::redirect('genericbot/list');
        exit;
    }

    $Errors = erLhcoreClassGenericBot::validateBot($bot);

    $userPhotoErrors = erLhcoreClassGenericBot::validateBotPhoto($bot);

    if ($userPhotoErrors !== false) {
        $Errors = array_merge($Errors, $userPhotoErrors);
    }

    if (count($Errors) == 0)
    {
        $bot->saveThis();

        if (isset($_POST['Save_bot'])) {
            erLhcoreClassModule::redirect('genericbot/list');
            exit;
        } else {
            $tpl->set('updated',true);
        }

    }  else {
        $tpl->set('errors',$Errors);
    }
}

$tpl->set('item', $bot);


$Result['content'] = $tpl->fetch();
$Result['path'] = array(
    array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/edit','System configuration')),
    array('url' => erLhcoreClassDesign::baseurl('genericbot/list'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/edit','Bots')),
    array('title' => $bot->name));

?>