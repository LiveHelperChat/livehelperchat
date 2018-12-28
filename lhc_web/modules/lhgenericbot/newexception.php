<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhgenericbot/newexception.tpl.php');
$botException = new erLhcoreClassModelGenericBotException();

if ( isset($_POST['Cancel_bot']) ) {
    erLhcoreClassModule::redirect('genericbot/listexception');
    exit;
}

$exceptions = array();

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.genericbot_exceptions', array('exceptions' => & $exceptions));

if (isset($_POST['Save_bot']) || isset($_POST['Update_bot']))
{
    $Errors = erLhcoreClassGenericBot::validateBotException($botException);

    if (count($Errors) == 0)
    {
        $botException->saveThis();

        if (isset($_POST['Update_bot'])) {
            erLhcoreClassModule::redirect('genericbot/editexception','/' . $botException->id);
        } else {
            erLhcoreClassModule::redirect('genericbot/listexception');
        }
        exit ;

    } else {
        $tpl->set('errors',$Errors);
    }
}

$tpl->set('item',$botException);
$tpl->set('exceptions',erLhcoreClassGenericBotValidator::formatExceptionList($botException,$exceptions));

$Result['content'] = $tpl->fetch();
$Result['path'] = array(
    array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/new','System configuration')),
    array('url' => erLhcoreClassDesign::baseurl('genericbot/list'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/new','Bots')),
    array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/new','New')),
)

?>