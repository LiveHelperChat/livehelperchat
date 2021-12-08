<?php

$recipient = erLhcoreClassModelMailconvMailingCampaignRecipient::fetch($Params['user_parameters']['id']);

if (!($recipient instanceof erLhcoreClassModelMailconvMailingCampaignRecipient)) {
    die('Invalid recipient!');
}

erLhcoreClassMailConvMailingWorker::sendEmail($recipient, erLhcoreClassModelMailconvMailingCampaign::fetch($recipient->campaign_id));

header('Location: ' . $_SERVER['HTTP_REFERER']);
exit;

?>