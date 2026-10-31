<?php

header ( 'content-type: application/json; charset=utf-8' );

$searchQuery = isset($_GET['q']) ? $_GET['q'] : '';
$chatId = isset($_GET['id']) ? (int)$_GET['id'] : null;

$groupChat = erLhcoreClassModelGroupChat::fetch((int)$Params['user_parameters']['id']);

if (!($groupChat instanceof erLhcoreClassModelGroupChat)) {
    echo json_encode(array());
    exit;
}

// Private group chat members list is available only to its members
if ($groupChat->type == erLhcoreClassModelGroupChat::PRIVATE_CHAT && $groupChat->user_id != $currentUser->getUserID() && erLhcoreClassModelGroupChatMember::getCount(array('filter' => array('group_id' => $groupChat->id, 'user_id' => $currentUser->getUserID()))) == 0) {
    echo json_encode(array());
    exit;
}

$session = erLhcoreClassUser::getSession();
$q = $session->createFindQuery( 'erLhcoreClassModelUser' );

// We are using search from a chat
// So we have make sure that other operator has permission to read Current chat
if ($chatId !== null && ($chat = erLhcoreClassModelChat::fetch($chatId)) instanceof erLhcoreClassModelChat) {
    $q->innerJoin('lh_userdep', $q->expr->eq('`lh_userdep`.`user_id`', '`lh_users`.`id`'));
    $q->where($q->expr->in('`lh_userdep`.`dep_id`', array(0, $chat->dep_id)));
}

$q->where(
    $q->expr->eq( 'disabled', $q->bindValue( 0 ) )
);

if ($searchQuery != '') {
    $q->where(
        $q->expr->lOr(
            $q->expr->like('name', $q->bindValue('%' . $searchQuery . '%')),
            $q->expr->like('surname', $q->bindValue('%' . $searchQuery . '%')),
            $q->expr->like('email', $q->bindValue('%' . $searchQuery . '%'))
        )
    );
}

$q->orderBy('`lh_users`.`id` DESC')->limit( 20 );

$operators = $session->find( $q, 'erLhcoreClassModelUser' );

erLhcoreClassChat::prefillGetAttributes($operators, array('name_official','id'), array(), array('remove_all' => true, 'clean_ignore' => true));

$presentMembers = erLhcoreClassModelGroupChatMember::getList(array('filter'=> array('group_id' => $groupChat->id)));

foreach ($operators as & $operator) {
    foreach ($presentMembers as $presentMember) {
        if ($presentMember->user_id == $operator->id) {
            if ($presentMember->jtime > 0) {
                $operator->member = true;
            } else {
                $operator->invited = true;
            }
        }
    }
}

echo json_encode(array_values($operators));
exit;