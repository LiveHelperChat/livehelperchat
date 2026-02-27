<?php

header('content-type: application/json; charset=utf-8');

$onlineUser = erLhcoreClassModelChatOnlineUser::fetch($Params['user_parameters']['ouser_id']);

if ($Params['user_parameters_unordered']['action'] === 'resetinvitation' && isset($_SERVER['HTTP_X_CSRFTOKEN']) && $currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {

    $onlineUser->invitation_id = 0;
    $onlineUser->operator_message = '';
    $onlineUser->operator_user_id = 0;
    $onlineUser->operator_user_proactive = '';
    $onlineUser->message_seen = 0;
    $onlineUser->message_seen_ts = 0;
    $onlineUser->chat_time = 0;
    $onlineUser->chat_id = 0;
    $onlineUser->online_attr_system = '';
    $onlineUser->saveThis();

    $tpl = erLhcoreClassTemplate::getInstance( 'lhchat/online_user/debug_data.tpl.php');
    $tpl->set('online_user', $onlineUser);

    $q = ezcDbInstance::get()->createDeleteQuery();

    // Delete realted one time invitations
    $q->deleteFrom('lh_abstract_proactive_chat_invitation_one_time')->where( $q->expr->eq('vid_id', $onlineUser->id));
    $stmt = $q->prepare();
    $stmt->execute();

    echo json_encode(['data_debug' => $tpl->fetch(), 'data' => erTranslationClassLhTranslation::getInstance()->getTranslation('lhaudit/debuginvitation','Invitation data for visitor was reset!')]);
    exit;
}

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