<?php

header('content-type: application/json; charset=utf-8');

$onlineUser = erLhcoreClassModelChatOnlineUser::fetch($Params['user_parameters']['ouser_id']);

$checksLimits = [
    'empty_operator_message' => $onlineUser->operator_message == '',
    'pro_active_limitation' => erLhcoreClassModelChatConfig::fetch('pro_active_limitation')->current_value,
    'pending_chats_count' => erLhcoreClassChat::getPendingChatsCountPublic($onlineUser->dep_id > 0 ? $onlineUser->dep_id : false)
];

$executionParams = array('debug' => true, 'tag' => isset($paramsHandle['tag']) ? $paramsHandle['tag'] : '');

if (is_numeric($Params['user_parameters']['invitation_id']) && $Params['user_parameters']['invitation_id'] > 0) {
    $invitation = erLhAbstractModelProactiveChatInvitation::fetch($Params['user_parameters']['invitation_id']);
    $executionParams['invitation_id'] = [$invitation->id];
}

if ($Params['user_parameters']['tag'] != '') {
    $executionParams['tag'] = $Params['user_parameters']['tag'];
}

$debug = erLhAbstractModelProactiveChatInvitation::processProActiveInvitation($onlineUser, $executionParams);

$tpl = erLhcoreClassTemplate::getInstance( 'lhaudit/debuginvitation.tpl.php');
$tpl->set('online_user', $onlineUser);
$tpl->set('debug_invitation', $debug);

echo json_encode(['data' => $tpl->fetch()]);
exit;
?>