<?php

session_write_close();

if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
    die('Invalid CSFR Token');
    exit;
}

$recipient = erLhcoreClassModelMailconvMailingCampaignRecipient::fetch($Params['user_parameters']['id']);

if (!($recipient instanceof erLhcoreClassModelMailconvMailingCampaignRecipient)) {
    die('Invalid recipient!');
}

erLhcoreClassMailConvMailingWorker::sendEmail($recipient, erLhcoreClassModelMailconvMailingCampaign::fetch($recipient->campaign_id));

header('Location: ' . $_SERVER['HTTP_REFERER']);
exit;

?>