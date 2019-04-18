<?php

/**
 * This is optional if some extension AH decides to block usage of this module function completely
 * We don't do redirect here
 * */
$response = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.statistic', array());

$tpl = erLhcoreClassTemplate::getInstance( 'lhstatistic/statistic.tpl.php');

$validTabs = array('active','total','last24','chatsstatistic','agentstatistic','performance','departments');

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
    }

    erLhcoreClassChatStatistic::formatUserFilter($filterParams);

    $tpl->set('input',$filterParams['input_form']);

    if (isset($_GET['xmlavguser'])) {
        erLhcoreClassChatStatistic::exportAverageOfChatsDialogsByUser(30,$filterParams['filter']);
        exit;
    }

    if (isset($_GET['doSearch'])) {

        $activeStats = array(
            'userStats' => erLhcoreClassChatStatistic::getRatingByUser(30,$filterParams['filter']),
            'countryStats' => erLhcoreClassChatStatistic::getTopChatsByCountry(30,$filterParams['filter']),
            'userChatsStats' => erLhcoreClassChatStatistic::numberOfChatsDialogsByUser(30,$filterParams['filter']),
            'depChatsStats' => erLhcoreClassChatStatistic::numberOfChatsDialogsByDepartment(30,$filterParams['filter']),
            'userChatsAverageStats' => erLhcoreClassChatStatistic::averageOfChatsDialogsByUser(30,$filterParams['filter']),
            'userWaitTimeByOperator' => erLhcoreClassChatStatistic::avgWaitTimeyUser(30,$filterParams['filter']),
            'numberOfChatsPerMonth' => erLhcoreClassChatStatistic::getNumberOfChatsPerMonth($filterParams['filter'], array('comparetopast' => $filterParams['input']->comparetopast)),
            'numberOfChatsPerWaitTimeMonth' => erLhcoreClassChatStatistic::getNumberOfChatsWaitTime($filterParams['filter']),
            'numberOfChatsPerHour' => erLhcoreClassChatStatistic::getWorkLoadStatistic(30, $filterParams['filter']),
            'averageChatTime' => erLhcoreClassChatStatistic::getAverageChatduration(30,$filterParams['filter']),
            'numberOfMsgByUser' => erLhcoreClassChatStatistic::numberOfMessagesByUser(30,$filterParams['filter']),
            'subjectsStatistic' => erLhcoreClassChatStatistic::subjectsStatistic(30,$filterParams['filter']),
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
    }
    
    erLhcoreClassChatStatistic::formatUserFilter($filterParams);
    
    $tpl->set('input',$filterParams['input_form']);
    $tpl->set('groupby',$filterParams['input_form']->groupby == 1 ? 'Y.m.d' : ($filterParams['input_form']->groupby == 2 ? 'Y-m-d' : 'Y.m'));

    if (isset($_GET['doSearch'])) {                    
        if ($filterParams['input_form']->groupby == 1) {
            $tpl->setArray(array(
                'numberOfChatsPerMonth' => erLhcoreClassChatStatistic::getNumberOfChatsPerDay($filterParams['filter']),
                'numberOfChatsPerWaitTimeMonth' => erLhcoreClassChatStatistic::getNumberOfChatsWaitTimePerDay($filterParams['filter']),
                'urlappend' => erLhcoreClassSearchHandler::getURLAppendFromInput($filterParams['input_form'])
            ));
        } elseif ($filterParams['input_form']->groupby == 2) {
            $tpl->setArray(array(
                'numberOfChatsPerMonth' => erLhcoreClassChatStatistic::getNumberOfChatsPerWeek($filterParams['filter']),
                'numberOfChatsPerWaitTimeMonth' => erLhcoreClassChatStatistic::getNumberOfChatsWaitTimePerWeek($filterParams['filter']),
                'urlappend' => erLhcoreClassSearchHandler::getURLAppendFromInput($filterParams['input_form'])
            ));
        } else {
            $tpl->setArray(array(
                'numberOfChatsPerMonth' => erLhcoreClassChatStatistic::getNumberOfChatsPerMonth($filterParams['filter']),
                'numberOfChatsPerWaitTimeMonth' => erLhcoreClassChatStatistic::getNumberOfChatsWaitTime($filterParams['filter']),
                'urlappend' => erLhcoreClassSearchHandler::getURLAppendFromInput($filterParams['input_form'])
            ));
        }
    }
    
} else if ($tab == 'last24') {
    
    if (isset($_GET['doSearch'])) {
        switch ($_GET['doSearch']) {
            case 'Today':
                $_GET['timefrom'] = date('Y-m-d', time());
                $_GET['timefrom_hours'] = 0;
                $_GET['timefrom_minutes'] = 0;
                break;
            case 'Week':
                $_GET['timefrom'] = date('Y-m-d', strtotime('this week'));
                $_GET['timefrom_hours'] = 0;
                $_GET['timefrom_minutes'] = 0;
                break;
            case 'Month':
                $_GET['timefrom'] = date('Y-m-d', strtotime('first day of this month'));
                $_GET['timefrom_hours'] = 0;
                $_GET['timefrom_minutes'] = 0;
                break;
            case 'Year':
                $_GET['timefrom'] = date('Y-01-01');
                $_GET['timefrom_hours'] = 0;
                $_GET['timefrom_minutes'] = 0;
                break;
            default:
                break;
        }
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