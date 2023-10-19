<?php

$Module = array( "name" => "Statistic module",
				 'variable_params' => true );

$ViewList = array();

$ViewList['departmentstatusxls'] = array(
    'params' => array(),
    'functions' => array( 'exportxls' ),
    'uparams' => array()
);

$ViewList['statistic'] = array(
    'params' => array(),
    'uparams' => array('timeto_include_hours','timefrom_include_hours','invitation_id','group_chart_type','group_limit','chart_type','group_field','groupby','export','report','transfer_happened','invitation_ids','wait_time_till','wait_time_from','subject_ids','department_ids','department_group_ids','group_ids','user_ids','timeintervalto_hours', 'timeintervalfrom_hours', 'group_by','xls','tab','timefrom','timeto','department_id','user_id','group_id','department_group_id','timefrom_seconds','timefrom_minutes','timefrom_hours','timeto_hours','timeto_minutes','timeto_seconds',
        'exclude_offline',
        'with_bot',
        'dropped_chat',
        'online_offline',
        'without_bot',
        'proactive_chat',
        'no_operator',
        'has_unread_messages',
        'not_invitation',
        'has_operator',
        'abandoned_chat',
        'bot_ids',
        'cls_us',
        'has_unread_op_messages',
        'country_ids',
        'region',
        'frt_from',
        'frt_till',
        'mart_from',
        'mart_till',
        'aart_till',
        'aart_from',
        'reporthash',
        'reportts',
        'reportverified',
        'r'
        ),
    //'functions' => array( 'viewstatistic' ),
    'multiple_arguments' => array('bot_ids','subject_ids','department_ids','group_ids','user_ids','department_group_ids','invitation_ids','chart_type','country_ids')
);

$ViewList['loadreport'] = array(
    'params' => array('report_id'),
    'functions' => array( 'viewstatistic' ),
);

$ViewList['deletereport'] = array(
    'params' => array('report_id'),
    'uparams' => array('csfr'),
    'functions' => array( 'viewstatistic' ),
);

$ViewList['copyreport'] = array(
    'params' => array('report_id'),
    'uparams' => array('csfr'),
    'functions' => array( 'viewstatistic' ),
);

$ViewList['reportrange'] = array(
    'params' => array(),
    'functions' => array( 'viewstatistic' ),
);

$ViewList['campaignmodal'] = array(
    'params' => array('invitation_id'),
    'uparams' => array(),
    'functions' => array( 'viewstatistic' )
);

$ViewList['abstatistic'] = array(
    'params' => array('campaign_id'),
    'uparams' => array(),
    'functions' => array( 'viewstatistic' )
);

$ViewList['departmentstats'] = array(
    'params' => array('id'),
    'uparams' => array('type'),
    'functions' => array( 'statisticdep' )
);

$ViewList['userstats'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'userstats' )
);

$ViewList['onlinehours'] = array(
    'params' => array(),
    'uparams' => array('group_by','xls','timefrom','timeto','user_id','timefrom_minutes','timefrom_hours','timeto_hours','timeto_minutes'),
    'functions' => array( 'viewstatistic' ),
    'multiple_arguments' => array('user_id')
);

$FunctionList['exportxls'] = array('explain' => 'Allow to operator to export departments statistic in XLS');
$FunctionList['viewstatistic'] = array('explain' =>'Allow operator to view statistic');
$FunctionList['configuration'] = array('explain' =>'Allow operator to configure statistic');
$FunctionList['statisticdep'] = array('explain' =>'Allow operator to see departments/departments groups statistic in modal window');
$FunctionList['userstats'] = array('explain' =>'Allow operator to see operator statistic in modal window');

?>