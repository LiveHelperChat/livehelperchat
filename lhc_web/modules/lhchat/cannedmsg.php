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

    $countAlias = false;
    if (is_numeric($filterParams['input_form']->used_freq)) {
        if ($filterParams['input_form']->used_freq === 0) { // Zero usage
            $filterParams['filter']['customfilter'][] = '`lh_canned_msg`.`id` NOT IN (SELECT `lh_canned_msg_use`.`canned_id` FROM `lh_canned_msg_use` WHERE `lh_canned_msg_use`.`ctime` > '. (time() - 31*3600*24) . ')';
        } elseif ($filterParams['input_form']->used_freq === 1) { // Once
            $filterParams['filter']['innerjoin']['lh_canned_msg_use'] = array('`lh_canned_msg_use`.`canned_id`', '`lh_canned_msg`.`id`');
            $filterParams['filter']['filtergt']['`lh_canned_msg_use`.`ctime`'] = time() - 31*24*3600;
            $filterParams['filter']['having'] = 'count(`lh_canned_msg`.`id`) = 1';
            $filterParams['filter']['group'] = '`lh_canned_msg`.`id`';
            $countAlias = true;
        } elseif ($filterParams['input_form']->used_freq === 2) { // One or more
            $filterParams['filter']['innerjoin']['lh_canned_msg_use'] = array('`lh_canned_msg_use`.`canned_id`', '`lh_canned_msg`.`id`');
            $filterParams['filter']['filtergt']['`lh_canned_msg_use`.`ctime`'] = time() - 31*24*3600;
            $filterParams['filter']['group'] = '`lh_canned_msg`.`id`';
            $countAlias = true;
        }
    }

    $append = erLhcoreClassSearchHandler::getURLAppendFromInput($filterParams['input_form']);

    if (isset($_GET['export'])) {
        erLhcoreClassChatExport::exportCannedMessages(erLhcoreClassModelCannedMsg::getList(array_merge_recursive($filterParams['filter'],array('offset' => 0, 'limit' => false),$departmentParams)));
    }

    if ($currentUser->hasAccessTo('lhchat','administratecannedmsg') && isset($_GET['quick_action'])) {
        $tpl = erLhcoreClassTemplate::getInstance('lhchat/cannedmsg/quick_actions.tpl.php');
        $tpl->set('action_url', erLhcoreClassDesign::baseurl('chat/cannedmsg') . erLhcoreClassSearchHandler::getURLAppendFromInput($filterParams['input_form']));
        $tpl->set('update_records',erLhcoreClassModelCannedMsg::getCount(array_merge_recursive($filterParams['filter'],array('offset' => 0, 'limit' => false),$departmentParams), 'count',false,false,true,false,false, $countAlias));

        if (ezcInputForm::hasPostData()) {
            if ((isset($_POST['disable_canned']) && $_POST['disable_canned'] == 'on') ||
                (isset($_POST['enable_canned']) && $_POST['enable_canned'] == 'on')
            ) {
                foreach (erLhcoreClassModelCannedMsg::getList(array_merge_recursive($filterParams['filter'],array('offset' => 0, 'limit' => false),$departmentParams)) as $cannedMessage) {
                    $cannedMessage->disabled = (isset($_POST['disable_canned']) && $_POST['disable_canned'] == 'on' ? 1 : 0);
                    $cannedMessage->updateThis(['update' => ['disabled']]);
                }
            }

            if (isset($_POST['dep_id_remove']) && is_numeric($_POST['dep_id_remove']) && (int)$_POST['dep_id_remove'] > 0) {
                $values = [];
                $db = ezcDbInstance::get();
                foreach (erLhcoreClassModelCannedMsg::getList(array_merge_recursive($filterParams['filter'],array('offset' => 0, 'limit' => false),$departmentParams)) as $cannedMessage) {
                    $db = ezcDbInstance::get();
                    $stmt = $db->prepare('DELETE FROM `lh_canned_msg_dep` WHERE `canned_id` = :canned_id AND `dep_id` = :dep_id');
                    $stmt->bindValue(':canned_id', $cannedMessage->id,PDO::PARAM_INT);
                    $stmt->bindValue(':dep_id', (int)$_POST['dep_id_remove'],PDO::PARAM_INT);
                    $stmt->execute();
                }
            }

            if (isset($_POST['dep_id']) && is_numeric($_POST['dep_id']) && (int)$_POST['dep_id'] > 0) {
                $values = [];
                $db = ezcDbInstance::get();
                foreach (erLhcoreClassModelCannedMsg::getList(array_merge_recursive($filterParams['filter'],array('offset' => 0, 'limit' => false),$departmentParams)) as $cannedMessage) {
                    $stmt = $db->prepare('SELECT COUNT(`id`) FROM `lh_canned_msg_dep` WHERE `canned_id` = :canned_id AND `dep_id` = :dep_id');
                    $stmt->bindValue(':canned_id', $cannedMessage->id,PDO::PARAM_INT);
                    $stmt->bindValue(':dep_id', (int)$_POST['dep_id'],PDO::PARAM_INT);
                    $stmt->execute();
                    $isAssigned = $stmt->fetch(PDO::FETCH_COLUMN) > 0;

                    if ($isAssigned === false) {
                        $values[] = "(" . $cannedMessage->id . "," . (int)$_POST['dep_id'] . ")";
                    }
                }

                if (!empty($values)) {
                    $db->query('INSERT INTO `lh_canned_msg_dep` (`canned_id`,`dep_id`) VALUES ' . implode(',',$values));
                }
            }

            $tpl->set('updated', true);
        }


        echo $tpl->fetch();
        exit;
    }

    if (isset($Params['user_parameters_unordered']['export']) && $Params['user_parameters_unordered']['export'] == 4) {
        $tpl = erLhcoreClassTemplate::getInstance('lhchat/cannedmsg/delete_cannedmsg.tpl.php');
        $tpl->set('action_url', erLhcoreClassDesign::baseurl('chat/cannedmsg') . erLhcoreClassSearchHandler::getURLAppendFromInput($filterParams['input_form']));

        if (ezcInputForm::hasPostData()) {
            session_write_close();
            $filterParams['filter']['limit'] = 20;
            $filterParams['filter']['offset'] = 0;

            foreach (erLhcoreClassModelCannedMsg::getList($filterParams['filter']) as $item){
                $item->removeThis();
            }

            erLhcoreClassRestAPIHandler::setHeaders();
            echo json_encode(['left_to_delete' => erLhcoreClassModelCannedMsg::getCount($filterParams['filter'], 'count',false,false,true,false,false, $countAlias)]);
            exit;
        }

        $tpl->set('update_records',erLhcoreClassModelCannedMsg::getCount($filterParams['filter'], 'count',false,false,true,false,false, $countAlias));

        echo $tpl->fetch();
        exit;
    }

    $pages = new lhPaginator();
    $pages->serverURL = erLhcoreClassDesign::baseurl('chat/cannedmsg') . $append;
    $pages->items_total = erLhcoreClassModelCannedMsg::getCount(array_merge_recursive($filterParams['filter'],$departmentParams), 'count',false,false,true,false,false, $countAlias);
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