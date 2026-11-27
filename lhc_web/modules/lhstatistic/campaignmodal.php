<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhstatistic/campaignmodal.tpl.php');

$invitation = erLhAbstractModelProactiveChatInvitation::fetch($Params['user_parameters']['invitation_id']);
$tpl->set('invitation',$invitation);

$field = 'invitation_id';

if ($invitation->parent_id > 0) {
    $field = 'variation_id';
}

$tpl->set('stats', erLhcoreClassChatStatistic::getProactiveStatistic(array('filter' => array('filter' => array($field => $invitation->id)))));

echo $tpl->fetch();
exit;

?>