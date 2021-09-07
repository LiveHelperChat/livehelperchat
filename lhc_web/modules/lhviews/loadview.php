<?php

header ( 'content-type: application/json; charset=utf-8' );

$tpl = erLhcoreClassTemplate::getInstance( 'lhviews/loadview.tpl.php');

$search = erLhAbstractModelSavedSearch::fetch($Params['user_parameters']['id']);

// Chats
if ($search->scope == 0) {
    $tpl->set('search',$search);
    $filter = array_merge_recursive($search->params_array['filter'], array('limit' => 20, 'offset' => 0));
    $tpl->set('items', erLhcoreClassModelChat::getList($filter));
}

echo json_encode(['body' => $tpl->fetch()]);

exit;

?>