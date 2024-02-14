<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhmailarchive/listarchivemails.tpl.php');

$archive = \LiveHelperChat\Models\mailConv\Archive\Range::fetch($Params['user_parameters']['id']);

if (isset($_GET['doSearch'])) {
    $filterParams = erLhcoreClassSearchHandler::getParams(array('customfilterfile' => 'lib/core/lhmailconv/filter/conversations.php', 'format_filter' => true, 'use_override' => true, 'uparams' => $Params['user_parameters_unordered']));
    $filterParams['is_search'] = true;
} else {
    $filterParams = erLhcoreClassSearchHandler::getParams(array('customfilterfile' => 'lib/core/lhmailconv/filter/conversations.php', 'format_filter' => true, 'uparams' => $Params['user_parameters_unordered']));
    $filterParams['is_search'] = false;
}


$append = erLhcoreClassSearchHandler::getURLAppendFromInput($filterParams['input_form']);

// Chat id has to be replaced to table one
if (isset($filterParams['filter']['filter']['`lhc_mailconv_conversation`.`id`'])) {
    $filterParams['filter']['filter']['`lhc_mailconv_conversation_archive_' . $Params['user_parameters']['id'] . '`.`id`'] = $filterParams['filter']['filter']['`lhc_mailconv_conversation`.`id`'];
    unset($filterParams['filter']['filter']['`lhc_mailconv_conversation`.`id`']);
}

// Set correct archive tables
$archive->setTables();

$filterParams['filter']['sort'] = '`id` DESC';

$pages = new lhPaginator();
$pages->serverURL = erLhcoreClassDesign::baseurl('mailarchive/listarchivemails').'/'.$archive->id.$append;
$pages->items_total = \LiveHelperChat\Models\mailConv\Archive\Conversation::getCount($filterParams['filter']);
$pages->setItemsPerPage(20);
$pages->paginate();

$items = array();
if ($pages->items_total > 0) {
    try {
        $items = \LiveHelperChat\Models\mailConv\Archive\Conversation::getList(array_merge(array('offset' => $pages->low, 'limit' => $pages->items_per_page,'sort' => 'id ASC'),$filterParams['filter']));
    } catch (Exception $e) {
        print_r($e->getMessage());
    }
}

$filterParams['input_form']->form_action = erLhcoreClassDesign::baseurl('mailarchive/listarchivemails').'/'.$archive->id;
$tpl->set('input',$filterParams['input_form']);
$tpl->set('items',$items);
$tpl->set('archive',$archive);
$tpl->set('pages',$pages);
$tpl->set('can_delete',erLhcoreClassUser::instance()->hasAccessTo('lhmailarchive','configuration'));

$Result['content'] = $tpl->fetch();

$Result['path'] = array(
    array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','System configuration')),
    array('url' => erLhcoreClassDesign::baseurl('mailarchive/archive'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/archive','Mail archive')),
    array('url' => erLhcoreClassDesign::baseurl('mailarchive/list'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/list','Archives list')));
$Result['path'][] = array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chatarchive/list','Archived mails'));




?>