<?php

header ( 'content-type: application/json; charset=utf-8' );

session_write_close();

$startTimeRequest = microtime();

$search = erLhAbstractModelSavedSearch::fetch($Params['user_parameters']['id']);
$totalRecords = 0;
$content = '';

// Chats
if ($search->scope == 'chat') {
    $tpl = erLhcoreClassTemplate::getInstance( 'lhviews/loadview.tpl.php');
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

    $startTime = microtime();
    $totalRecords = $pages->items_total = erLhcoreClassModelChat::getCount($filterSearch);
    erLhcoreClassViewResque::logSlowView($startTime, microtime(), $search);

    $pages->translationContext = 'chat/pendingchats';
    $pages->serverURL = erLhcoreClassDesign::baseurl('views/loadview').'/'.$search->id;
    $pages->paginate();
    $tpl->set('pages',$pages);
    $filter = array_merge_recursive($filterSearch, array('limit' => $pages->items_per_page, 'offset' => $pages->low));
    $items = erLhcoreClassModelChat::getList($filter);
    $iconsAdditional = erLhAbstractModelChatColumn::getList(array('ignore_fields' => array('position','conditions','column_identifier','enabled'), 'sort' => false, 'filter' => array('icon_mode' => 1, 'enabled' => 1, 'chat_enabled' => 1)));
    erLhcoreClassChat::prefillGetAttributes($items, array(), array(), array('additional_columns' => $iconsAdditional, 'do_not_clean' => true));
    $tpl->set('icons_additional',$iconsAdditional);
    $tpl->set('items', $items);
    $tpl->set('list_mode', $Params['user_parameters_unordered']['mode'] == 'list');

    // Update view data, so background worker do nothing
    $search->total_records = (int)$totalRecords;
    $search->updated_at = time();
    $search->requested_at = time();
    $search->updateThis(['update' => ['total_records','updated_at','requested_at']]);
    $content = $tpl->fetch();
} else {
    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('views.loadview', array(
        'total_records' => & $totalRecords,
        'content' => & $content,
        'search' => $search,
        'uparams' => $Params['user_parameters_unordered']
    ));
}

echo json_encode(['body' => $content, 'view_id' => $search->id, 'total_records' => (int)$totalRecords]);

erLhcoreClassModule::logSlowRequest($startTimeRequest, microtime(), $currentUser->getUserID(), ['loadview' => $Params['user_parameters']['id']]);

exit;

?>