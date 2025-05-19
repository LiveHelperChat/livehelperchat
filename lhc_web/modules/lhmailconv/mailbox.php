<?php

$tpl = erLhcoreClassTemplate::getInstance('lhmailconv/mailbox.tpl.php');

if (isset($_GET['doSearch'])) {
    $filterParams = erLhcoreClassSearchHandler::getParams(array('customfilterfile' => 'lib/core/lhmailconv/filter/mailbox.php', 'format_filter' => true, 'use_override' => true, 'uparams' => $Params['user_parameters_unordered']));
    $filterParams['is_search'] = true;
} else {
    $filterParams = erLhcoreClassSearchHandler::getParams(array('customfilterfile' => 'lib/core/lhmailconv/filter/mailbox.php', 'format_filter' => true, 'uparams' => $Params['user_parameters_unordered']));
    $filterParams['is_search'] = false;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $Params['user_parameters_unordered']['resetstatus'] == 'reset') {

    if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
        die('Invalid CSRF Token');
        exit;
    }

    $mailboxReset = erLhcoreClassModelMailconvMailbox::getList(array_merge(array('limit' => 1000, 'offset' => 0),$filterParams['filter']));
    foreach ($mailboxReset as $item) {
        $item->last_process_time = 0;
        $item->sync_started = 0;
        $item->last_sync_time = 0;
        $item->sync_status = erLhcoreClassModelMailconvMailbox::SYNC_PENDING;
        $uuidStatusArray = $item->uuid_status_array;
        foreach ($uuidStatusArray as $key => $uuidStatus) {
            $uuidStatusArray[$key] = 0;
        }
        $item->uuid_status = json_encode($uuidStatusArray);
        $item->updateThis(array('update' => array('sync_started','last_sync_time','sync_status','last_process_time','uuid_status')));
    }

    echo "ok";
    exit;
}

$append = erLhcoreClassSearchHandler::getURLAppendFromInput($filterParams['input_form']);

$pages = new lhPaginator();
$pages->items_total = erLhcoreClassModelMailconvMailbox::getCount($filterParams['filter']);
$pages->translationContext = 'chat/activechats';
$pages->serverURL = erLhcoreClassDesign::baseurl('mailconv/mailbox').$append;
$pages->paginate();
$tpl->set('pages',$pages);

if ($pages->items_total > 0) {
    $items = erLhcoreClassModelMailconvMailbox::getList(array_merge(array('limit' => $pages->items_per_page, 'offset' => $pages->low),$filterParams['filter']));
    $tpl->set('items',$items);
}

$filterParams['input_form']->form_action = erLhcoreClassDesign::baseurl('mailconv/mailbox');
$tpl->set('input',$filterParams['input_form']);
$tpl->set('inputAppend',$append);

$Result['content'] = $tpl->fetch();

$Result['path'] = array (
    array('url' => erLhcoreClassDesign::baseurl('system/configuration') . '#!#mailconv', 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Mail conversation')),
    array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Mailbox'))
);

?>