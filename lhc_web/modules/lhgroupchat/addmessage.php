<?php

header ( 'content-type: application/json; charset=utf-8' );

$db = ezcDbInstance::get();
$db->beginTransaction();

try {

    $groupChat = erLhcoreClassModelGroupChat::fetch((int)$Params['user_parameters']['id']);

    $payload = json_decode(file_get_contents('php://input'),true);

    if (!isset($payload['msg']) || trim($payload['msg']) == '') {
        throw new Exception('Please enter a message!');
    }

    $userData = $currentUser->getUserData();

    $msg = new erLhcoreClassModelGroupMsg();
    $msg->time = time();
    $msg->user_id = $userData->id;
    $msg->msg = trim($payload['msg']);
    $msg->chat_id = $groupChat->id;
    $msg->name_support = $userData->name_official;
    $msg->saveThis();

    $groupChat->last_msg_op_id = $userData->id;
    $groupChat->last_msg = $msg->msg;
    $groupChat->last_user_msg_time = time();
    $groupChat->last_msg_id = $msg->id;
    $groupChat->updateThis(array('update' => array('last_msg_op_id','last_msg','last_user_msg_time','last_msg_id')));

    echo json_encode(array('result' => 'ok'));
    $db->commit();

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(array('result' => $e->getMessage()));
    $db->rollback();
}

exit;
?>