<?php

$tpl = erLhcoreClassTemplate::getInstance('lhmailing/newcampaignrecipient.tpl.php');

$campaign = erLhcoreClassModelMailconvMailingCampaign::fetch($Params['user_parameters']['id']);

if (!($campaign instanceof erLhcoreClassModelMailconvMailingCampaign)) {
    die('Invalid campaign!');
}

if (is_numeric($Params['user_parameters']['recipient_id']) && $Params['user_parameters']['recipient_id'] > 0) {
    $item = erLhcoreClassModelMailconvMailingCampaignRecipient::fetch($Params['user_parameters']['recipient_id']);
} else {
    $item = new erLhcoreClassModelMailconvMailingCampaignRecipient();
    $item->campaign_id = $campaign->id;
    $item->type = erLhcoreClassModelMailconvMailingCampaignRecipient::TYPE_MANUAL;
}

if (ezcInputForm::hasPostData() && !(!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token']))) {

    $items = array();
    $Errors = erLhcoreClassMailconvMailingValidator::validateCampaignRecipient($item);
    if (count($Errors) == 0) {
        try {
            $item->saveThis();
            $tpl->set('updated', true);
        } catch (Exception $e) {
            $tpl->set('errors', array($e->getMessage()));
        }
    } else {
        $tpl->set('errors', $Errors);
    }
}

$tpl->set('item', $item);
$tpl->set('campaign', $campaign);
echo $tpl->fetch();
exit;

?>