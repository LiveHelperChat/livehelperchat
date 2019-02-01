<?php

$tpl = erLhcoreClassTemplate::getInstance('lhgenericbot/edittrgroup.tpl.php');

$bot =  erLhcoreClassModelGenericBotTrGroup::fetch((int)$Params['user_parameters']['id']);

if ( isset($_POST['Cancel_bot']) ) {
    erLhcoreClassModule::redirect('genericbot/listtranslations');
    exit;
}

if (isset($_POST['DeletePhoto'])) {
    $bot->removeFile();
}

if (isset($_POST['Update_bot']) || isset($_POST['Save_bot'])  )
{
    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        erLhcoreClassModule::redirect('genericbot/listtranslations');
        exit;
    }

    $Errors = erLhcoreClassGenericBot::validateBotTranslationGroup($bot);

    $userPhotoErrors = erLhcoreClassGenericBot::validateBotPhoto($bot, array('path' => 'var/bottrphoto/'));

    if ($userPhotoErrors !== false) {
        $Errors = array_merge($Errors, $userPhotoErrors);
    }

    if (count($Errors) == 0)
    {
        $bot->saveThis();

        if (isset($_POST['Save_bot'])) {
            erLhcoreClassModule::redirect('genericbot/listtranslations');
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
    array('url' => erLhcoreClassDesign::baseurl('genericbot/listtranslations'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/edit','Translations groups')),
    array('title' => $bot->name));

?>