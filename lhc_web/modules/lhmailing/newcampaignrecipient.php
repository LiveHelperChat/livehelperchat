<?php

$tpl = erLhcoreClassTemplate::getInstance('lhmailing/newcampaignrecipient.tpl.php');

$campaign = erLhcoreClassModelMailconvMailingCampaign::fetch($Params['user_parameters']['id']);

if (!($campaign instanceof erLhcoreClassModelMailconvMailingCampaign)) {
    die('Invalid campaign!');
}

$item = new erLhcoreClassModelMailconvMailingCampaignRecipient();
$item->campaign_id = $campaign->id;
$item->type = erLhcoreClassModelMailconvMailingCampaignRecipient::TYPE_MANUAL;

if (ezcInputForm::hasPostData()) {
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