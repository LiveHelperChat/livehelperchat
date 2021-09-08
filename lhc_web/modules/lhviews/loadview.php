<?php

header ( 'content-type: application/json; charset=utf-8' );

$tpl = erLhcoreClassTemplate::getInstance( 'lhviews/loadview.tpl.php');

$search = erLhAbstractModelSavedSearch::fetch($Params['user_parameters']['id']);
$totalRecords = 0;

// Chats
if ($search->scope == 0) {
    $tpl->set('can_delete_global',$currentUser->hasAccessTo('lhchat','deleteglobalchat'));
    $tpl->set('can_delete_general',$currentUser->hasAccessTo('lhchat','deletechat'));
    $tpl->set('can_close_global',$currentUser->hasAccessTo('lhchat','allowcloseremote'));
    $tpl->set('current_user_id',$currentUser->getUserID());
    $tpl->set('search',$search);

    $filterSearch = $search->params_array['filter'];
    
    if ($search->days > 0) {
        $filterSearch['filtergte']['time'] = time() - $search->days * 24 * 3600;
    }

    $pages = new lhPaginator();
    $totalRecords = $pages->items_total = erLhcoreClassModelChat::getCount($filterSearch);
    $pages->translationContext = 'chat/pendingchats';
    $pages->serverURL = erLhcoreClassDesign::baseurl('views/loadview').'/'.$search->id;
    $pages->paginate();
    $tpl->set('pages',$pages);
    $filter = array_merge_recursive($filterSearch, array('limit' => $pages->items_per_page, 'offset' => $pages->low));
    $items = erLhcoreClassModelChat::getList($filter);
    $tpl->set('items', $items);
    $tpl->set('list_mode', $Params['user_parameters_unordered']['mode'] == 'list');
}

echo json_encode(['body' => $tpl->fetch(), 'total_records' => (int)$totalRecords]);

exit;

?>