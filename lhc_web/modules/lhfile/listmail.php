<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhfile/listmail.tpl.php');

if (isset($_GET['doSearch'])) {
	$filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'chat','module_file' => 'mailfilelist','format_filter' => true, 'use_override' => true, 'uparams' => $Params['user_parameters_unordered']));
	$filterParams['is_search'] = true;
} else {
	$filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'chat','module_file' => 'mailfilelist','format_filter' => true, 'uparams' => $Params['user_parameters_unordered']));
	$filterParams['is_search'] = false;
}

$append = erLhcoreClassSearchHandler::getURLAppendFromInput($filterParams['input_form']);

// If filtering by conversation_id, fetch message IDs and use them instead
if (isset($filterParams['filter']['filter']['conversation_id'])) {
    $conversationId = $filterParams['filter']['filter']['conversation_id'];
    $messages = erLhcoreClassModelMailconvMessage::getList(['filter' => ['conversation_id' => $conversationId], 'select_columns' => ['id']]);
    $messageIds = array_column($messages, 'id');

    unset($filterParams['filter']['filter']['conversation_id']);

    if (!empty($messageIds)) {
        $filterParams['filter']['filterin']['message_id'] = $messageIds;
    } else {
        // No messages found, ensure no results
        $filterParams['filter']['filter']['message_id'] = -1;
    }
}

$pages = new lhPaginator();
$pages->serverURL = erLhcoreClassDesign::baseurl('file/listmail').$append;
$pages->items_total = erLhcoreClassModelMailconvFile::getCount($filterParams['filter']);
$pages->setItemsPerPage(20);
$pages->paginate();

$items = array();
if ($pages->items_total > 0) {
    $items = erLhcoreClassModelMailconvFile::getList(array_merge(array('offset' => $pages->low, 'limit' => $pages->items_per_page,'sort' => 'id DESC'),$filterParams['filter']));
}

$tpl->set('items',$items);
$tpl->set('pages',$pages);

$filterParams['input_form']->form_action = erLhcoreClassDesign::baseurl('file/listmail');
$tpl->set('input',$filterParams['input_form']);

$Result['content'] = $tpl->fetch();
$Result['path'] = array(
array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','System configuration')),
array('url' => erLhcoreClassDesign::baseurl('file/listmail'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','List of mail files')));

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('file.listmail_path', array('result' => & $Result));

?>
