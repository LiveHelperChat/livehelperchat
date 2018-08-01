<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhstatistic/campaignmodal.tpl.php');

$invitation = erLhAbstractModelProactiveChatInvitation::fetch($Params['user_parameters']['invitation_id']);
$tpl->set('invitation',$invitation);
$tpl->set('stats',erLhcoreClassChatStatistic::getProactiveStatistic(array('filter' => array('filter' => array('invitation_id' => $invitation->id)))));

echo $tpl->fetch();
exit;

?>