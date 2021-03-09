<?php

$tpl = erLhcoreClassTemplate::getInstance('lhwebhooks/editincoming.tpl.php');

$item = erLhcoreClassModelChatIncomingWebhook::fetch((int)$Params['user_parameters']['id']);

if ( isset($_POST['Cancel_action']) ) {
    erLhcoreClassModule::redirect('webhooks/configuration');
    exit;
}

if (isset($_POST['Update_action']) || isset($_POST['Save_action'])  )
{
    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        erLhcoreClassModule::redirect('webhooks/configuration');
        exit;
    }

    $Errors = erLhcoreClassAdminChatValidatorHelper::validateIncomingWebhook($item);

    if (count($Errors) == 0)
    {
        $item->saveThis();

        if (isset($_POST['Save_action'])) {
            erLhcoreClassModule::redirect('webhooks/incomingwebhooks');
            exit;
        } else {
            $tpl->set('updated',true);
        }

    }  else {
        $tpl->set('errors',$Errors);
    }
}

$tpl->set('item', $item);

$Result['additional_footer_js'] = '<script src="'.erLhcoreClassDesign::designJS('js/angular.incoming.webhooks.js').'"></script>';

$Result['content'] = $tpl->fetch();
$Result['path'] = array(
    array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('webhooks/module','System configuration')),
    array('url' => erLhcoreClassDesign::baseurl('webhooks/incomingwebhooks'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('webhooks/module','Incoming webhooks')),
    array('title' => $item->name));

?>