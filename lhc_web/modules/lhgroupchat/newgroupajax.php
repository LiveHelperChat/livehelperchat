<?php

header ( 'content-type: application/json; charset=utf-8' );

$payload = json_decode(file_get_contents('php://input'),true);

if (isset($payload['name']) && $payload['name'] != '') {

    try {
        if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
            throw new Exception('Invalid CSRF token!');
        }

        // Create a group chat
        $item = new erLhcoreClassModelGroupChat();
        $item->name = $payload['name'];

        if ($currentUser->hasAccessTo('lhgroupchat','public_chat')) {
            $item->type = isset($payload['public']) && $payload['public'] == 1 ? 1 : 0;
        } else {
            $item->type = 1;
        }

        $item->user_id = $currentUser->getUserID();
        $item->time = time();
        $item->saveThis();

        // Create a member
        $newMember = new erLhcoreClassModelGroupChatMember();
        $newMember->user_id = $item->user_id;
        $newMember->group_id = $item->id;
        $newMember->last_activity = time();
        $newMember->jtime = time();
        $newMember->saveThis();

        echo json_encode($item);
    } catch (Exception $e){
        http_response_code(400);
        echo json_encode(array('error' => true, "messages" => $e->getMessage()));
    }

} else {
    http_response_code(400);
    echo json_encode(array('error' => true));
}

exit;

?>