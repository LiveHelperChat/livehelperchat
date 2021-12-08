<?php

$tpl = erLhcoreClassTemplate::getInstance('lhmailing/detailssend.tpl.php');

$recipient = erLhcoreClassModelMailconvMailingCampaignRecipient::fetch($Params['user_parameters']['id']);

if (!($recipient instanceof erLhcoreClassModelMailconvMailingCampaignRecipient)) {
    die('Invalid recipient!');
}

$tpl->set('item', $recipient);

echo $tpl->fetch();
exit;

?>