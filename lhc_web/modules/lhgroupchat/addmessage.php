<?php

if (isset($_GET['rest_api']) && $_GET['rest_api'] == 'true') {
    $restAPI = true;
    erLhcoreClassRestAPIHandler::validateRequest();
    $currentUserId = (int)erLhcoreClassRestAPIHandler::getUserId();

    $payload = $_POST;
    $userData = erLhcoreClassRestAPIHandler::getUser();

} else {

    header ( 'content-type: application/json; charset=utf-8' );

    $currentUser = erLhcoreClassUser::instance();

    if (!$currentUser->hasAccessTo('lhgroupchat','use')) {
        throw new Exception('You do not have permission to use group chats');
    }

    $payload = json_decode(file_get_contents('php://input'),true);
    $userData = $currentUser->getUserData();
}

$db = ezcDbInstance::get();
$db->beginTransaction();

try {

    $groupChat = erLhcoreClassModelGroupChat::fetch((int)$Params['user_parameters']['id']);

    if (!isset($payload['msg']) || trim($payload['msg']) == '') {
        throw new Exception('Please enter a message!');
    }

    $msg = new erLhcoreClassModelGroupMsg();
    $msg->time = time();
    $msg->user_id = $userData->id;
    $msg->msg = trim($payload['msg']);
    $msg->chat_id = $groupChat->id;
    $msg->name_support = $userData->name_official;
    $msg->saveThis();

    $options = [];

    // We join only if it's support chat and operator has not joined yet
    if ($groupChat->type == erLhcoreClassModelGroupChat::SUPPORT_CHAT && erLhcoreClassModelGroupChatMember::getCount(array('filter' => array('group_id' => $groupChat->id, 'user_id' => $currentUser->getUserID()))) == 0) {

        $newMember = new erLhcoreClassModelGroupChatMember();
        $newMember->user_id = $currentUser->getUserID();
        $newMember->group_id = $groupChat->id;
        $newMember->last_activity = time();
        $newMember->jtime = time();
        $newMember->saveThis();

        $groupChat->updateMembersCount();
    }

    $options[] = 'status';
    $groupChat->last_msg_op_id = $userData->id;
    $groupChat->last_msg = mb_substr($msg->msg,0,200);
    $groupChat->last_user_msg_time = time();
    $groupChat->last_msg_id = $msg->id;
    $groupChat->updateThis(array('update' => array('last_msg_op_id','last_msg','last_user_msg_time','last_msg_id')));

    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('group_chat.web_add_msg_admin', array('msg' => & $msg,'chat' => & $groupChat));

    echo json_encode(array('result' => $options));
    $db->commit();

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(array('result' => $e->getMessage()));
    $db->rollback();
}

exit;
?>