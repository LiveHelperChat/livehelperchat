<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhgenericbot/listtranslationsitems.tpl.php');

if (isset($_GET['doSearch'])) {
    $filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'genericbot','module_file' => 'list_translations_items', 'format_filter' => true, 'use_override' => true, 'uparams' => $Params['user_parameters_unordered']));
    $filterParams['is_search'] = true;
} else {
    $filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'genericbot','module_file' => 'list_translations_items', 'format_filter' => true, 'uparams' => $Params['user_parameters_unordered']));
    $filterParams['is_search'] = false;
}

$append = erLhcoreClassSearchHandler::getURLAppendFromInput($filterParams['input_form']);

if (isset($_GET['quick_action'])) {

    $currentUser = erLhcoreClassUser::instance();

    $tpl = erLhcoreClassTemplate::getInstance('lhgenericbot/parts/quick_actions.tpl.php');
    $tpl->set('action_url', erLhcoreClassDesign::baseurl('genericbot/listtranslationsitems') . $append);
    $tpl->set('update_records', erLhcoreClassModelGenericBotTrItem::getCount($filterParams['filter']));

    if (ezcInputForm::hasPostData()) {

        $errors = array();

        if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
            $errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/list','Invalid CSRF token');
        }

        if (empty($errors)) {

            $directItemActions = array(
                'auto_translate' => 'auto_translate',
                'detect_language' => 'detect_language'
            );

            foreach ($directItemActions as $postKey => $column) {
                if (isset($_POST[$postKey]) && $_POST[$postKey] == 'on') {
                    $q = ezcDbInstance::get()->createUpdateQuery();
                    $conditions = erLhcoreClassModelGenericBotTrItem::getConditions($filterParams['filter'], $q);
                    $q->update('lh_generic_bot_tr_item')->set($column, 1);
                    if (!empty($conditions)) {
                        $q->where($conditions);
                    }
                    $q->prepare()->execute();
                }
            }

            $tpl->set('updated', true);
        } else {
            $tpl->set('errors', $errors);
        }
    }

    echo $tpl->fetch();
    exit;
}

$pages = new lhPaginator();
$pages->items_total = erLhcoreClassModelGenericBotTrItem::getCount($filterParams['filter']);
$pages->translationContext = 'chat/pendingchats';
$pages->serverURL = erLhcoreClassDesign::baseurl('genericbot/listtranslationsitems').$append;
$pages->paginate();
$tpl->set('pages',$pages);

if ($pages->items_total > 0) {
    $items = erLhcoreClassModelGenericBotTrItem::getList(array_merge($filterParams['filter'],array('limit' => $pages->items_per_page,'offset' => $pages->low)));
    $tpl->set('items',$items);
}

$filterParams['input_form']->form_action = erLhcoreClassDesign::baseurl('genericbot/listtranslationsitems');
$tpl->set('input',$filterParams['input_form']);
$tpl->set('inputAppend',$append);

$Result['content'] = $tpl->fetch();

$Result['path'] = array(
    array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/new','System configuration')),
    array('url' => erLhcoreClassDesign::baseurl('genericbot/listtranslations'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/edit','Bot individualization')),
    array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/edit','Translations items')),
);

?>
