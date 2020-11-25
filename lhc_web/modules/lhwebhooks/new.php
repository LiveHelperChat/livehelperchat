<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhwebhooks/new.tpl.php');
$item = new erLhcoreClassModelChatWebhook();

if ( isset($_POST['Cancel_action']) ) {
    erLhcoreClassModule::redirect('webhooks/configuration');
    exit;
}

if (isset($_POST['Save_action']) || isset($_POST['Update_action']))
{
    $Errors = erLhcoreClassAdminChatValidatorHelper::validateWebhook($item);

    if (count($Errors) == 0)
    {
        $item->saveThis();

        if (isset($_POST['Update_action'])) {
            erLhcoreClassModule::redirect('webhooks/edit','/' . $item->id);
        } else {
            erLhcoreClassModule::redirect('webhooks/configuration');
        }

        exit ;

    } else {
        $tpl->set('errors',$Errors);
    }
}

$tpl->set('item', $item);
$Result['additional_footer_js'] = '<script src="'.erLhcoreClassDesign::designJS('js/angular.webhooks.js').'"></script>';

$Result['content'] = $tpl->fetch();
$Result['path'] = array(
    array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('webhooks/module','System configuration')),
    array('url' => erLhcoreClassDesign::baseurl('webhooks/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('webhooks/module','Webhooks')),
    array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('webhooks/module','New')),
)

?>