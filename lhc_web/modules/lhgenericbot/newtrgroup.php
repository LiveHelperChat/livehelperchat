<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhgenericbot/newtrgroup.tpl.php');
$botTranslationGroup = new erLhcoreClassModelGenericBotTrGroup();

if ( isset($_POST['Cancel_bot']) ) {
    erLhcoreClassModule::redirect('genericbot/listtranslations');
    exit;
}

$exceptions = array();

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.genericbot_exceptions', array('exceptions' => & $exceptions));

if (isset($_POST['Save_bot']) || isset($_POST['Update_bot']))
{
    $Errors = erLhcoreClassGenericBot::validateBotTranslationGroup($botTranslationGroup);

    if (count($Errors) == 0)
    {
        $botTranslationGroup->saveThis();

        $userPhotoErrors = erLhcoreClassGenericBot::validateBotPhoto($botTranslationGroup);

        if ($userPhotoErrors !== false && count($userPhotoErrors) == 0) {
            $botTranslationGroup->saveThis();
        }

        if (isset($_POST['Update_bot'])) {
            erLhcoreClassModule::redirect('genericbot/edittrgroup','/' . $botTranslationGroup->id);
        } else {
            erLhcoreClassModule::redirect('genericbot/listtranslations');
        }
        exit ;

    } else {
        $tpl->set('errors',$Errors);
    }
}

$tpl->set('item',$botTranslationGroup);

$Result['content'] = $tpl->fetch();
$Result['path'] = array(
    array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/new','System configuration')),
    array('url' => erLhcoreClassDesign::baseurl('genericbot/listtranslations'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/new','Translations groups')),
    array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/new','New')),
)

?>