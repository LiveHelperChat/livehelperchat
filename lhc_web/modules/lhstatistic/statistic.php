<?php

/**
 * This is optional if some extension AH decides to block usage of this module function completely
 * We don't do redirect here
 * */
$response = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.statistic', array());

// Custom Unordered Parameters
erLhcoreClassChatEventDispatcher::getInstance()->dispatch('statistic.uparams_append',array('uparams' => & $Params['user_parameters_unordered']));

try {
    $dt = new DateTime();
    $offset = $dt->format("P");
    $db = ezcDbInstance::get();
    $db->query("SET LOCAL time_zone='" . $offset ."'");
} catch (Exception $e) {
    // Ignore
}

$tpl = erLhcoreClassTemplate::getInstance( 'lhstatistic/statistic.tpl.php');

$validTabs = array('visitors','active','total','last24','chatsstatistic','agentstatistic','performance','departments','configuration');

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('statistic.valid_tabs', array(
    'valid_tabs' => & $validTabs
));

$tab = isset($Params['user_parameters_unordered']['tab']) && in_array($Params['user_parameters_unordered']['tab'],$validTabs) ? $Params['user_parameters_unordered']['tab'] : 'active';

// We do not need a session anymore
session_write_close();

if ($Params['user_parameters_unordered']['report'] > 0) {
    $savedSearchPresent = \LiveHelperChat\Models\Statistic\SavedReport::fetch($Params['user_parameters_unordered']['report']);
    if (!is_object($savedSearchPresent) || $savedSearchPresent->user_id != $currentUser->getUserID()) {
        $Params['user_parameters_unordered']['report'] = null;
    }
}

function reportModal($filterParams, $Params, $tab, $currentUser) {
    $savedSearch = new \LiveHelperChat\Models\Statistic\SavedReport();

    if ($Params['user_parameters_unordered']['report'] > 0) {
        $savedSearchPresent = \LiveHelperChat\Models\Statistic\SavedReport::fetch($Params['user_parameters_unordered']['report']);
        if (is_object($savedSearchPresent) && $savedSearchPresent->user_id == $currentUser->getUserID()) {
            $savedSearch = $savedSearchPresent;
        }
    }

    $tpl = erLhcoreClassTemplate::getInstance('lhstatistic/report_save.tpl.php');
    $tpl->set('action_url', erLhcoreClassDesign::baseurl('statistic/statistic').'/(tab)/'. $tab . erLhcoreClassSearchHandler::getURLAppendFromInput($filterParams['input_form']));
    if (ezcInputForm::hasPostData()) {

        $Errors = \LiveHelperChat\Validators\ReportValidator::validateReport($savedSearch, array(
                'filter' => $filterParams['filter'],
                'tab' => $tab,
                'input_form' => $filterParams['input_form'])
        );

        if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
            $Errors[] = 'Invalid CSRF token!';
        }

        if (empty($Errors)) {
            $savedSearch->user_id = $currentUser->getUserID();
            if (isset($_POST['report_save_action']) && $_POST['report_save_action'] == 'new') {
                $savedSearch->id = null;
            }
            $savedSearch->saveThis();
            $tpl->set('updated', true);
        } else {
            $tpl->set('errors', $Errors);
        }

    } elseif ($savedSearch->id === null) {
        $savedSearch->params = json_encode(array(
                'filter' => $filterParams['filter'],
                'tab' => 'active',
                'input_form' => $filterParams['input_form'])
        );
    }

    $tpl->set('item', $savedSearch);
    echo $tpl->fetch();
    exit;
}

