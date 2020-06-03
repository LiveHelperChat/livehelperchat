<?php

header ( 'content-type: application/json; charset=utf-8' );

$operators = array(
    (int)$Params['user_parameters']['id'],
    (int)$currentUser->getUserID()
);

$db = ezcDbInstance::get();
$db->beginTransaction();

try {
    // We need to find a private chat where only we are the members with another operator
    $sql = "SELECT DISTINCT `lh_group_chat`.`id` FROM `lh_group_chat`
INNER JOIN lh_group_chat_member ON `lh_group_chat_member`.`group_id` = `lh_group_chat`.`id`
WHERE 
`lh_group_chat_member`.`user_id` IN (". implode(',',$operators) . ") AND
`lh_group_chat`.`type` = 1 AND
`lh_group_chat`.`tm` = 2";

    $stmt = $db->prepare($sql);
    $stmt->execute();
    $chatId = $stmt->fetch(PDO::FETCH_COLUMN);

    if (is_numeric($chatId)) {
        $groupChat = erLhcoreClassModelGroupChat::fetch($chatId);
    } else {

        $operator = erLhcoreClassModelUser::fetch((int)$Params['user_parameters']['id']);

        // Create a group chat
        $groupChat = new erLhcoreClassModelGroupChat();
        $groupChat->name = $operator->name_official;
        $groupChat->type = 1;
        $groupChat->user_id = $currentUser->getUserID();
        $groupChat->time = time();
        $groupChat->tm = 2;
        $groupChat->saveThis();

        $msg = new erLhcoreClassModelGroupMsg();
        $msg->msg = (string)$currentUser->getUserData(true)->name_official . ' ' . erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','has invited') . ' ' . $operator->name_official . ' ' . erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','for the private chat.');
        $msg->chat_id = $groupChat->id;
        $msg->user_id = -1;
        $msg->time = time();
        $msg->saveThis();

        $groupChat->last_msg_id = $msg->id;
        $groupChat->updateThis(array('update' => array('last_msg_id')));

        // Create a member
        $newMember = new erLhcoreClassModelGroupChatMember();
        $newMember->user_id = $groupChat->user_id;
        $newMember->group_id = $groupChat->id;
        $newMember->last_activity = time();
        $newMember->jtime = time();
        $newMember->saveThis();

        // Invite another operator
        $newMember = new erLhcoreClassModelGroupChatMember();
        $newMember->user_id = $operator->id;
        $newMember->group_id = $groupChat->id;
        $newMember->last_activity = time();
        $newMember->jtime = 0;
        $newMember->saveThis();



    }

    $db->commit();
    echo json_encode($groupChat->getState());
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode($e->getMessage());
    $db->rollback();
}

exit;

?>