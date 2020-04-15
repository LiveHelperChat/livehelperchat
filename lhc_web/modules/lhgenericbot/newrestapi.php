<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhgenericbot/newrestapi.tpl.php');
$botRestAPI = new erLhcoreClassModelGenericBotRestAPI();

if ( isset($_POST['Cancel_bot']) ) {
    erLhcoreClassModule::redirect('genericbot/listrestapi');
    exit;
}


if (isset($_POST['Save_bot']) || isset($_POST['Update_bot']))
{
    $Errors = erLhcoreClassGenericBot::validateBotRestAPI($botRestAPI);

    if (count($Errors) == 0)
    {
        $botRestAPI->saveThis();

        if (isset($_POST['Update_bot'])) {
            erLhcoreClassModule::redirect('genericbot/editrestapi','/' . $botRestAPI->id);
        } else {
            erLhcoreClassModule::redirect('genericbot/listrestapi');
        }
        exit ;

    } else {
        $tpl->set('errors',$Errors);
    }
}

$tpl->set('item',$botRestAPI);

$Result['additional_footer_js'] = '<script type="text/javascript" src="'.erLhcoreClassDesign::designJS('js/angular.bot.rest.api.js').'"></script>';

$Result['content'] = $tpl->fetch();
$Result['path'] = array(
    array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/new','System configuration')),
    array('url' => erLhcoreClassDesign::baseurl('genericbot/list'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/new','Bots')),
    array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/new','New')),
)

?>