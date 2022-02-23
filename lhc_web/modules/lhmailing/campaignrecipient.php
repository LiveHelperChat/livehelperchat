<?php

$tpl = erLhcoreClassTemplate::getInstance('lhmailing/campaignrecipient.tpl.php');

if (isset($_GET['doSearch'])) {
    $filterParams = erLhcoreClassSearchHandler::getParams(array('customfilterfile' => 'lib/core/lhmailconv/filter/campaign_recipient.php', 'format_filter' => true, 'use_override' => true, 'uparams' => $Params['user_parameters_unordered']));
    $filterParams['is_search'] = true;
} else {
    $filterParams = erLhcoreClassSearchHandler::getParams(array('customfilterfile' => 'lib/core/lhmailconv/filter/campaign_recipient.php', 'format_filter' => true, 'uparams' => $Params['user_parameters_unordered']));
    $filterParams['is_search'] = false;
}

$campaign = erLhcoreClassModelMailconvMailingCampaign::fetch($filterParams['input_form']->campaign);

if (!($campaign instanceof erLhcoreClassModelMailconvMailingCampaign)) {
    die('Invalid campaign!');
}

$append = erLhcoreClassSearchHandler::getURLAppendFromInput($filterParams['input_form']);

if ($Params['user_parameters_unordered']['export'] == 'csv') {
    erLhcoreClassMailconvExport::exportCampaignRecipientCSV(array_merge($filterParams['filter'], array('limit' => 100000, 'offset' => 0)), ['campaign' => $campaign]);
    exit;
}

$pages = new lhPaginator();
$pages->items_total = erLhcoreClassModelMailconvMailingCampaignRecipient::getCount($filterParams['filter']);
$pages->translationContext = 'chat/activechats';
$pages->serverURL = erLhcoreClassDesign::baseurl('mailing/campaignrecipient').$append;
$pages->paginate();
$tpl->set('pages',$pages);

if ($pages->items_total > 0) {
    $items = erLhcoreClassModelMailconvMailingCampaignRecipient::getList(array_merge(array('limit' => $pages->items_per_page, 'offset' => $pages->low),$filterParams['filter']));
    $tpl->set('items',$items);
}

$filterParams['input_form']->form_action = erLhcoreClassDesign::baseurl('mailing/campaignrecipient') . '/' . $filterParams['input_form']->campaign;
$tpl->set('input',$filterParams['input_form']);
$tpl->set('inputAppend',$append);
$tpl->set('campaign', $campaign);


$Result['content'] = $tpl->fetch();

$Result['path'] = array (
    array('url' => erLhcoreClassDesign::baseurl('system/configuration') . '#!#mailconv', 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Mail conversation')),
    array(
        'url' => erLhcoreClassDesign::baseurl('mailing/campaign'),
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv', 'Campaign')
    ),
    array(
        'url' => erLhcoreClassDesign::baseurl('mailing/editcampaign') . '/' . $campaign->id,
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv', 'Edit campaign')
    ),
    array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Recipients'))
);

?>