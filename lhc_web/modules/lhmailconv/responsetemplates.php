<?php

$tpl = erLhcoreClassTemplate::getInstance('lhmailconv/responsetemplates.tpl.php');

if (isset($_GET['doSearch'])) {
    $filterParams = erLhcoreClassSearchHandler::getParams(array('customfilterfile' => 'lib/core/lhmailconv/filter/response_templates.php', 'format_filter' => true, 'use_override' => true, 'uparams' => $Params['user_parameters_unordered']));
    $filterParams['is_search'] = true;
} else {
    $filterParams = erLhcoreClassSearchHandler::getParams(array('customfilterfile' => 'lib/core/lhmailconv/filter/response_templates.php', 'format_filter' => true, 'uparams' => $Params['user_parameters_unordered']));
    $filterParams['is_search'] = false;
}

$append = erLhcoreClassSearchHandler::getURLAppendFromInput($filterParams['input_form']);

if (is_array($filterParams['input_form']->dep_id) && !empty($filterParams['input_form']->dep_id)) {
    $filterParams['filter']['innerjoin']['lhc_mailconv_response_template_dep'] = array('`lhc_mailconv_response_template_dep`.`template_id`','`lhc_mailconv_response_template`.`id`');
    $filterParams['filter']['filterin']['`lhc_mailconv_response_template_dep`.`dep_id`'] = $filterParams['input_form']->dep_id;
}

if (is_array($filterParams['input_form']->subject_id) && !empty($filterParams['input_form']->subject_id)) {
    $filterParams['filter']['innerjoin']['lhc_mailconv_response_template_subject'] = array('`lhc_mailconv_response_template_subject`.`template_id`','`lhc_mailconv_response_template`.`id`');
    $filterParams['filter']['filterin']['`lhc_mailconv_response_template_subject`.`subject_id`'] = $filterParams['input_form']->subject_id;
}

if (isset($_GET['export'])) {
    erLhcoreClassChatExport::exportResponseTemplate(erLhcoreClassModelMailconvResponseTemplate::getList(array_merge_recursive($filterParams['filter'],array('offset' => 0, 'limit' => false, 'sort' => 'id ASC'))));
}

$pages = new lhPaginator();
$pages->items_total = erLhcoreClassModelMailconvResponseTemplate::getCount($filterParams['filter']);
$pages->translationContext = 'chat/activechats';
$pages->serverURL = erLhcoreClassDesign::baseurl('mailconv/responsetemplates').$append;
$pages->paginate();
$tpl->set('pages',$pages);

if ($pages->items_total > 0) {
    $items = erLhcoreClassModelMailconvResponseTemplate::getList(array_merge(array('limit' => $pages->items_per_page, 'offset' => $pages->low),$filterParams['filter']));
    $tpl->set('items',$items);
}

$filterParams['input_form']->form_action = erLhcoreClassDesign::baseurl('mailconv/responsetemplates');
$tpl->set('input',$filterParams['input_form']);
$tpl->set('inputAppend',$append);
$tpl->set('currentUser',$currentUser);

$Result['content'] = $tpl->fetch();


$Result['path'] = array (
    array('url' => erLhcoreClassDesign::baseurl('system/configuration') . '#!#mailconv', 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Mail conversation')),
    array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Response templates'))
);

?>