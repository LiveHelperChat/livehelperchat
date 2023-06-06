<?php

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.cannedmsg', array());

if (isset($_POST['DeleteSelected']) && !empty($_POST['canned_id']) && $currentUser->hasAccessTo('lhchat','administratecannedmsg')) {
    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        erLhcoreClassModule::redirect('chat/cannedmsg');
        exit;
    }
    $db = ezcDbInstance::get();
    foreach ($_POST['canned_id'] as $canned_id) {
        $cannedMessage = erLhcoreClassModelCannedMsg::fetch($canned_id);
        if ($cannedMessage instanceof erLhcoreClassModelCannedMsg) {
            $cannedMessage->removeThis();
        }
    }

    if (isset($_SERVER['HTTP_REFERER'])) {
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
}

$tpl = erLhcoreClassTemplate::getInstance( 'lhchat/cannedmsg.tpl.php');

$validTabs = array('cannedmsg','statistic');
$tab = isset($Params['user_parameters_unordered']['tab']) && in_array($Params['user_parameters_unordered']['tab'],$validTabs) ? $Params['user_parameters_unordered']['tab'] : 'cannedmsg';
$tpl->set('tab',$tab);

if ($tab == 'cannedmsg') {

    /**
     * Append user departments filter
     * */
    $departmentParams = array();

    $userDepartments = true;

    if (!$currentUser->hasAccessTo('lhchat','explorecannedmsg_all')) {
        $userDepartments = erLhcoreClassUserDep::parseUserDepartmetnsForFilter($currentUser->getUserID(), $currentUser->cache_version);
        if ($userDepartments !== true) {
            $customFilter = '(`lh_canned_msg`.`id` IN (SELECT `canned_id` FROM `lh_canned_msg_dep` WHERE `dep_id` IN (' . implode(',', $userDepartments) . ')))';
            if ($currentUser->hasAccessTo('lhcannedmsg', 'see_global')) {
                $customFilter = '(' . $customFilter . ' OR department_id = 0)';
            }
            $departmentParams['customfilter'][] = $customFilter;
        }
    }

    if ($currentUser->hasAccessTo('lhchat','administratecannedmsg') && is_numeric($Params['user_parameters_unordered']['id']) && $Params['user_parameters_unordered']['action'] == 'delete') {

        // Delete selected canned message
        try {

            if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
                die('Invalid CSRF Token');
                exit;
            }

            $Msg = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelCannedMsg', (int)$Params['user_parameters_unordered']['id']);

            if ($userDepartments === true || empty(array_diff($Msg->department_ids_front, $userDepartments))) {
                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.cannedmsg_before_remove',array('msg' => & $Msg));
                $Msg->removeThis();

                erLhcoreClassLog::logObjectChange(array(
                    'object' => $Msg,
                    'check_log' => true,
                    'action' => 'Delete',
                    'msg' => array(
                        'delete' => $Msg->getState(),
                        'user_id' => $currentUser->getUserID()
                    )
                ));
            }

        } catch (Exception $e) {
            // Do nothing
        }

        if (isset($_SERVER['HTTP_REFERER'])) {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        } else {
            erLhcoreClassModule::redirect('chat/cannedmsg');
        }

        exit;
    }

    if (isset($_GET['doSearch']) || isset($_POST['DeleteSelected'])) {
        $filterParams = erLhcoreClassSearchHandler::getParams(array('use_post' => isset($_POST['DeleteSelected']), 'module' => 'chat','module_file' => 'canned_search','format_filter' => true, 'use_override' => true, 'uparams' => $Params['user_parameters_unordered']));
        $filterParams['is_search'] = true;
    } else {
        $filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'chat','module_file' => 'canned_search','format_filter' => true, 'uparams' => $Params['user_parameters_unordered']));
        $filterParams['is_search'] = false;
    }

    erLhcoreClassChatStatistic::formatUserFilter($filterParams);

    if (is_array($filterParams['input_form']->department_id) && !empty($filterParams['input_form']->department_id)) {
        $filterParams['filter']['innerjoin']['lh_canned_msg_dep'] = array('`lh_canned_msg_dep`.`canned_id`','`lh_canned_msg` . `id`');
        $filterParams['filter']['filterin']['`lh_canned_msg_dep`.`dep_id`'] = $filterParams['input_form']->department_id;
    }

    if (is_array($filterParams['input_form']->subject_id) && !empty($filterParams['input_form']->subject_id)) {
        $filterParams['filter']['innerjoin']['lh_canned_msg_subject'] = array('`lh_canned_msg_subject`.`canned_id`','`lh_canned_msg`.`id`');
        $filterParams['filter']['filterin']['`lh_canned_msg_subject`.`subject_id`'] = $filterParams['input_form']->subject_id;
    }

    $append = erLhcoreClassSearchHandler::getURLAppendFromInput($filterParams['input_form']);

    if (isset($_GET['export'])) {
        erLhcoreClassChatExport::exportCannedMessages(erLhcoreClassModelCannedMsg::getList(array_merge_recursive($filterParams['filter'],array('offset' => 0, 'limit' => false),$departmentParams)));
    }

    if ($currentUser->hasAccessTo('lhchat','administratecannedmsg') && isset($_GET['quick_action'])) {
        $tpl = erLhcoreClassTemplate::getInstance('lhchat/cannedmsg/quick_actions.tpl.php');
        $tpl->set('action_url', erLhcoreClassDesign::baseurl('chat/cannedmsg') . erLhcoreClassSearchHandler::getURLAppendFromInput($filterParams['input_form']));
        $tpl->set('update_records',erLhcoreClassModelCannedMsg::getCount(array_merge_recursive($filterParams['filter'],array('offset' => 0, 'limit' => false),$departmentParams)));

        if (ezcInputForm::hasPostData()) {
            if ((isset($_POST['disable_canned']) && $_POST['disable_canned'] == 'on') ||
                (isset($_POST['enable_canned']) && $_POST['enable_canned'] == 'on')
            ) {
                $q = ezcDbInstance::get()->createUpdateQuery();
                $conditions = erLhcoreClassModelCannedMsg::getConditions($filterParams['filter'], $q);
                $q->update( 'lh_canned_msg' )
                    ->set( 'disabled', (isset($_POST['disable_canned']) && $_POST['disable_canned'] == 'on' ? 1 : 0))
                    ->where(
                        $conditions
                    );
                $stmt = $q->prepare();
                $stmt->execute();
            }
            $tpl->set('updated', true);
        }


        echo $tpl->fetch();
        exit;
    }

    $pages = new lhPaginator();
    $pages->serverURL = erLhcoreClassDesign::baseurl('chat/cannedmsg') . $append;
    $pages->items_total = erLhcoreClassModelCannedMsg::getCount(array_merge_recursive($filterParams['filter'],$departmentParams));
    $pages->setItemsPerPage(20);
    $pages->paginate();

    $items = array();
    if ($pages->items_total > 0) {
        $items = erLhcoreClassModelCannedMsg::getList(array_merge_recursive($filterParams['filter'],array('offset' => $pages->low, 'limit' => $pages->items_per_page),$departmentParams));
    }

    $filterParams['input_form']->form_action = erLhcoreClassDesign::baseurl('chat/cannedmsg');

    $tpl->set('items',$items);
    $tpl->set('pages',$pages);
    $tpl->set('input',$filterParams['input_form']);
    $tpl->set('inputAppend',$append);
} else {
    /**
     * Append user departments filter
     * */
    $departmentParams = array();
    $userDepartments = erLhcoreClassUserDep::parseUserDepartmetnsForFilter($currentUser->getUserID(), $currentUser->cache_version);
    if ($userDepartments !== true){
        $departmentParams['filterin']['department_id'] = $userDepartments;

        if ($currentUser->hasAccessTo('lhcannedmsg','see_global')) {
            $departmentParams['filterin']['department_id'][] = 0;
        }
    }

    if (isset($_GET['doSearch'])) {
        $filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'chat','module_file' => 'canned_search_statistic', 'format_filter' => true, 'use_override' => true, 'uparams' => $Params['user_parameters_unordered']));
        $filterParams['is_search'] = true;
    } else {
        $filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'chat','module_file' => 'canned_search_statistic', 'format_filter' => true, 'uparams' => $Params['user_parameters_unordered']));
        $filterParams['is_search'] = false;
    }

    erLhcoreClassChatStatistic::formatUserFilter($filterParams);

    if (is_array($filterParams['input_form']->department_id) && !empty($filterParams['input_form']->department_id)) {
        $filterParams['filter']['innerjoin']['lh_canned_msg_dep'] = array('`lh_canned_msg_dep`.`canned_id`','`lh_canned_msg_use` . `canned_id`');
        $filterParams['filter']['filterin']['`lh_canned_msg_dep`.`dep_id`'] = $filterParams['input_form']->department_id;
    }

    $append = erLhcoreClassSearchHandler::getURLAppendFromInput($filterParams['input_form']);

    if (isset($_GET['export']) && $_GET['export'] == 1) {
        erLhcoreClassChatStatistic::cannedStatistic(30,$filterParams['filter'], ['offset' => 0, 'action' => 'export']);
    }

    $pages = new lhPaginator();
    $pages->serverURL = erLhcoreClassDesign::baseurl('chat/cannedmsg') . '/(tab)/statistic' . $append;
    $pages->items_total = erLhcoreClassChatStatistic::cannedStatistic(30,$filterParams['filter'], ['action' => 'count']);
    $pages->setItemsPerPage(20);
    $pages->paginate();

    $filterParams['input_form']->form_action = erLhcoreClassDesign::baseurl('chat/cannedmsg') . '/(tab)/statistic';
    $tpl->set('items',erLhcoreClassChatStatistic::cannedStatistic(30,$filterParams['filter'], ['offset' => $pages->low, 'action' => 'list']));
    $tpl->set('pages',$pages);
    $tpl->set('input_statistic',$filterParams['input_form']);
    $tpl->set('inputAppend',$append);
}

$Result['content'] = $tpl->fetch();
$Result['path'] = array(
array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','System configuration')),
array('url' => erLhcoreClassDesign::baseurl('chat/cannedmsg'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Canned messages')));

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.cannedmsg_path',array('result' => & $Result));

?>