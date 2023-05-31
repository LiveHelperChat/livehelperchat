<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhgenericbot/conditions/newcondition.tpl.php');
$condition = new \LiveHelperChat\Models\Bot\Condition();

if ( isset($_POST['Cancel_bot']) ) {
    erLhcoreClassModule::redirect('genericbot/conditions');
    exit;
}

if (isset($_POST['Save_bot']) || isset($_POST['Update_bot']))
{

    $Errors = LiveHelperChat\Helpers\Bot\ConditionValidator::validate($condition);

    if (count($Errors) == 0)
    {
        $condition->saveThis();

        if (isset($_POST['Update_bot'])) {
            erLhcoreClassModule::redirect('genericbot/editcondition','/' . $condition->id);
        } else {
            erLhcoreClassModule::redirect('genericbot/conditions');
        }
        exit ;

    } else {
        $tpl->set('errors',$Errors);
    }
}

$tpl->set('item',$condition);

$Result['additional_footer_js'] = '<script src="'.erLhcoreClassDesign::designJS('js/angular.lhc.priority.js').'"></script>';
$Result['content'] = $tpl->fetch();
$Result['path'] = array(
    array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/new','System configuration')),
    array('url' => erLhcoreClassDesign::baseurl('genericbot/conditions'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/new','Conditions')),
    array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/new','New')),
)

?>