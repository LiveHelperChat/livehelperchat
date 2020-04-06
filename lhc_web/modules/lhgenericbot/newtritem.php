<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhgenericbot/newtritem.tpl.php');

$botTranslationItem = new erLhcoreClassModelGenericBotTrItem();

if ( isset($_POST['Cancel_bot']) ) {
    erLhcoreClassModule::redirect('genericbot/listtranslationsitems','/(group_id)/' . $botTranslationItem->group_id);
    exit;
}

$botTranslationItem->group_id = (int)$Params['user_parameters_unordered']['group_id'];

if (isset($_POST['Save_bot']) || isset($_POST['Update_bot']))
{

    $Errors = erLhcoreClassGenericBot::validateBotTranslationItem($botTranslationItem);

    if (count($Errors) == 0)
    {
        $botTranslationItem->saveThis();

        if (isset($_POST['Update_bot'])) {
            erLhcoreClassModule::redirect('genericbot/edittritem','/' . $botTranslationItem->id);
        } else {
            erLhcoreClassModule::redirect('genericbot/listtranslationsitems','/(group_id)/' . $botTranslationItem->group_id);
        }
        exit ;

    } else {
        $tpl->set('errors',$Errors);
    }
}

$tpl->set('item',$botTranslationItem);
$tpl->set('group_id',(int)$Params['user_parameters_unordered']['group_id']);

$Result['content'] = $tpl->fetch();

$Result['additional_footer_js'] = '<script type="text/javascript" src="'.erLhcoreClassDesign::designJS('js/angular.lhc.tritem.js').'"></script>';

$Result['path'] = array(
    array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/edit','System configuration')),
    array('url' => erLhcoreClassDesign::baseurl('genericbot/listtranslations'), 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/edit','Translations groups')),
    array('url' => erLhcoreClassDesign::baseurl('genericbot/listtranslationsitems') . '/(group_id)/' . (int)$Params['user_parameters_unordered']['group_id'], 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/edit','Translations items')),
    array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/edit','New')));

?>