<?php

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Content-Type: image/png");
echo hex2bin('89504e470d0a1a0a0000000d494844520000000100000001010300000025db56ca00000003504c5445000000a77a3dda0000000174524e530040e6d8660000000a4944415408d76360000000020001e221bc330000000049454e44ae426082');

if (strlen($Params['user_parameters']['id']) != 40) {
    exit;
}

$hash = $Params['user_parameters']['id'];

$messageOpen = erLhcoreClassModelMailconvMessageOpen::findOne(['filter' => ['hash' => $Params['user_parameters']['id']]]);

if (!($messageOpen instanceof erLhcoreClassModelMailconvMessageOpen)) {
    $messageOpen = new erLhcoreClassModelMailconvMessageOpen();
    $messageOpen->hash = $Params['user_parameters']['id'];
}

$messageOpen->opened_at = time();
$messageOpen->saveThis();

// Update message open if we have a message already
// This can be false, if fetching messages is delayed for whatever
// reason
$lastMessage = erLhcoreClassModelMailconvMessage::findOne(['filter' => ['message_hash' => $messageOpen->hash, 'opened_at' => 0]]);

if (!($lastMessage instanceof erLhcoreClassModelMailconvMessage)) {
    exit;
}

$lastMessage->opened_at = $messageOpen->opened_at;
$lastMessage->updateThis(['update' => ['opened_at']]);

// Campaign recipient update on mail open
$campaignRecipient = erLhcoreClassModelMailconvMailingCampaignRecipient::findOne(['filter' => ['message_id' => $lastMessage->id]]);

if ($campaignRecipient instanceof erLhcoreClassModelMailconvMailingCampaignRecipient) {
    $campaignRecipient->opened_at = $messageOpen->opened_at;
    $campaignRecipient->updateThis(['update' => ['opened_at']]);
}

// Conversation update
if (!($lastMessage->conversation instanceof erLhcoreClassModelMailconvConversation)) {
    exit;
}

$lastMessage->conversation->opened_at = $lastMessage->opened_at;
$lastMessage->conversation->updateThis(['update' => ['opened_at']]);

exit;


?>