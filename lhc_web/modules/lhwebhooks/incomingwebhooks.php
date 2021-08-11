<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhwebhooks/incomingwebhooks.tpl.php');

if (isset($_GET['doSearch'])) {
    $filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'chat','module_file' => 'webhooks', 'format_filter' => true, 'use_override' => true, 'uparams' => $Params['user_parameters_unordered']));
    $filterParams['is_search'] = true;
} else {
    $filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'chat','module_file' => 'webhooks', 'format_filter' => true, 'uparams' => $Params['user_parameters_unordered']));
    $filterParams['is_search'] = false;
}

// Export
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $restAPI = erLhcoreClassModelChatIncomingWebhook::fetch($_GET['id']);
    $exportData = $restAPI->getState();
    unset($exportData['id']);
    header('Content-Disposition: attachment; filename="incoming-webhook-'.$restAPI->id.'.json"');
    header('Content-Type: application/json');
    echo json_encode($exportData);
    exit;
}

// Import
if (ezcInputForm::hasPostData()) {

    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        erLhcoreClassModule::redirect('webhooks/incomingwebhooks');
        exit;
    }

    if (erLhcoreClassSearchHandler::isFile('restfile',array('json'))) {
        $dir = 'var/tmpfiles/';
        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('theme.temppath',array('dir' => & $dir));

        erLhcoreClassFileUpload::mkdirRecursive( $dir );

        $filename = erLhcoreClassSearchHandler::moveUploadedFile('restfile',$dir);
        $content = file_get_contents($dir . $filename);
        unlink($dir . $filename);
        $data = json_decode($content,true);
            
        if ($data !== null) {
            $restAPI = new erLhcoreClassModelChatIncomingWebhook();
            $restAPI->name = substr(erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/import','Copy of') . ' ' .$data['name'],0,50);
            $restAPI->dep_id = $data['dep_id'];
            $restAPI->disabled = $data['disabled'];
            $restAPI->identifier = $data['identifier'];
            $restAPI->configuration = $data['configuration'];
            $restAPI->scope = $data['scope'];
            $restAPI->saveThis();
            $tpl->set('imported_rest',true);
        }
    }
}


erLhcoreClassChatStatistic::formatUserFilter($filterParams);

$append = erLhcoreClassSearchHandler::getURLAppendFromInput($filterParams['input_form']);

$pages = new lhPaginator();
$pages->items_total = erLhcoreClassModelChatIncomingWebhook::getCount($filterParams['filter']);
$pages->translationContext = 'chat/pendingchats';
$pages->serverURL = erLhcoreClassDesign::baseurl('webhooks/incomingwebhooks').$append;
$pages->paginate();
$tpl->set('pages',$pages);

if ($pages->items_total > 0) {
    $items = erLhcoreClassModelChatIncomingWebhook::getList(array_merge($filterParams['filter'],array('limit' => $pages->items_per_page,'offset' => $pages->low)));
    $tpl->set('items',$items);
}

$filterParams['input_form']->form_action = erLhcoreClassDesign::baseurl('webhooks/configuration');
$tpl->set('input',$filterParams['input_form']);
$tpl->set('inputAppend',$append);

$Result['content'] = $tpl->fetch();

$Result['path'] = array(
    array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('webhooks/module','System configuration')),
    array('url' => erLhcoreClassDesign::baseurl('notifications/index'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('webhooks/module', 'Incoming webhooks'))
);

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('webhooks.list_path',array('result' => & $Result));
?>
