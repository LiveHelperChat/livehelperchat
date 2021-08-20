<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhcannedmsg/editreplace.tpl.php');

$item = erLhcoreClassModelCannedMsgReplace::fetch((int)$Params['user_parameters']['id']);

if ( isset($_POST['Cancel_action']) ) {
    erLhcoreClassModule::redirect('cannedmsg/listreplace');
    exit;
}

if (isset($_POST['Save_action']) || isset($_POST['Update_action']))
{
    $Errors = erLhcoreClassAdminChatValidatorHelper::validateReplaceVariable($item);

    if (count($Errors) == 0)
    {
        $item->saveThis();

        if (isset($_POST['Update_action'])) {
            erLhcoreClassModule::redirect('cannedmsg/editreplace','/' . $item->id);
        } else {
            erLhcoreClassModule::redirect('cannedmsg/listreplace');
        }

        exit;

    } else {
        $tpl->set('errors',$Errors);
    }
}

$tpl->set('item',$item);

$Result['content'] = $tpl->fetch();
$Result['additional_footer_js'] = '<script src="'.erLhcoreClassDesign::designJS('js/angular.lhc.replacegenerator.js').'"></script>';
$Result['path'] = array(
    array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/new','System configuration')),
    array('url' => erLhcoreClassDesign::baseurl('cannedmsg/listreplace'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Replaceable variables')),
    array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/new','Edit')),
)

?>