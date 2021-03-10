<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhwebhooks/newincoming.tpl.php');
$item = new erLhcoreClassModelChatIncomingWebhook();

if ( isset($_POST['Cancel_action']) ) {
    erLhcoreClassModule::redirect('webhooks/incomingwebhooks');
    exit;
}

if (isset($_POST['Save_action']) || isset($_POST['Update_action']))
{
    $Errors = erLhcoreClassAdminChatValidatorHelper::validateIncomingWebhook($item);

    if (count($Errors) == 0)
    {
        $item->saveThis();

        if (isset($_POST['Update_action'])) {
            erLhcoreClassModule::redirect('webhooks/editincoming','/' . $item->id);
        } else {
            erLhcoreClassModule::redirect('webhooks/incomingwebhooks');
        }

        exit ;

    } else {
        $tpl->set('errors',$Errors);
    }
}

$tpl->set('item', $item);
$Result['additional_footer_js'] = '<script src="'.erLhcoreClassDesign::designJS('js/angular.incoming.webhooks.js').'"></script>';

$Result['content'] = $tpl->fetch();
$Result['path'] = array(
    array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('webhooks/module','System configuration')),
    array('url' => erLhcoreClassDesign::baseurl('webhooks/incomingwebhooks'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('webhooks/module','Incoming webhooks')),
    array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('webhooks/module','New')),
)

?>