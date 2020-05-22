<?php

header ( 'content-type: application/json; charset=utf-8' );

try {
    $groupChat = erLhcoreClassModelGroupChat::fetch($Params['user_parameters']['id']);

    if (!($groupChat instanceof erLhcoreClassModelGroupChat)) {
        throw new Exception('Group not found!');
    }

    if ($groupChat->type == erLhcoreClassModelGroupChat::PRIVATE_CHAT) {

        // User tries to join private chat, but he is not a member of it.
        // Throw an exception
        if (erLhcoreClassModelGroupChatMember::getCount(array('filter' => array('group_id' => $groupChat->id, 'user_id' => $currentUser->getUserID()))) == 0){
            throw new Exception('You are not a member of this private group!');
        }
    } else {
        // Auto join if it's public chat
        if (erLhcoreClassModelGroupChatMember::getCount(array('filter' => array('group_id' => $groupChat->id, 'user_id' => $currentUser->getUserID()))) == 0) {
            $newMember = new erLhcoreClassModelGroupChatMember();
            $newMember->user_id = $currentUser->getUserID();
            $newMember->group_id = $groupChat->id;
            $newMember->last_activity = time();
            $newMember->jtime = time();
            $newMember->saveThis();

            $groupChat->updateMembersCount();
        }
    }

    echo json_encode(array(
        'chat' => $groupChat
    ));

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(array(
        'error' => $e->getMessage()
    ));
}


exit;

?>