if ($tab == 'active') {
    
    if (isset($_GET['doSearch'])) {
    	$filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'chat','module_file' => 'activestatistic_tab','format_filter' => true, 'use_override' => true, 'uparams' => $Params['user_parameters_unordered']));
    } else {
    	$filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'chat','module_file' => 'activestatistic_tab','format_filter' => true, 'uparams' => $Params['user_parameters_unordered']));
        if (empty($filterParams['input_form']->chart_type)) {
            $configuration = (array)erLhcoreClassModelChatConfig::fetch('statistic_options')->data;
            $filterParams['input_form']->chart_type = isset($configuration['statistic']) ? $configuration['statistic'] : array();
        }
    }

    erLhcoreClassChatStatistic::formatUserFilter($filterParams);

    // Global filters
    $departmentFilter = erLhcoreClassUserDep::conditionalDepartmentFilter();

    if (!empty($departmentFilter)){
        if (isset($filterParams['filter']['filterin']['lh_chat.dep_id'])) {
            $filterParams['filter']['filterin']['lh_chat.dep_id'] = array_values(array_intersect($filterParams['filter']['filterin']['lh_chat.dep_id'],$departmentFilter['filterin']['id']));
            if (empty($filterParams['filter']['filterin']['lh_chat.dep_id'])) {
                $filterParams['filter']['filterin']['lh_chat.dep_id'] = array(-1);
            }
        } else {
            $filterParams['filter']['filterin']['lh_chat.dep_id'] = array_values($departmentFilter['filterin']['id']);
        }
    }

    $userFilterDefault = erLhcoreClassGroupUser::getConditionalUserFilter();

    if (!empty($userFilterDefault)) {
        if (isset($filterParams['filter']['filterin']['lh_chat.user_id'])) {
            $filterParams['filter']['filterin']['lh_chat.user_id'] = array_values(array_intersect($filterParams['filter']['filterin']['lh_chat.user_id'],$userFilterDefault['filterin']['id']));
        } else {
            $filterParams['filter']['filterin']['lh_chat.user_id'] = array_values($userFilterDefault['filterin']['id']);
        }
    }

    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('statistic.active_filter',array('filter' => & $filterParams, 'uparams' => $Params['user_parameters_unordered']));

    $tpl->set('input',$filterParams['input_form']);

    if (isset($_GET['xmlavguser'])) {
        erLhcoreClassChatStatistic::exportAverageOfChatsDialogsByUser(30,$filterParams['filter']);
        exit;
    }

    if (isset($Params['user_parameters_unordered']['export']) && $Params['user_parameters_unordered']['export'] == 1) {
        reportModal($filterParams, $Params, 'active', $currentUser);
    }

    if (isset($_GET['doSearch'])) {

        $activeStats = array(
            'userStats' =>  ((is_array($filterParams['input_form']->chart_type) && in_array('thumbs',$filterParams['input_form']->chart_type)) ? erLhcoreClassChatStatistic::getRatingByUser(30,$filterParams['filter']) : array()),
            'countryStats' => ((is_array($filterParams['input_form']->chart_type) && in_array('country',$filterParams['input_form']->chart_type)) ? erLhcoreClassChatStatistic::getTopChatsByCountry(30,$filterParams['filter']) : array()),
            'userChatsStats' => ((is_array($filterParams['input_form']->chart_type) && in_array('chatbyuser',$filterParams['input_form']->chart_type)) ? erLhcoreClassChatStatistic::numberOfChatsDialogsByUser(30,$filterParams['filter']) : array()),
            'userChatsParticipantStats' => ((is_array($filterParams['input_form']->chart_type) && in_array('chatbyuserparticipant',$filterParams['input_form']->chart_type)) ? erLhcoreClassChatStatistic::numberOfChatsDialogsByUserParticipant(30,$filterParams['filter']) : array()),

            'userTransferChatsStats' => ((is_array($filterParams['input_form']->chart_type) && in_array('chatbytransferuser',$filterParams['input_form']->chart_type)) ? erLhcoreClassChatStatistic::numberOfChatsDialogsByUser(30,$filterParams['filter'],'transfer_uid') : array()),
            'depChatsStats' => ((is_array($filterParams['input_form']->chart_type) && in_array('chatbydep',$filterParams['input_form']->chart_type)) ? erLhcoreClassChatStatistic::numberOfChatsDialogsByDepartment(30,$filterParams['filter']) : array()),
            'userChatsAverageStats' => ((is_array($filterParams['input_form']->chart_type) && in_array('avgdurationop',$filterParams['input_form']->chart_type)) ? erLhcoreClassChatStatistic::averageOfChatsDialogsByUser(30,$filterParams['filter']) : array()),
            'userWaitTimeByOperator' => ((is_array($filterParams['input_form']->chart_type) && in_array('waitbyoperator',$filterParams['input_form']->chart_type)) ? erLhcoreClassChatStatistic::avgWaitTimeyUser(30,$filterParams['filter']) : array()),

            'numberOfChatsPerMonth' => (
                (is_array($filterParams['input_form']->chart_type) && (
                    in_array('active',$filterParams['input_form']->chart_type) ||
                    in_array('proactivevsdefault',$filterParams['input_form']->chart_type) ||
                    in_array('msgtype',$filterParams['input_form']->chart_type) ||
                    in_array('unanswered',$filterParams['input_form']->chart_type) ||
                    in_array('msgdelop',$filterParams['input_form']->chart_type) ||
                    in_array('msgdelbot',$filterParams['input_form']->chart_type)
                )
            ) ? erLhcoreClassChatStatistic::getNumberOfChatsPerMonth($filterParams['filter'], array('charttypes' => $filterParams['input_form']->chart_type, 'comparetopast' => $filterParams['input']->comparetopast)) : array()),

            'numberOfChatsPerWaitTimeMonth' => ((is_array($filterParams['input_form']->chart_type) && in_array('waitmonth',$filterParams['input_form']->chart_type)) ? erLhcoreClassChatStatistic::getNumberOfChatsWaitTime($filterParams['filter']) : array()),
            'numberOfChatsPerHour' => ((is_array($filterParams['input_form']->chart_type) && in_array('avgduration',$filterParams['input_form']->chart_type)) ? erLhcoreClassChatStatistic::getWorkLoadStatistic(30, $filterParams['filter']) : array()),
            'averageChatTime' => ((is_array($filterParams['input_form']->chart_type) && in_array('avgduration',$filterParams['input_form']->chart_type)) ? erLhcoreClassChatStatistic::getAverageChatduration(30,$filterParams['filter']) : array()),
            'numberOfMsgByUser' => ((is_array($filterParams['input_form']->chart_type) && in_array('usermsg',$filterParams['input_form']->chart_type)) ? erLhcoreClassChatStatistic::numberOfMessagesByUser(30,$filterParams['filter']) : array()),
            'subjectsStatistic' => ((is_array($filterParams['input_form']->chart_type) && in_array('subject',$filterParams['input_form']->chart_type)) ? erLhcoreClassChatStatistic::subjectsStatistic(30,$filterParams['filter']) : array()),
            'cannedStatistic' => ((is_array($filterParams['input_form']->chart_type) && in_array('canned',$filterParams['input_form']->chart_type)) ? erLhcoreClassChatStatistic::cannedStatistic(30,$filterParams['filter']) : array()),

            'nickgroupingdate' => ((is_array($filterParams['input_form']->chart_type) && in_array('nickgroupingdate',$filterParams['input_form']->chart_type)) ? erLhcoreClassChatStatistic::nickGroupingDate(30,$filterParams['filter'], array('group_limit' => $filterParams['input']->group_limit, 'group_field' => $filterParams['input']->group_field)) : array()),
            'nickgroupingdatenick' => ((is_array($filterParams['input_form']->chart_type) && in_array('nickgroupingdatenick',$filterParams['input_form']->chart_type)) ? erLhcoreClassChatStatistic::nickGroupingDateNick(30,$filterParams['filter'], array('group_limit' => $filterParams['input']->group_limit, 'group_field' => $filterParams['input']->group_field)) : array()),

            'urlappend' => erLhcoreClassSearchHandler::getURLAppendFromInput($filterParams['input_form'])
        );

        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('statistic.process_active_tab', array(
            'active_stats' => & $activeStats,
            'filter_params' => $filterParams
        ));

        if (isset($_GET['reportType']) && $_GET['reportType'] != 'live') {
            erLhcoreClassChatStatistic::exportCSV($activeStats, $_GET['reportType']);
            exit;
        }

        $tpl->setArray($activeStats);
    }
    
} elseif ($tab == 'chatsstatistic') {
    
    if (isset($_GET['doSearch'])) {
    	$filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'chat','module_file' => 'chatsstatistic_tab','format_filter' => true, 'use_override' => true, 'uparams' => $Params['user_parameters_unordered']));
    } else {
    	$filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'chat','module_file' => 'chatsstatistic_tab','format_filter' => true, 'uparams' => $Params['user_parameters_unordered']));
        if (empty($filterParams['input_form']->chart_type)) {
            $configuration = (array)erLhcoreClassModelChatConfig::fetch('statistic_options')->data;
            $filterParams['input_form']->chart_type = isset($configuration['chat_statistic']) ? $configuration['chat_statistic'] : array();
        }
    }
    
    erLhcoreClassChatStatistic::formatUserFilter($filterParams);

    // Global filters
    $departmentFilter = erLhcoreClassUserDep::conditionalDepartmentFilter();

    if (!empty($departmentFilter)){
        if (isset($filterParams['filter']['filterin']['lh_chat.dep_id'])) {
            $filterParams['filter']['filterin']['lh_chat.dep_id'] = array_values(array_intersect($filterParams['filter']['filterin']['lh_chat.dep_id'],$departmentFilter['filterin']['id']));
            if (empty($filterParams['filter']['filterin']['lh_chat.dep_id'])) {
                $filterParams['filter']['filterin']['lh_chat.dep_id'] = array(-1);
            }
        } else {
            $filterParams['filter']['filterin']['lh_chat.dep_id'] = array_values($departmentFilter['filterin']['id']);
        }
    }

    $userFilterDefault = erLhcoreClassGroupUser::getConditionalUserFilter();

    if (!empty($userFilterDefault)) {
        if (isset($filterParams['filter']['filterin']['lh_chat.user_id'])) {
            $filterParams['filter']['filterin']['lh_chat.user_id'] = array_values(array_intersect($filterParams['filter']['filterin']['lh_chat.user_id'],$userFilterDefault['filterin']['id']));
        } else {
            $filterParams['filter']['filterin']['lh_chat.user_id'] = array_values($userFilterDefault['filterin']['id']);
        }
    }

    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('statistic.chatsstatistic_filter',array('filter' => & $filterParams, 'uparams' => $Params['user_parameters_unordered']));

    $tpl->set('input',$filterParams['input_form']);
    $tpl->set('groupby',$filterParams['input_form']->groupby == 1 ? 'Y.m.d' : ($filterParams['input_form']->groupby == 2 ? 'Y-m-d' : 'Y.m'));

    if (isset($Params['user_parameters_unordered']['export']) && $Params['user_parameters_unordered']['export'] == 1) {
        reportModal($filterParams, $Params, 'chatsstatistic', $currentUser);
    }

    if (isset($_GET['doSearch'])) {

        if ($filterParams['input_form']->groupby == 1) {
            $activeStats = array(
                'numberOfChatsPerMonth' => (
                (is_array($filterParams['input_form']->chart_type) && (
                        in_array('active',$filterParams['input_form']->chart_type) ||
                        in_array('total_chats',$filterParams['input_form']->chart_type) ||
                        in_array('proactivevsdefault',$filterParams['input_form']->chart_type) ||
                        in_array('msgtype',$filterParams['input_form']->chart_type) ||
                        in_array('unanswered',$filterParams['input_form']->chart_type) ||
                        in_array('msgdelop',$filterParams['input_form']->chart_type) ||
                        in_array('msgdelbot',$filterParams['input_form']->chart_type)
                    )
                ) ? erLhcoreClassChatStatistic::getNumberOfChatsPerDay($filterParams['filter'], array('charttypes' => $filterParams['input_form']->chart_type)) : array()),
                'numberOfChatsPerWaitTimeMonth' => ((is_array($filterParams['input_form']->chart_type) && in_array('waitmonth',$filterParams['input_form']->chart_type)) ? erLhcoreClassChatStatistic::getNumberOfChatsWaitTimePerDay($filterParams['filter']): array()),

                'nickgroupingdate' => ((is_array($filterParams['input_form']->chart_type) && in_array('nickgroupingdate',$filterParams['input_form']->chart_type)) ? erLhcoreClassChatStatistic::nickGroupingDateDay($filterParams['filter'], array('group_limit' => $filterParams['input']->group_limit, 'group_field' => $filterParams['input']->group_field)) : array()),
                'nickgroupingdatenick' => ((is_array($filterParams['input_form']->chart_type) && in_array('nickgroupingdatenick',$filterParams['input_form']->chart_type)) ? erLhcoreClassChatStatistic::nickGroupingDateNickDay($filterParams['filter'], array('group_limit' => $filterParams['input']->group_limit, 'group_field' => $filterParams['input']->group_field)) : array()),
                'by_channel' =>  ((is_array($filterParams['input_form']->chart_type) && in_array('by_channel',$filterParams['input_form']->chart_type)) ? erLhcoreClassChatStatistic::byChannel($filterParams['filter'], array('group_limit' => $filterParams['input']->group_limit, 'groupby' => $filterParams['input_form']->groupby, 'group_field' => $filterParams['input']->group_field)) : array()),
                'urlappend' => erLhcoreClassSearchHandler::getURLAppendFromInput($filterParams['input_form'])
            );
        } elseif ($filterParams['input_form']->groupby == 2) {
            $activeStats = array(
                'by_channel' =>  ((is_array($filterParams['input_form']->chart_type) && in_array('by_channel',$filterParams['input_form']->chart_type)) ? erLhcoreClassChatStatistic::byChannel($filterParams['filter'], array('group_limit' => $filterParams['input']->group_limit, 'groupby' => $filterParams['input_form']->groupby, 'group_field' => $filterParams['input']->group_field)) : array()),
                'numberOfChatsPerMonth' => (
                (is_array($filterParams['input_form']->chart_type) && (
                        in_array('active',$filterParams['input_form']->chart_type) ||
                        in_array('total_chats',$filterParams['input_form']->chart_type) ||
                        in_array('proactivevsdefault',$filterParams['input_form']->chart_type) ||
                        in_array('msgtype',$filterParams['input_form']->chart_type) ||
                        in_array('unanswered',$filterParams['input_form']->chart_type) ||
                        in_array('msgdelop',$filterParams['input_form']->chart_type) ||
                        in_array('msgdelbot',$filterParams['input_form']->chart_type)
                    )
                ) ? erLhcoreClassChatStatistic::getNumberOfChatsPerWeek($filterParams['filter'], array('charttypes' => $filterParams['input_form']->chart_type)): array()),
                'numberOfChatsPerWaitTimeMonth' => ((is_array($filterParams['input_form']->chart_type) && in_array('waitmonth',$filterParams['input_form']->chart_type)) ? erLhcoreClassChatStatistic::getNumberOfChatsWaitTimePerWeek($filterParams['filter']): array()),

                'nickgroupingdate' => ((is_array($filterParams['input_form']->chart_type) && in_array('nickgroupingdate',$filterParams['input_form']->chart_type)) ? erLhcoreClassChatStatistic::nickGroupingDateWeek($filterParams['filter'], array('group_limit' => $filterParams['input']->group_limit, 'group_field' => $filterParams['input']->group_field)) : array()),
                'nickgroupingdatenick' => ((is_array($filterParams['input_form']->chart_type) && in_array('nickgroupingdatenick',$filterParams['input_form']->chart_type)) ? erLhcoreClassChatStatistic::nickGroupingDateNickWeek($filterParams['filter'], array('group_limit' => $filterParams['input']->group_limit, 'group_field' => $filterParams['input']->group_field)) : array()),

                'urlappend' => erLhcoreClassSearchHandler::getURLAppendFromInput($filterParams['input_form'])
            );
        } elseif ($filterParams['input_form']->groupby == 3) {
            $activeStats = array(
                'by_channel' =>  ((is_array($filterParams['input_form']->chart_type) && in_array('by_channel',$filterParams['input_form']->chart_type)) ? erLhcoreClassChatStatistic::byChannel($filterParams['filter'], array('group_limit' => $filterParams['input']->group_limit, 'groupby' => $filterParams['input_form']->groupby, 'group_field' => $filterParams['input']->group_field)) : array()),
                'numberOfChatsPerMonth' => (
                (is_array($filterParams['input_form']->chart_type) && (
                        in_array('active',$filterParams['input_form']->chart_type) ||
                        in_array('total_chats',$filterParams['input_form']->chart_type) ||
                        in_array('proactivevsdefault',$filterParams['input_form']->chart_type) ||
                        in_array('msgtype',$filterParams['input_form']->chart_type) ||
                        in_array('unanswered',$filterParams['input_form']->chart_type) ||
                        in_array('msgdelop',$filterParams['input_form']->chart_type) ||
                        in_array('msgdelbot',$filterParams['input_form']->chart_type)
                    )
                ) ? erLhcoreClassChatStatistic::getNumberOfChatsPerWeekDay($filterParams['filter'], array('charttypes' => $filterParams['input_form']->chart_type)): array()),
                'numberOfChatsPerWaitTimeMonth' => ((is_array($filterParams['input_form']->chart_type) && in_array('waitmonth',$filterParams['input_form']->chart_type)) ? erLhcoreClassChatStatistic::getNumberOfChatsWaitTimePerWeekDay($filterParams['filter']): array()),

                'nickgroupingdate' => ((is_array($filterParams['input_form']->chart_type) && in_array('nickgroupingdate',$filterParams['input_form']->chart_type)) ? erLhcoreClassChatStatistic::nickGroupingDateWeekDay($filterParams['filter'], array('group_limit' => $filterParams['input']->group_limit, 'group_field' => $filterParams['input']->group_field)) : array()),
                'nickgroupingdatenick' => ((is_array($filterParams['input_form']->chart_type) && in_array('nickgroupingdatenick',$filterParams['input_form']->chart_type)) ? erLhcoreClassChatStatistic::nickGroupingDateNickWeekDay($filterParams['filter'], array('group_limit' => $filterParams['input']->group_limit, 'group_field' => $filterParams['input']->group_field)) : array()),

                'urlappend' => erLhcoreClassSearchHandler::getURLAppendFromInput($filterParams['input_form'])
            );
        } else {
            $activeStats = array(
                'by_channel' =>  ((is_array($filterParams['input_form']->chart_type) && in_array('by_channel',$filterParams['input_form']->chart_type)) ? erLhcoreClassChatStatistic::byChannel($filterParams['filter'], array('group_limit' => $filterParams['input']->group_limit, 'groupby' => $filterParams['input_form']->groupby, 'group_field' => $filterParams['input']->group_field)) : array()),
                'numberOfChatsPerMonth' => (
                (is_array($filterParams['input_form']->chart_type) && (
                        in_array('active',$filterParams['input_form']->chart_type) ||
                        in_array('total_chats',$filterParams['input_form']->chart_type) ||
                        in_array('proactivevsdefault',$filterParams['input_form']->chart_type) ||
                        in_array('msgtype',$filterParams['input_form']->chart_type) ||
                        in_array('unanswered',$filterParams['input_form']->chart_type) ||
                        in_array('msgdelop',$filterParams['input_form']->chart_type) ||
                        in_array('msgdelbot',$filterParams['input_form']->chart_type)
                    )
                ) ? erLhcoreClassChatStatistic::getNumberOfChatsPerMonth($filterParams['filter'], array('charttypes' => $filterParams['input_form']->chart_type)) : array()),
                'numberOfChatsPerWaitTimeMonth' => ((is_array($filterParams['input_form']->chart_type) && in_array('waitmonth',$filterParams['input_form']->chart_type)) ? erLhcoreClassChatStatistic::getNumberOfChatsWaitTime($filterParams['filter']) : array()),

                'nickgroupingdate' => ((is_array($filterParams['input_form']->chart_type) && in_array('nickgroupingdate',$filterParams['input_form']->chart_type)) ? erLhcoreClassChatStatistic::nickGroupingDate(30,$filterParams['filter'], array('group_limit' => $filterParams['input']->group_limit, 'group_field' => $filterParams['input']->group_field)) : array()),
                'nickgroupingdatenick' => ((is_array($filterParams['input_form']->chart_type) && in_array('nickgroupingdatenick',$filterParams['input_form']->chart_type)) ? erLhcoreClassChatStatistic::nickGroupingDateNick(30,$filterParams['filter'], array('group_limit' => $filterParams['input']->group_limit, 'group_field' => $filterParams['input']->group_field)) : array()),

                'urlappend' => erLhcoreClassSearchHandler::getURLAppendFromInput($filterParams['input_form'])
            );
        }

        if (isset($_GET['reportType']) && $_GET['reportType'] != 'live') {
            erLhcoreClassChatStatistic::exportCSV($activeStats, $_GET['reportType']);
            exit;
        }

        $tpl->setArray($activeStats);
    }
    
} else if ($tab == 'last24') {
    
    if (isset($_GET['doSearch'])) {
        $filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'chat','module_file' => 'last24statistic','format_filter' => true, 'use_override' => true, 'uparams' => $Params['user_parameters_unordered']));
    } else {
        $filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'chat','module_file' => 'last24statistic','format_filter' => true, 'uparams' => array()));
    }

    erLhcoreClassChatStatistic::formatUserFilter($filterParams);
    
    if (empty($filterParams['filter'])) {
        $filter24 = array('filtergte' => array('time' => (time()-(24*3600))));
    } else {
        $filter24 = $filterParams['filter'];
    }

    $departmentFilter = erLhcoreClassUserDep::conditionalDepartmentFilter();

    if (!empty($departmentFilter)){
        if (isset($filter24['filterin']['lh_chat.dep_id'])) {
            $filter24['filterin']['lh_chat.dep_id'] = array_values(array_intersect($filter24['filterin']['lh_chat.dep_id'],$departmentFilter['filterin']['id']));
            if (empty($filter24['filterin']['lh_chat.dep_id'])) {
                $filter24['filterin']['lh_chat.dep_id'] = array(-1);
            }
        } else {
            $filter24['filterin']['lh_chat.dep_id'] = array_values($departmentFilter['filterin']['id']);
        }
    }

    $userFilterDefault = erLhcoreClassGroupUser::getConditionalUserFilter();

    if (!empty($userFilterDefault)) {
        if (isset($filter24['filterin']['lh_chat.user_id'])) {
            $filter24['filterin']['lh_chat.user_id'] = array_values(array_intersect($filter24['filterin']['lh_chat.user_id'],$userFilterDefault['filterin']['id']));
        } else {
            $filter24['filterin']['lh_chat.user_id'] = array_values($userFilterDefault['filterin']['id']);
        }
    }

    if (isset($_GET['doSearch'])) {
        $tpl->set('last24hstatistic',erLhcoreClassChatStatistic::getLast24HStatistic($filter24));
        $tpl->set('operators',erLhcoreClassChatStatistic::getTopTodaysOperators(100,0,$filter24));
    }

    $tpl->set('input',$filterParams['input_form']);
    $tpl->set('filter24',$filter24);

    
} else if ($tab == 'agentstatistic') {

    if (isset($_GET['doSearch'])) {
        $filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'chat','module_file' => 'agent_statistic','format_filter' => true, 'use_override' => true, 'uparams' => $Params['user_parameters_unordered']));
    } else {
        $filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'chat','module_file' => 'agent_statistic','format_filter' => true, 'uparams' => $Params['user_parameters_unordered']));
    }

    $userFilterDefault = erLhcoreClassGroupUser::getConditionalUserFilter();

    if (!empty($userFilterDefault)){
        $filterParams['filter'] = array_merge_recursive($filterParams['filter'], $userFilterDefault);
    }

    if (isset($_GET['xmlagentstatistic'])) {
        erLhcoreClassChatStatistic::exportAgentStatistic(30,$filterParams['filter']);
        exit;
    }

    if (isset($Params['user_parameters_unordered']['export']) && $Params['user_parameters_unordered']['export'] == 1) {
        reportModal($filterParams, $Params, 'agentstatistic', $currentUser);
    }

    if (isset($_GET['doSearch'])) {
        $agentStatistic = erLhcoreClassChatStatistic::getAgentStatistic(30, $filterParams['filter']);
    } else {
        $agentStatistic = array();
    }

    $tpl->set('input',$filterParams['input_form']);
    $tpl->set('agentStatistic',$agentStatistic);
    $tpl->set('agentStatistic_avg',erLhcoreClassChatStatistic::getAgentStatisticSummary($agentStatistic));

    
} else if ($tab == 'performance') {

    if (isset($_GET['doSearch'])) {
        $filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'chat', 'module_file' => 'performance_statistic', 'format_filter' => true, 'use_override' => true, 'uparams' => $Params['user_parameters_unordered']));
    } else {
        $filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'chat', 'module_file' => 'performance_statistic', 'format_filter' => true, 'uparams' => $Params['user_parameters_unordered']));
    }

    erLhcoreClassChatStatistic::formatUserFilter($filterParams);

    $departmentFilter = erLhcoreClassUserDep::conditionalDepartmentFilter();

    if (!empty($departmentFilter)){
        $filterParams['filter']['customfilter'][] = '(`lh_chat`.`dep_id` IN (' . implode(',',$departmentFilter['filterin']['id']) .'))';
    }

    if (isset($Params['user_parameters_unordered']['export']) && $Params['user_parameters_unordered']['export'] == 1) {
        reportModal($filterParams, $Params, 'performance', $currentUser);
    }

    if (isset($_GET['doSearch'])) {
        $performanceStatistic = erLhcoreClassChatStatistic::getPerformanceStatistic(30, $filterParams['filter'], $filterParams);
    } else {
        $performanceStatistic = array();
    }

    $tpl->set('input', $filterParams['input_form']);
    $tpl->set('performanceStatistic', $performanceStatistic);

} else if ($tab == 'departments') {

    if (isset($_GET['doSearch'])) {
        $filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'chat','module_file' => 'departments_statistic','format_filter' => true, 'use_override' => true, 'uparams' => $Params['user_parameters_unordered']));
    } else {
        $filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'chat','module_file' => 'departments_statistic','format_filter' => true, 'uparams' => $Params['user_parameters_unordered']));
    }

    erLhcoreClassChatStatistic::formatUserFilter($filterParams, 'lh_departament_availability');

    $departmentFilter = erLhcoreClassUserDep::conditionalDepartmentFilter();

    if (!empty($departmentFilter)){
        $filterParams['filter']['customfilter'][] = '(`lh_departament_availability`.`dep_id` IN (' . implode(',',$departmentFilter['filterin']['id']) .'))';
    }

    $tpl->set('input',$filterParams['input_form']);

    if (isset($_GET['doSearch']) || $Params['user_parameters_unordered']['xls'] == 1) {
        $departmentstats = erLhcoreClassChatStatistic::getDepartmentsStatistic(30, $filterParams['filter'], $filterParams);
    } else {
        $departmentstats = array();
    }

    if ($Params['user_parameters_unordered']['xls'] == 1) {
        $departmentStats = erLhcoreClassChatStatistic::getDepartmentsStatistic(30, $filterParams['filter'], $filterParams);
        erLhcoreClassChatStatistic::exportDepartmentStatistic($departmentStats);
        exit;
    }

    $append = erLhcoreClassSearchHandler::getURLAppendFromInput($filterParams['input_form']);

    $tpl->set('input_append', $append);
    $tpl->set('departmentstats', $departmentstats);

} elseif ($tab == 'configuration') {

    $statisticOptions = erLhcoreClassModelChatConfig::fetch('statistic_options');
    $configuration = (array)$statisticOptions->data;
    if (!isset($configuration['statistic'])) {
        $configuration['statistic'] = array();
    }

    if (!isset($configuration['chat_statistic'])) {
        $configuration['chat_statistic'] = array();
    }

    if (ezcInputForm::hasPostData()) {
        
        if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
            erLhcoreClassModule::redirect();
            exit;
        }

        $definition = array(
            'chart_type' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL,  'string', null,FILTER_REQUIRE_ARRAY
            ),
            'canned_stats' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL,  'boolean'
            ),
            'chat_chart_type' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL,  'string',null,FILTER_REQUIRE_ARRAY
            ),
            'avg_wait_time' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL,  'int', array('min_range' => 5*60, 'max_range' => 4*7*24*3600)
            ),
            'avg_chat_duration' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL,  'int', array('min_range' => 5*60, 'max_range' => 4*7*24*3600)
            )
        );

        $form = new ezcInputForm( INPUT_POST, $definition );
        $Errors = array();

        if ($form->hasValidData('chart_type')) {
            $configuration['statistic'] = $form->chart_type;
        }

        if ($form->hasValidData('chat_chart_type')) {
            $configuration['chat_statistic'] = $form->chat_chart_type;
        }

        if ($form->hasValidData('avg_wait_time')) {
            $configuration['avg_wait_time'] = $form->avg_wait_time;
        } else {
            $configuration['avg_wait_time'] = 0;
        }

        if ($form->hasValidData('avg_chat_duration')) {
            $configuration['avg_chat_duration'] = $form->avg_chat_duration;
        } else {
            $configuration['avg_chat_duration'] = 0;
        }

        if ($form->hasValidData('canned_stats')) {
            $configuration['canned_stats'] = $form->canned_stats;
        } else {
            $configuration['canned_stats'] = 0;
        }

        $statisticOptions->explain = '';
        $statisticOptions->type = 0;
        $statisticOptions->hidden = 1;
        $statisticOptions->identifier = 'statistic_options';
        $statisticOptions->value = serialize($configuration);
        $statisticOptions->saveThis();

        // Need to clear cache because messages might start to collect canned messages usage statistic
        $CacheManager = erConfigClassLhCacheConfig::getInstance();
        $CacheManager->expireCache();

        $tpl->set('updated', true);
    }

    $tpl->set('configuration', $configuration);

} else if ($tab == 'visitors') {

    if (isset($_GET['doSearch'])) {
        $filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'chat','module_file' => 'visitorsstatistic_tab','format_filter' => true, 'use_override' => true, 'uparams' => $Params['user_parameters_unordered']));
    } else {
        $filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'chat','module_file' => 'visitorsstatistic_tab','format_filter' => true, 'uparams' => $Params['user_parameters_unordered']));
        $configuration = (array)erLhcoreClassModelChatConfig::fetch('statistic_options')->data;
        $filterParams['input_form']->chart_type = isset($configuration['chat_statistic']) ? $configuration['chat_statistic'] : array();
    }
    
    erLhcoreClassChatStatistic::formatUserFilter($filterParams);

    $departmentFilter = erLhcoreClassUserDep::conditionalDepartmentFilter();

    if (!empty($departmentFilter)){
        $filterParams['filter']['customfilter'][] = '(`dep_id` IN (' . implode(',',$departmentFilter['filterin']['id']) .'))';
    }

    $tpl->set('input',$filterParams['input_form']);
    $tpl->set('groupby',$filterParams['input_form']->groupby == 1 ? 'Y.m.d' : ($filterParams['input_form']->groupby == 2 ? 'Y-m-d' : 'Y.m'));

    if (isset($_GET['doSearch'])) {
        $tpl->setArray(array(
            'visitors_statistic' => erLhcoreClassChatStatistic::getVisitorsStatistic($filterParams['filter'], array('groupby' => $filterParams['input_form']->groupby,'charttypes' => $filterParams['input_form']->chart_type)),
            'urlappend' => erLhcoreClassSearchHandler::getURLAppendFromInput($filterParams['input_form'])
        ));
    }
    
} elseif ($tab == 'total') {
    $tpl->set('totalfilter',erLhcoreClassUserDep::conditionalDepartmentFilter(false,'`lh_chat`.`dep_id`'));
} else {
    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('statistic.process_tab', array(
        'tpl' => & $tpl,
        'params' => $Params
    ));
}

$tpl->set('tab',$tab);

$Result['content'] = $tpl->fetch();
$Result['path'] = array(array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Statistic')));
$Result['additional_header_js'] = '<script type="text/javascript" src="'.erLhcoreClassDesign::design('js/Chart.bundle.min.js').'"></script>';

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.statistic_path',array('result' => & $Result));
?>