<?php

header ( 'content-type: application/json; charset=utf-8' );

$db = ezcDbInstance::get();
$db->beginTransaction();

try {

    if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
        throw new Exception('Invalid CSRF token!');
    }

    $item = erLhcoreClassModelGroupChat::fetch($Params['user_parameters']['id']);

    if (!($item instanceof erLhcoreClassModelGroupChat)) {
        throw new Exception('Group chat was not found!');
    }

    // Only members of a private group chat may cancel invitations
    if ($item->type == erLhcoreClassModelGroupChat::PRIVATE_CHAT && $item->user_id != $currentUser->getUserID() && erLhcoreClassModelGroupChatMember::getCount(array('filter' => array('group_id' => $item->id, 'user_id' => $currentUser->getUserID()))) == 0) {
        throw new Exception('You are not a member of this private group!');
    }

    erLhcoreClassGroupChat::cancelInvite($item->id, $Params['user_parameters']['op_id']);

    $item->updateMembersCount();

    $db->commit();
    echo json_encode(array());
} catch (Exception $e){
    http_response_code(400);
    echo json_encode($e->getMessage());
    $db->rollback();
}

exit;

?>