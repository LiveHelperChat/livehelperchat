<?php

$tpl = erLhcoreClassTemplate::getInstance('lhgenericbot/editcommand.tpl.php');

$command = erLhcoreClassModelGenericBotCommand::fetch((int)$Params['user_parameters']['id']);

if ( isset($_POST['Cancel_bot']) ) {
    erLhcoreClassModule::redirect('genericbot/commands');
    exit;
}

if (isset($_POST['Update_bot']) || isset($_POST['Save_bot'])  )
{
    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        erLhcoreClassModule::redirect('genericbot/commands');
        exit;
    }

    $Errors = erLhcoreClassGenericBot::validateBotCommand($command);

    if (count($Errors) == 0)
    {
        $command->saveThis();

        if (isset($_POST['Save_bot'])) {
            erLhcoreClassModule::redirect('genericbot/commands');
            exit;
        } else {
            $tpl->set('updated',true);
        }

    }  else {
        $tpl->set('errors',$Errors);
    }
}

$tpl->set('item', $command);

$Result['content'] = $tpl->fetch();
$Result['path'] = array(
    array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/edit','System configuration')),
    array('url' => erLhcoreClassDesign::baseurl('genericbot/commands'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/edit','Commands')),
    array('title' => $command->command));

?>