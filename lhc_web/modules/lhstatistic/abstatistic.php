<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhstatistic/campaignmodal.tpl.php');

$campaign = erLhAbstractModelProactiveChatCampaign::fetch($Params['user_parameters']['campaign_id']);
$tpl->set('invitation',$campaign);
$tpl->set('stats',erLhcoreClassChatStatistic::getProactiveStatistic(array('filter' => array('filter' => array('campaign_id' => $campaign->id)))));

echo $tpl->fetch();
exit;

?>