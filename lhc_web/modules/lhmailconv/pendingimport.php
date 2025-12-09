<?php

$tpl = erLhcoreClassTemplate::getInstance('lhmailconv/pendingimport.tpl.php');

$filterParams = erLhcoreClassSearchHandler::getParams(array('customfilterfile' => 'lib/core/lhmailconv/filter/pending_import.php', 'format_filter' => true, 'uparams' => $Params['user_parameters_unordered']));
$append = erLhcoreClassSearchHandler::getURLAppendFromInput($filterParams['input_form']);

$pages = new lhPaginator();
$pages->items_total = \LiveHelperChat\Models\mailConv\PendingImport::getCount($filterParams['filter']);
$pages->translationContext = 'mailconv/pendingimport';
$pages->serverURL = erLhcoreClassDesign::baseurl('mailconv/pendingimport').$append;
$pages->paginate();
$tpl->set('pages',$pages);

if ($pages->items_total > 0) {
    $items = \LiveHelperChat\Models\mailConv\PendingImport::getList(array_merge(array('limit' => $pages->items_per_page, 'offset' => $pages->low),$filterParams['filter']));
    $tpl->set('items',$items);
}

$filterParams['input_form']->form_action = erLhcoreClassDesign::baseurl('mailconv/pendingimport');
$tpl->set('input',$filterParams['input_form']);
$tpl->set('inputAppend',$append);
$tpl->set('currentUser',$currentUser);

$Result['content'] = $tpl->fetch();

$Result['path'] = array (
    array('url' => erLhcoreClassDesign::baseurl('system/configuration') . '#!#mailconv', 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Mail conversation')),
    array('title' => 'Pending imports')
);

?>
