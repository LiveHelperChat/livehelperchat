<?php

/**
 * This is optional if some extension AH decides to block usage of this module function completely
 * We don't do redirect here
 * */
$response = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.statistic', array());

$tpl = erLhcoreClassTemplate::getInstance( 'lhstatistic/statistic.tpl.php');

$validTabs = array('visitors','active','total','last24','chatsstatistic','agentstatistic','performance','departments','configuration');

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('statistic.valid_tabs', array(
    'valid_tabs' => & $validTabs
));

$tab = isset($Params['user_parameters_unordered']['tab']) && in_array($Params['user_parameters_unordered']['tab'],$validTabs) ? $Params['user_parameters_unordered']['tab'] : 'active';

// We do not need a session anymore
session_write_close();

if ($tab == 'active') {
    
    if (isset($_GET['doSearch'])) {
    	$filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'chat','module_file' => 'activestatistic_tab','format_filter' => true, 'use_override' => true, 'uparams' => $Params['user_parameters_unordered']));
    } else {
    	$filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'chat','module_file' => 'activestatistic_tab','format_filter' => true, 'uparams' => $Params['user_parameters_unordered']));
        $configuration = (array)erLhcoreClassModelChatConfig::fetch('statistic_options')->data;
        $filterParams['input_form']->chart_type = isset($configuration['statistic']) ? $configuration['statistic'] : array();
    }

    erLhcoreClassChatStatistic::formatUserFilter($filterParams);

    // Global filters
    $departmentFilter = erLhcoreClassUserDep::conditionalDepartmentFilter();

    if (!empty($departmentFilter)){
        if (isset($filterParams['filter']['filterin']['lh_chat.dep_id'])) {
            $filterParams['filter']['filterin']['lh_chat.dep_id'] = array_intersect($filterParams['filter']['filterin']['lh_chat.dep_id'],$departmentFilter['filterin']['id']);
            if (empty($filterParams['filter']['filterin']['lh_chat.dep_id'])) {
                $filterParams['filter']['filterin']['lh_chat.dep_id'] = array(-1);
            }
        } else {
            $filterParams['filter']['filterin']['lh_chat.dep_id'] = $departmentFilter['filterin']['id'];
        }
    }

    $userFilterDefault = erLhcoreClassGroupUser::getConditionalUserFilter();

    if (!empty($userFilterDefault)) {
        if (isset($filterParams['filter']['filterin']['lh_chat.user_id'])) {
            $filterParams['filter']['filterin']['lh_chat.user_id'] = array_intersect($filterParams['filter']['filterin']['lh_chat.user_id'],$userFilterDefault['filterin']['id']);
        } else {
            $filterParams['filter']['filterin']['lh_chat.user_id'] = $userFilterDefault['filterin']['id'];
        }
    }

    $tpl->set('input',$filterParams['input_form']);

    if (isset($_GET['xmlavguser'])) {
        erLhcoreClassChatStatistic::exportAverageOfChatsDialogsByUser(30,$filterParams['filter']);
        exit;
    }

    if (isset($_GET['doSearch'])) {

        $activeStats = array(
            'userStats' =>  ((is_array($filterParams['input_form']->chart_type) && in_array('thumbs',$filterParams['input_form']->chart_type)) ? erLhcoreClassChatStatistic::getRatingByUser(30,$filterParams['filter']) : array()),
            'countryStats' => ((is_array($filterParams['input_form']->chart_type) && in_array('country',$filterParams['input_form']->chart_type)) ? erLhcoreClassChatStatistic::getTopChatsByCountry(30,$filterParams['filter']) : array()),
            'userChatsStats' => ((is_array($filterParams['input_form']->chart_type) && in_array('chatbyuser',$filterParams['input_form']->chart_type)) ? erLhcoreClassChatStatistic::numberOfChatsDialogsByUser(30,$filterParams['filter']) : array()),
            'depChatsStats' => ((is_array($filterParams['input_form']->chart_type) && in_array('chatbydep',$filterParams['input_form']->chart_type)) ? erLhcoreClassChatStatistic::numberOfChatsDialogsByDepartment(30,$filterParams['filter']) : array()),
            'userChatsAverageStats' => ((is_array($filterParams['input_form']->chart_type) && in_array('avgdurationop',$filterParams['input_form']->chart_type)) ? erLhcoreClassChatStatistic::averageOfChatsDialogsByUser(30,$filterParams['filter']) : array()),
            'userWaitTimeByOperator' => ((is_array($filterParams['input_form']->chart_type) && in_array('waitbyoperator',$filterParams['input_form']->chart_type)) ? erLhcoreClassChatStatistic::avgWaitTimeyUser(30,$filterParams['filter']) : array()),

            'numberOfChatsPerMonth' => (
                (is_array($filterParams['input_form']->chart_type) && (
                    in_array('active',$filterParams['input_form']->chart_type) ||
                    in_array('proactivevsdefault',$filterParams['input_form']->chart_type) ||
                    in_array('msgtype',$filterParams['input_form']->chart_type) ||
                    in_array('unanswered',$filterParams['input_form']->chart_type)
                )
            ) ? erLhcoreClassChatStatistic::getNumberOfChatsPerMonth($filterParams['filter'], array('charttypes' => $filterParams['input_form']->chart_type, 'comparetopast' => $filterParams['input']->comparetopast)) : array()),

            'numberOfChatsPerWaitTimeMonth' => ((is_array($filterParams['input_form']->chart_type) && in_array('waitmonth',$filterParams['input_form']->chart_type)) ? erLhcoreClassChatStatistic::getNumberOfChatsWaitTime($filterParams['filter']) : array()),
            'numberOfChatsPerHour' => ((is_array($filterParams['input_form']->chart_type) && in_array('avgduration',$filterParams['input_form']->chart_type)) ? erLhcoreClassChatStatistic::getWorkLoadStatistic(30, $filterParams['filter']) : array()),
            'averageChatTime' => ((is_array($filterParams['input_form']->chart_type) && in_array('avgduration',$filterParams['input_form']->chart_type)) ? erLhcoreClassChatStatistic::getAverageChatduration(30,$filterParams['filter']) : array()),
            'numberOfMsgByUser' => ((is_array($filterParams['input_form']->chart_type) && in_array('usermsg',$filterParams['input_form']->chart_type)) ? erLhcoreClassChatStatistic::numberOfMessagesByUser(30,$filterParams['filter']) : array()),
            'subjectsStatistic' => ((is_array($filterParams['input_form']->chart_type) && in_array('subject',$filterParams['input_form']->chart_type)) ? erLhcoreClassChatStatistic::subjectsStatistic(30,$filterParams['filter']) : array()),

            'nickgroupingdate' => ((is_array($filterParams['input_form']->chart_type) && in_array('nickgroupingdate',$filterParams['input_form']->chart_type)) ? erLhcoreClassChatStatistic::nickGroupingDate(30,$filterParams['filter'], array('group_field' => $filterParams['input']->group_field)) : array()),
            'nickgroupingdatenick' => ((is_array($filterParams['input_form']->chart_type) && in_array('nickgroupingdatenick',$filterParams['input_form']->chart_type)) ? erLhcoreClassChatStatistic::nickGroupingDateNick(30,$filterParams['filter'], array('group_field' => $filterParams['input']->group_field)) : array()),

            'urlappend' => erLhcoreClassSearchHandler::getURLAppendFromInput($filterParams['input_form'])
        );

        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('statistic.process_active_tab', array(
            'active_stats' => & $activeStats,
            'filter_params' => $filterParams
        ));

        $tpl->setArray($activeStats);
    }
    
} elseif ($tab == 'chatsstatistic') {
    
    if (isset($_GET['doSearch'])) {
    	$filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'chat','module_file' => 'chatsstatistic_tab','format_filter' => true, 'use_override' => true, 'uparams' => $Params['user_parameters_unordered']));
    } else {
    	$filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'chat','module_file' => 'chatsstatistic_tab','format_filter' => true, 'uparams' => $Params['user_parameters_unordered']));
        $configuration = (array)erLhcoreClassModelChatConfig::fetch('statistic_options')->data;
        $filterParams['input_form']->chart_type = isset($configuration['chat_statistic']) ? $configuration['chat_statistic'] : array();
    }
    
    erLhcoreClassChatStatistic::formatUserFilter($filterParams);

    // Global filters
    $departmentFilter = erLhcoreClassUserDep::conditionalDepartmentFilter();

    if (!empty($departmentFilter)){
        if (isset($filterParams['filter']['filterin']['lh_chat.dep_id'])) {
            $filterParams['filter']['filterin']['lh_chat.dep_id'] = array_intersect($filterParams['filter']['filterin']['lh_chat.dep_id'],$departmentFilter['filterin']['id']);
            if (empty($filterParams['filter']['filterin']['lh_chat.dep_id'])) {
                $filterParams['filter']['filterin']['lh_chat.dep_id'] = array(-1);
            }
        } else {
            $filterParams['filter']['filterin']['lh_chat.dep_id'] = $departmentFilter['filterin']['id'];
        }
    }

    $userFilterDefault = erLhcoreClassGroupUser::getConditionalUserFilter();

    if (!empty($userFilterDefault)) {
        if (isset($filterParams['filter']['filterin']['lh_chat.user_id'])) {
            $filterParams['filter']['filterin']['lh_chat.user_id'] = array_intersect($filterParams['filter']['filterin']['lh_chat.user_id'],$userFilterDefault['filterin']['id']);
        } else {
            $filterParams['filter']['filterin']['lh_chat.user_id'] = $userFilterDefault['filterin']['id'];
        }
    }

    $tpl->set('input',$filterParams['input_form']);
    $tpl->set('groupby',$filterParams['input_form']->groupby == 1 ? 'Y.m.d' : ($filterParams['input_form']->groupby == 2 ? 'Y-m-d' : 'Y.m'));

    if (isset($_GET['doSearch'])) {                    
        if ($filterParams['input_form']->groupby == 1) {
            $tpl->setArray(array(
                'numberOfChatsPerMonth' => (
                (is_array($filterParams['input_form']->chart_type) && (
                        in_array('active',$filterParams['input_form']->chart_type) ||
                        in_array('proactivevsdefault',$filterParams['input_form']->chart_type) ||
                        in_array('msgtype',$filterParams['input_form']->chart_type) ||
                        in_array('unanswered',$filterParams['input_form']->chart_type)
                    )
                ) ? erLhcoreClassChatStatistic::getNumberOfChatsPerDay($filterParams['filter'], array('charttypes' => $filterParams['input_form']->chart_type)) : array()),
                'numberOfChatsPerWaitTimeMonth' => ((is_array($filterParams['input_form']->chart_type) && in_array('waitmonth',$filterParams['input_form']->chart_type)) ? erLhcoreClassChatStatistic::getNumberOfChatsWaitTimePerDay($filterParams['filter']): array()),

                'nickgroupingdate' => ((is_array($filterParams['input_form']->chart_type) && in_array('nickgroupingdate',$filterParams['input_form']->chart_type)) ? erLhcoreClassChatStatistic::nickGroupingDateDay($filterParams['filter'], array('group_field' => $filterParams['input']->group_field)) : array()),
                'nickgroupingdatenick' => ((is_array($filterParams['input_form']->chart_type) && in_array('nickgroupingdatenick',$filterParams['input_form']->chart_type)) ? erLhcoreClassChatStatistic::nickGroupingDateNickDay($filterParams['filter'], array('group_field' => $filterParams['input']->group_field)) : array()),

                'urlappend' => erLhcoreClassSearchHandler::getURLAppendFromInput($filterParams['input_form'])
            ));
        } elseif ($filterParams['input_form']->groupby == 2) {
            $tpl->setArray(array(
                'numberOfChatsPerMonth' => (
                (is_array($filterParams['input_form']->chart_type) && (
                        in_array('active',$filterParams['input_form']->chart_type) ||
                        in_array('proactivevsdefault',$filterParams['input_form']->chart_type) ||
                        in_array('msgtype',$filterParams['input_form']->chart_type) ||
                        in_array('unanswered',$filterParams['input_form']->chart_type)
                    )
                ) ? erLhcoreClassChatStatistic::getNumberOfChatsPerWeek($filterParams['filter'], array('charttypes' => $filterParams['input_form']->chart_type)): array()),
                'numberOfChatsPerWaitTimeMonth' => ((is_array($filterParams['input_form']->chart_type) && in_array('waitmonth',$filterParams['input_form']->chart_type)) ? erLhcoreClassChatStatistic::getNumberOfChatsWaitTimePerWeek($filterParams['filter']): array()),

                'nickgroupingdate' => ((is_array($filterParams['input_form']->chart_type) && in_array('nickgroupingdate',$filterParams['input_form']->chart_type)) ? erLhcoreClassChatStatistic::nickGroupingDateWeek($filterParams['filter'], array('group_field' => $filterParams['input']->group_field)) : array()),
                'nickgroupingdatenick' => ((is_array($filterParams['input_form']->chart_type) && in_array('nickgroupingdatenick',$filterParams['input_form']->chart_type)) ? erLhcoreClassChatStatistic::nickGroupingDateNickWeek($filterParams['filter'], array('group_field' => $filterParams['input']->group_field)) : array()),

                'urlappend' => erLhcoreClassSearchHandler::getURLAppendFromInput($filterParams['input_form'])
            ));
        } else {
            $tpl->setArray(array(
                'numberOfChatsPerMonth' => (
                (is_array($filterParams['input_form']->chart_type) && (
                        in_array('active',$filterParams['input_form']->chart_type) ||
                        in_array('proactivevsdefault',$filterParams['input_form']->chart_type) ||
                        in_array('msgtype',$filterParams['input_form']->chart_type) ||
                        in_array('unanswered',$filterParams['input_form']->chart_type)
                    )
                ) ? erLhcoreClassChatStatistic::getNumberOfChatsPerMonth($filterParams['filter'], array('charttypes' => $filterParams['input_form']->chart_type)) : array()),
                'numberOfChatsPerWaitTimeMonth' => ((is_array($filterParams['input_form']->chart_type) && in_array('waitmonth',$filterParams['input_form']->chart_type)) ? erLhcoreClassChatStatistic::getNumberOfChatsWaitTime($filterParams['filter']) : array()),

                'nickgroupingdate' => ((is_array($filterParams['input_form']->chart_type) && in_array('nickgroupingdate',$filterParams['input_form']->chart_type)) ? erLhcoreClassChatStatistic::nickGroupingDate(30,$filterParams['filter'], array('group_field' => $filterParams['input']->group_field)) : array()),
                'nickgroupingdatenick' => ((is_array($filterParams['input_form']->chart_type) && in_array('nickgroupingdatenick',$filterParams['input_form']->chart_type)) ? erLhcoreClassChatStatistic::nickGroupingDateNick(30,$filterParams['filter'], array('group_field' => $filterParams['input']->group_field)) : array()),

                'urlappend' => erLhcoreClassSearchHandler::getURLAppendFromInput($filterParams['input_form'])
            ));
        }
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
            $filter24['filterin']['lh_chat.dep_id'] = array_intersect($filter24['filterin']['lh_chat.dep_id'],$departmentFilter['filterin']['id']);
            if (empty($filter24['filterin']['lh_chat.dep_id'])) {
                $filter24['filterin']['lh_chat.dep_id'] = array(-1);
            }
        } else {
            $filter24['filterin']['lh_chat.dep_id'] = $departmentFilter['filterin']['id'];
        }
    }

    $userFilterDefault = erLhcoreClassGroupUser::getConditionalUserFilter();

    if (!empty($userFilterDefault)) {
        if (isset($filter24['filterin']['lh_chat.user_id'])) {
            $filter24['filterin']['lh_chat.user_id'] = array_intersect($filter24['filterin']['lh_chat.user_id'],$userFilterDefault['filterin']['id']);
        } else {
            $filter24['filterin']['lh_chat.user_id'] = $userFilterDefault['filterin']['id'];
        }
    }

    $tpl->set('last24hstatistic',erLhcoreClassChatStatistic::getLast24HStatistic($filter24));    
    $tpl->set('input',$filterParams['input_form']);
    $tpl->set('filter24',$filter24);
    $tpl->set('operators',erLhcoreClassChatStatistic::getTopTodaysOperators(100,0,$filter24));
    
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
        $definition = array(
            'chart_type' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL,  'string', null,FILTER_REQUIRE_ARRAY
            ),
            'chat_chart_type' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL,  'string',null,FILTER_REQUIRE_ARRAY
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

        $statisticOptions->explain = '';
        $statisticOptions->type = 0;
        $statisticOptions->hidden = 1;
        $statisticOptions->identifier = 'statistic_options';
        $statisticOptions->value = serialize($configuration);
        $statisticOptions->saveThis();

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