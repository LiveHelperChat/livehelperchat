<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhgenericbot/new.tpl.php');
$bot = new erLhcoreClassModelGenericBotBot();

if ( isset($_POST['Cancel_bot']) ) {
    erLhcoreClassModule::redirect('genericbot/list');
    exit;
}

if (isset($_POST['Save_bot']) || isset($_POST['Update_bot']))
{
    $Errors = erLhcoreClassGenericBot::validateBot($bot);

    if (count($Errors) == 0)
    {
        $bot->saveThis();

        $userPhotoErrors = erLhcoreClassGenericBot::validateBotPhoto($bot);

        if ($userPhotoErrors !== false && count($userPhotoErrors) == 0) {
            $bot->saveThis();
        }

        if (isset($_POST['Update_bot'])) {
            erLhcoreClassModule::redirect('genericbot/edit','/' . $bot->id);
        } else {
            erLhcoreClassModule::redirect('genericbot/list');
        }

        exit ;

    } else {
        $tpl->set('errors',$Errors);
    }
}

$tpl->set('item',$bot);

$Result['content'] = $tpl->fetch();
$Result['path'] = array(
    array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/new','System configuration')),
    array('url' => erLhcoreClassDesign::baseurl('genericbot/list'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/new','Bots')),
    array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/new','New')),
)

?>