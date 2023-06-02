<?php

$tpl = erLhcoreClassTemplate::getInstance('lhgenericbot/conditions/editcondition.tpl.php');

$condition = \LiveHelperChat\Models\Bot\Condition::fetch((int)$Params['user_parameters']['id']);

if ( isset($_POST['Cancel_bot']) ) {
    erLhcoreClassModule::redirect('genericbot/commands');
    exit;
}

if (isset($_POST['Update_bot']) || isset($_POST['Save_bot'])  )
{
    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        erLhcoreClassModule::redirect('genericbot/conditions');
        exit;
    }

    $Errors = LiveHelperChat\Helpers\Bot\ConditionValidator::validate($condition);

    if (count($Errors) == 0)
    {
        $condition->saveThis();

        if (isset($_POST['Save_bot'])) {
            erLhcoreClassModule::redirect('genericbot/conditions');
            exit;
        } else {
            $tpl->set('updated',true);
        }

    }  else {
        $tpl->set('errors',$Errors);
    }
}

$tpl->set('item', $condition);
$Result['additional_footer_js'] = '<script src="'.erLhcoreClassDesign::designJS('js/angular.lhc.priority.js').'"></script>';

$Result['content'] = $tpl->fetch();
$Result['path'] = array(
    array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/edit','System configuration')),
    array('url' => erLhcoreClassDesign::baseurl('genericbot/conditions'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/edit','Conditions')),
    array('title' => $condition->name));

?>