<?php

if ($Params['user_parameters_unordered']['action'] == 'copy' && is_numeric($Params['user_parameters_unordered']['id'])) {

    $campaign = erLhcoreClassModelMailconvMailingCampaign::fetch($Params['user_parameters_unordered']['id']);

    if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
        die('Invalid CSRF Token');
        exit;
    }

    $campaign->id = null;
    $campaign->name = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Copy of').' '.$campaign->name;
    $campaign->enabled = 0;
    $campaign->status = erLhcoreClassModelMailconvMailingCampaign::STATUS_PENDING;
    $campaign->starts_at = 0;
    $campaign->saveThis();

    foreach (erLhcoreClassModelMailconvMailingCampaignRecipient::getList(['sort' => 'id ASC', 'limit' => false,'filter' => ['campaign_id' => $Params['user_parameters_unordered']['id']]]) as $copyRecipient) {
        $copyRecipient->id = null;
        $copyRecipient->campaign_id = $campaign->id;
        $copyRecipient->status = erLhcoreClassModelMailconvMailingCampaignRecipient::PENDING;
        $copyRecipient->message_id = 0;
        $copyRecipient->conversation_id = 0;
        $copyRecipient->opened_at = 0;
        $copyRecipient->send_at = 0;
        $copyRecipient->log = '';
        $copyRecipient->saveThis();
    }

    exit;
}

$tpl = erLhcoreClassTemplate::getInstance('lhmailing/campaign.tpl.php');

if (isset($_GET['doSearch'])) {
    $filterParams = erLhcoreClassSearchHandler::getParams(array('customfilterfile' => 'lib/core/lhmailconv/filter/campaign.php', 'format_filter' => true, 'use_override' => true, 'uparams' => $Params['user_parameters_unordered']));
    $filterParams['is_search'] = true;
} else {
    $filterParams = erLhcoreClassSearchHandler::getParams(array('customfilterfile' => 'lib/core/lhmailconv/filter/campaign.php', 'format_filter' => true, 'uparams' => $Params['user_parameters_unordered']));
    $filterParams['is_search'] = false;
}

$append = erLhcoreClassSearchHandler::getURLAppendFromInput($filterParams['input_form']);

if (!$currentUser->hasAccessTo('lhmailing','all_campaigns')) {
    $filterParams['filter']['filter']['user_id'] = $currentUser->getUserID();
}

$pages = new lhPaginator();
$pages->items_total = erLhcoreClassModelMailconvMailingCampaign::getCount($filterParams['filter']);
$pages->translationContext = 'chat/activechats';
$pages->serverURL = erLhcoreClassDesign::baseurl('mailing/campaign').$append;
$pages->paginate();
$tpl->set('pages',$pages);

if ($pages->items_total > 0) {
    $items = erLhcoreClassModelMailconvMailingCampaign::getList(array_merge(array('limit' => $pages->items_per_page, 'offset' => $pages->low),$filterParams['filter']));
    $tpl->set('items',$items);
}

$filterParams['input_form']->form_action = erLhcoreClassDesign::baseurl('mailing/campaign');
$tpl->set('input',$filterParams['input_form']);
$tpl->set('inputAppend',$append);

$Result['content'] = $tpl->fetch();

$Result['path'] = array (
    array('url' => erLhcoreClassDesign::baseurl('system/configuration') . '#!#mailconv', 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Mail conversation')),
    array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Campaign'))
);

?>