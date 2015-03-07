<?php

/**
 * This is optional if some extension AH decides to block usage of this module function completely
 * We don't do redirect here
 * */
$response = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.statistic', array());

$tpl = erLhcoreClassTemplate::getInstance( 'lhchat/statistic.tpl.php');

if (isset($_GET['doSearch'])) {
	$filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'chat','module_file' => 'chat_search','format_filter' => true, 'use_override' => true, 'uparams' => $Params['user_parameters_unordered']));
} else {
	$filterParams = erLhcoreClassSearchHandler::getParams(array('module' => 'chat','module_file' => 'chat_search','format_filter' => true, 'uparams' => array()));
}

$tpl->set('input',$filterParams['input_form']);

$tpl->setArray(array(
	'userStats' => erLhcoreClassChatStatistic::getRatingByUser(30,$filterParams['filter']),
	'countryStats' => erLhcoreClassChatStatistic::getTopChatsByCountry(30,$filterParams['filter']),
	'userChatsStats' => erLhcoreClassChatStatistic::numberOfChatsDialogsByUser(30,$filterParams['filter']),
	'userWaitTimeByOperator' => erLhcoreClassChatStatistic::avgWaitTimeyUser(30,$filterParams['filter']),
	'numberOfChatsPerMonth' => erLhcoreClassChatStatistic::getNumberOfChatsPerMonth($filterParams['filter']),
	'numberOfChatsPerWaitTimeMonth' => erLhcoreClassChatStatistic::getNumberOfChatsWaitTime($filterParams['filter']),
	'numberOfChatsPerHour' => erLhcoreClassChatStatistic::getWorkLoadStatistic($filterParams['filter']),
	'numberOfMsgByUser' => erLhcoreClassChatStatistic::numberOfMessagesByUser(30,$filterParams['filter']))
);
    	
$Result['content'] = $tpl->fetch();
$Result['path'] = array(array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/statistic','Statistic')))

?>