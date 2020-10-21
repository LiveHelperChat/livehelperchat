<?php

header ( 'content-type: application/json; charset=utf-8' );

$db = ezcDbInstance::get();
$db->beginTransaction();

try {
    $item = erLhcoreClassModelGroupChat::fetchAndLock($Params['user_parameters']['id']);

    erLhcoreClassGroupChat::inviteOperator($item->id, $Params['user_parameters']['op_id'], ($item->type == erLhcoreClassModelGroupChat::SUPPORT_CHAT ? erLhcoreClassModelGroupChatMember::SUPPORT_CHAT : erLhcoreClassModelGroupChatMember::NORMAL_CHAT));

    $userInvited = erLhcoreClassModelUser::fetch($Params['user_parameters']['op_id']);

    $msg = new erLhcoreClassModelGroupMsg();
    $msg->msg = (string)$currentUser->getUserData(true)->name_official . ' ' . erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','has invited') . ' ' . $userInvited->name_official . ' ' . erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','for the private chat.');
    $msg->chat_id = $item->id;
    $msg->user_id = -1;
    $msg->time = time();
    $msg->saveThis();

    $item->last_msg_id = $msg->id;
    $item->updateThis(array('update' => array('last_msg_id')));

    // We join only if it's support chat and operator has not joined yet
    if ($item->type == erLhcoreClassModelGroupChat::SUPPORT_CHAT && erLhcoreClassModelGroupChatMember::getCount(array('filter' => array('group_id' => $item->id, 'user_id' => $currentUser->getUserID()))) == 0) {
        $newMember = new erLhcoreClassModelGroupChatMember();
        $newMember->user_id = $currentUser->getUserID();
        $newMember->group_id = $item->id;
        $newMember->last_activity = time();
        $newMember->jtime = time();
        $newMember->saveThis();
    }

    $item->updateMembersCount();

    $db->commit();

    echo json_encode("ok");
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode($e->getMessage());
    $db->rollback();
}

exit;

?>