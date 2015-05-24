<?php

/**
 * This is optional if some extension AH decides to block usage of this module function completely
 * We don't do redirect here
 * */
$response = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.statistic', array());

$tpl = erLhcoreClassTemplate::getInstance( 'lhstatistic/statistic.tpl.php');

$validTabs = array('active','total','last24','chatsstatistic');

$tab = isset($Params['user_parameters_unordered']['tab']) && in_array($Params['user_parameters_unordered']['tab'],$validTabs) ? $Params['user_parameters_unordered']['tab'] : 'active';

if ($tab == 'active') {
    
    if (isset($_GET['doSearch'])) {
    	$filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'chat','module_file' => 'chat_search','format_filter' => true, 'use_override' => true, 'uparams' => $Params['user_parameters_unordered']));
    } else {
    	$filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'chat','module_file' => 'chat_search','format_filter' => true, 'uparams' => $Params['user_parameters_unordered']));
    }
    
    $tpl->set('input',$filterParams['input_form']);
    
    if (isset($_GET['xmlavguser'])) {
        erLhcoreClassChatStatistic::exportAverageOfChatsDialogsByUser(30,$filterParams['filter']);
        exit;
    }
    
    $tpl->setArray(array(
        'userStats' => erLhcoreClassChatStatistic::getRatingByUser(30,$filterParams['filter']),
        'countryStats' => erLhcoreClassChatStatistic::getTopChatsByCountry(30,$filterParams['filter']),
        'userChatsStats' => erLhcoreClassChatStatistic::numberOfChatsDialogsByUser(30,$filterParams['filter']),
        'userChatsAverageStats' => erLhcoreClassChatStatistic::averageOfChatsDialogsByUser(30,$filterParams['filter']),
        'userWaitTimeByOperator' => erLhcoreClassChatStatistic::avgWaitTimeyUser(30,$filterParams['filter']),
        'numberOfChatsPerMonth' => erLhcoreClassChatStatistic::getNumberOfChatsPerMonth($filterParams['filter']),
        'numberOfChatsPerWaitTimeMonth' => erLhcoreClassChatStatistic::getNumberOfChatsWaitTime($filterParams['filter']),
        'numberOfChatsPerHour' => erLhcoreClassChatStatistic::getWorkLoadStatistic($filterParams['filter']),
        'averageChatTime' => erLhcoreClassChatStatistic::getAverageChatduration(30,$filterParams['filter']),
        'numberOfMsgByUser' => erLhcoreClassChatStatistic::numberOfMessagesByUser(30,$filterParams['filter']),
        'urlappend' => erLhcoreClassSearchHandler::getURLAppendFromInput($filterParams['input_form'])
    ));
    
} elseif ($tab == 'chatsstatistic') {
    
    if (isset($_GET['doSearch'])) {
    	$filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'chat','module_file' => 'chatsstatistic_tab','format_filter' => true, 'use_override' => true, 'uparams' => $Params['user_parameters_unordered']));
    } else {
    	$filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'chat','module_file' => 'chatsstatistic_tab','format_filter' => true, 'uparams' => $Params['user_parameters_unordered']));
    }
    
    $tpl->set('input',$filterParams['input_form']);

    $tpl->set('groupby',$filterParams['input_form']->groupby == 1 ? 'Y.m.d' : 'Y.m');
    
    if ($filterParams['input_form']->groupby == 1) {
        $tpl->setArray(array(
            'numberOfChatsPerMonth' => erLhcoreClassChatStatistic::getNumberOfChatsPerDay($filterParams['filter']),
            'numberOfChatsPerWaitTimeMonth' => erLhcoreClassChatStatistic::getNumberOfChatsWaitTimePerDay($filterParams['filter']),
            'urlappend' => erLhcoreClassSearchHandler::getURLAppendFromInput($filterParams['input_form'])
        ));
    } else {
        $tpl->setArray(array(
            'numberOfChatsPerMonth' => erLhcoreClassChatStatistic::getNumberOfChatsPerMonth($filterParams['filter']),
            'numberOfChatsPerWaitTimeMonth' => erLhcoreClassChatStatistic::getNumberOfChatsWaitTime($filterParams['filter']),
            'urlappend' => erLhcoreClassSearchHandler::getURLAppendFromInput($filterParams['input_form'])
        ));
    }
     
} else if ($tab == 'last24') {
    
    if (isset($_GET['doSearch'])) {
        $filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'chat','module_file' => 'last24statistic','format_filter' => true, 'use_override' => true, 'uparams' => $Params['user_parameters_unordered']));
    } else {
        $filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'chat','module_file' => 'last24statistic','format_filter' => true, 'uparams' => array()));
    }

    if (empty($filterParams['filter'])) {
        $filter24 = array('filtergte' => array('time' => (time()-(24*3600))));
    } else {
        $filter24 = $filterParams['filter'];
    }
        
    $tpl->set('input',$filterParams['input_form']);
    $tpl->set('filter24',$filter24);
    
}

$tpl->set('tab',$tab);

$Result['content'] = $tpl->fetch();
$Result['path'] = array(array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Statistic')));

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.statistic_path',array('result' => & $Result));
?>