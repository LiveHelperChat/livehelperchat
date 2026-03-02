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
    'uparams' => array (
        0 => 'timeto_include_hours',
        1 => 'timefrom_include_hours',
        2 => 'invitation_id',
        3 => 'group_chart_type',
        4 => 'group_limit',
        5 => 'chart_type',
        6 => 'group_field',
        7 => 'groupby',
        8 => 'export',
        9 => 'report',
        10 => 'transfer_happened',
        11 => 'invitation_ids',
        12 => 'wait_time_till',
        13 => 'wait_time_from',
        14 => 'subject_ids',
        15 => 'department_ids',
        16 => 'department_group_ids',
        17 => 'group_ids',
        18 => 'user_ids',
        19 => 'timeintervalto_hours',
        20 => 'timeintervalfrom_hours',
        21 => 'group_by',
        22 => 'xls',
        23 => 'tab',
        24 => 'timefrom',
        25 => 'timeto',
        26 => 'department_id',
        27 => 'user_id',
        28 => 'group_id',
        29 => 'department_group_id',
        30 => 'timefrom_seconds',
        31 => 'timefrom_minutes',
        32 => 'timefrom_hours',
        33 => 'timeto_hours',
        34 => 'timeto_minutes',
        35 => 'timeto_seconds',
        36 => 'exclude_offline',
        37 => 'with_bot',
        38 => 'dropped_chat',
        39 => 'online_offline',
        40 => 'without_bot',
        41 => 'proactive_chat',
        42 => 'no_operator',
        43 => 'has_unread_messages',
        44 => 'not_invitation',
        45 => 'has_operator',
        46 => 'abandoned_chat',
        47 => 'bot_ids',
        48 => 'cls_us',
        49 => 'has_unread_op_messages',
        51 => 'opened',
        52 => 'country_ids',
        53 => 'region',
        54 => 'frt_from',
        55 => 'frt_till',
        56 => 'mart_from',
        57 => 'mart_till',
        58 => 'aart_till',
        59 => 'aart_from',
        60 => 'reporthash',
        61 => 'reportts',
        62 => 'reportverified',
        63 => 'r',
        64 => 'is_external',
        65 => 'has_attachment',
        66 => 'has_online_hours',
        67 => 'exclude_deactivated',
        68 => 'mail_conv_user',
        69 => 'attr_int_1_multi',
        70 => 'op_msg_count',
        71 => 'vi_msg_count',
        72 => 'bot_msg_count'
    ),
    'functions' => array( 'viewstatistic' ),
    'multiple_arguments' => array('bot_ids','subject_ids','department_ids','group_ids','user_ids','department_group_ids','invitation_ids','chart_type','country_ids','attr_int_1_multi')
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
    'uparams' => array('type','tab'),
    'functions' => array( 'statisticdep' )
);

$ViewList['userstats'] = array(
    'params' => array('id'),
    'uparams' => array('action'),
    'functions' => array( 'userstats' )
);

$ViewList['onlinehours'] = array(
    'params' => array(),
    'uparams' => array('group_by','xls','timefrom','timeto','user_id','timefrom_minutes','timefrom_hours','timeto_hours','timeto_minutes','export'),
    'functions' => array( 'viewstatistic' ),
    'multiple_arguments' => array('user_id')
);

$FunctionList['exportxls'] = array('explain' => 'Allow to operator to export departments statistic in XLS');
$FunctionList['viewstatistic'] = array('explain' =>'Allow operator to view statistic');
$FunctionList['configuration'] = array('explain' =>'Allow operator to configure statistic');
$FunctionList['statisticdep'] = array('explain' =>'Allow operator to see departments/departments groups statistic in modal window');
$FunctionList['userstats'] = array('explain' =>'Allow operator to see operator statistic in modal window');

?>