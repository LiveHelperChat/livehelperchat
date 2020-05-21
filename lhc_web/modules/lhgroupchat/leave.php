<?php

header ( 'content-type: application/json; charset=utf-8' );

$item = erLhcoreClassModelGroupChat::fetch($Params['user_parameters']['id']);

$groupChatMember = erLhcoreClassModelGroupChatMember::findOne(array('filter' => array('user_id' => $currentUser->getUserID(), 'group_id' => $Params['user_parameters']['id'])));

if ($groupChatMember instanceof erLhcoreClassModelGroupChatMember) {
    $groupChatMember->removeThis();
    $item->updateMembersCount();
}

// If it's private group and the last person left the chat, remove group itself.
if ($item->type == erLhcoreClassModelGroupChat::PRIVATE_CHAT) {
    if (erLhcoreClassModelGroupChatMember::getCount(array('filtergt' => array('jtime' => 0), 'filter' => array('group_id' => $item->id))) == 0) {
        $item->removeThis();
    }
}

echo json_encode(array());

exit;

?>