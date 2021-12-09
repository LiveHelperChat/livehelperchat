<?php

$tpl = erLhcoreClassTemplate::getInstance('lhmailing/mailingrecipient.tpl.php');

if (isset($_GET['doSearch'])) {
    $filterParams = erLhcoreClassSearchHandler::getParams(array('customfilterfile' => 'lib/core/lhmailconv/filter/mailing_recipient.php', 'format_filter' => true, 'use_override' => true, 'uparams' => $Params['user_parameters_unordered']));
    $filterParams['is_search'] = true;
} else {
    $filterParams = erLhcoreClassSearchHandler::getParams(array('customfilterfile' => 'lib/core/lhmailconv/filter/mailing_recipient.php', 'format_filter' => true, 'uparams' => $Params['user_parameters_unordered']));
    $filterParams['is_search'] = false;
}

// Mailing list filter
if (!empty($filterParams['input_form']->ml)) {
    $filterParams['filter']['innerjoin']['lhc_mailconv_mailing_list_recipient'] = array('`lhc_mailconv_recipient`.`id`','`lhc_mailconv_mailing_list_recipient`.`mailing_recipient_id`');
    $filterParams['filter']['filterin']['`lhc_mailconv_mailing_list_recipient`.`mailing_list_id`'] = $filterParams['input_form']->ml;
}

$append = erLhcoreClassSearchHandler::getURLAppendFromInput($filterParams['input_form']);

$pages = new lhPaginator();
$pages->items_total = erLhcoreClassModelMailconvMailingRecipient::getCount($filterParams['filter']);
$pages->translationContext = 'chat/activechats';
$pages->serverURL = erLhcoreClassDesign::baseurl('mailing/mailingrecipient').$append;
$pages->paginate();
$tpl->set('pages',$pages);

if ($pages->items_total > 0) {
    $items = erLhcoreClassModelMailconvMailingRecipient::getList(array_merge(array('limit' => $pages->items_per_page, 'offset' => $pages->low),$filterParams['filter']));
    $tpl->set('items',$items);
}

$filterParams['input_form']->form_action = erLhcoreClassDesign::baseurl('mailing/mailingrecipient');
$tpl->set('input',$filterParams['input_form']);
$tpl->set('inputAppend',$append);

$Result['content'] = $tpl->fetch();

$Result['path'] = array (
    array('url' => erLhcoreClassDesign::baseurl('system/configuration') . '#!#mailconv', 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Mail conversation')),
    array('url' => erLhcoreClassDesign::baseurl('mailing/mailinglist'), 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Mailing list')),
    array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Mailing recipient'))
);

?>