<?php

header ( 'content-type: application/json; charset=utf-8' );

$searchQuery = isset($_GET['q']) ? $_GET['q'] : '';

if ($searchQuery != '') {

    $session = erLhcoreClassUser::getSession();
    $q = $session->createFindQuery( 'erLhcoreClassModelUser' );
    $q->where($q->expr->lOr(
        $q->expr->like('name', $q->bindValue('%' . $searchQuery . '%')),
        $q->expr->like('surname', $q->bindValue('%' . $searchQuery . '%')),
        $q->expr->like('email', $q->bindValue('%' . $searchQuery . '%'))
    ),
    $q->expr->eq( 'disabled', $q->bindValue( 0 ) ))
    ->orderBy('id DESC')
    ->limit( 20 );

    $operators = $session->find( $q, 'erLhcoreClassModelUser' );

} else {
    $operators = erLhcoreClassModelUser::getList(array('limit' => 20, 'filter' => array('disabled' => 0)));
}

erLhcoreClassChat::prefillGetAttributes($operators, array('name_official','id'), array(), array('remove_all' => true, 'clean_ignore' => true));

$presentMembers = erLhcoreClassModelGroupChatMember::getList(array('filter'=> array('group_id' => (int)$Params['user_parameters']['id'])));

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