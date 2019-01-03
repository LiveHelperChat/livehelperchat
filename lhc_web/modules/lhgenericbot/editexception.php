<?php

$tpl = erLhcoreClassTemplate::getInstance('lhgenericbot/editexception.tpl.php');

$botException =  erLhcoreClassModelGenericBotException::fetch((int)$Params['user_parameters']['id']);

if ( isset($_POST['Cancel_bot']) ) {
    erLhcoreClassModule::redirect('genericbot/listexceptions');
    exit;
}

if ( isset($_POST['Delete_bot']) ) {

    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        erLhcoreClassModule::redirect('genericbot/listexceptions');
        exit;
    }

    $botException->removeThis();
    erLhcoreClassModule::redirect('genericbot/listexceptions');
    exit;
}

$exceptions = array();

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.genericbot_exceptions', array('exceptions' => & $exceptions));

if (isset($_POST['Update_bot']) || isset($_POST['Save_bot'])  )
{
    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        erLhcoreClassModule::redirect('genericbot/listexceptions');
        exit;
    }

    $Errors = erLhcoreClassGenericBot::validateBotException($botException);

    if (count($Errors) == 0)
    {
        $botException->saveThis();

        if (isset($_POST['Save_bot'])) {
            erLhcoreClassModule::redirect('genericbot/listexceptions');
            exit;
        } else {
            $tpl->set('updated',true);
        }

    }  else {
        $tpl->set('errors',$Errors);
    }
}

$exceptionsFormatted = erLhcoreClassGenericBotValidator::formatExceptionList($botException,$exceptions);

$tpl->set('item', $botException);
$tpl->set('exceptions',$exceptionsFormatted);

$Result['content'] = $tpl->fetch();
$Result['path'] = array(
    array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/edit','System configuration')),
    array('url' => erLhcoreClassDesign::baseurl('genericbot/listexceptions'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/edit','Exceptions')),
    array('title' => $botException->name));

?>