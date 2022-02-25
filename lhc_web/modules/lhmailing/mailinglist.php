<?php

$tpl = erLhcoreClassTemplate::getInstance('lhmailing/mailinglist.tpl.php');

if (isset($_GET['doSearch'])) {
    $filterParams = erLhcoreClassSearchHandler::getParams(array('customfilterfile' => 'lib/core/lhmailconv/filter/mailbox.php', 'format_filter' => true, 'use_override' => true, 'uparams' => $Params['user_parameters_unordered']));
    $filterParams['is_search'] = true;
} else {
    $filterParams = erLhcoreClassSearchHandler::getParams(array('customfilterfile' => 'lib/core/lhmailconv/filter/mailbox.php', 'format_filter' => true, 'uparams' => $Params['user_parameters_unordered']));
    $filterParams['is_search'] = false;
}

$append = erLhcoreClassSearchHandler::getURLAppendFromInput($filterParams['input_form']);

if (!$currentUser->hasAccessTo('lhmailing','all_mailing_list')) {
    $filterParams['filter']['filter']['user_id'] = $currentUser->getUserID();
}

$pages = new lhPaginator();
$pages->items_total = erLhcoreClassModelMailconvMailingList::getCount($filterParams['filter']);
$pages->translationContext = 'chat/activechats';
$pages->serverURL = erLhcoreClassDesign::baseurl('mailing/mailinglist').$append;
$pages->paginate();
$tpl->set('pages',$pages);

if ($pages->items_total > 0) {
    $items = erLhcoreClassModelMailconvMailingList::getList(array_merge(array('limit' => $pages->items_per_page, 'offset' => $pages->low),$filterParams['filter']));
    $tpl->set('items',$items);
}

$filterParams['input_form']->form_action = erLhcoreClassDesign::baseurl('mailing/mailinglist');
$tpl->set('input',$filterParams['input_form']);
$tpl->set('inputAppend',$append);

$Result['content'] = $tpl->fetch();

$Result['path'] = array (
    array('url' => erLhcoreClassDesign::baseurl('system/configuration') . '#!#mailconv', 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Mail conversation')),
    array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Mailing list'))
);

?>