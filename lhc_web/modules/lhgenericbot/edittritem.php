<?php

$tpl = erLhcoreClassTemplate::getInstance('lhgenericbot/edittritem.tpl.php');

$item =  erLhcoreClassModelGenericBotTrItem::fetch((int)$Params['user_parameters']['id']);

if ( isset($_POST['Cancel_bot']) ) {
    erLhcoreClassModule::redirect('genericbot/listtranslations');
    exit;
}

if (isset($_POST['Update_bot']) || isset($_POST['Save_bot'])  )
{
    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        erLhcoreClassModule::redirect('genericbot/listtranslationsitems');
        exit;
    }

    $Errors = erLhcoreClassGenericBot::validateBotTranslationItem($item);

    if (count($Errors) == 0)
    {
        $item->saveThis();

        if (isset($_POST['Save_bot'])) {
            erLhcoreClassModule::redirect('genericbot/listtranslationsitems','/(group_id)/' . $item->group_id);
            exit;
        } else {
            $tpl->set('updated',true);
        }

    }  else {
        $tpl->set('errors',$Errors);
    }
}

$tpl->set('item', $item);

$Result['additional_footer_js'] = '<script type="text/javascript" src="'.erLhcoreClassDesign::designJS('js/angular.bot.rest.api.js').'"></script>';

$Result['content'] = $tpl->fetch();
$Result['path'] = array(
    array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/edit','System configuration')),
    array('url' => erLhcoreClassDesign::baseurl('genericbot/listtranslations'), 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/edit','Translations groups')),
    array('url' => erLhcoreClassDesign::baseurl('genericbot/listtranslationsitems') . '/(group_id)/' . $item->group_id, 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/edit','Translations items')),
    array('title' => $item->identifier));

?>