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
    'uparams' => array('department_ids','department_group_ids','group_ids','user_ids','timeintervalto_hours', 'timeintervalfrom_hours', 'group_by','xls','tab','timefrom','timeto','department_id','user_id','group_id','department_group_id','timefrom_minutes','timefrom_hours','timeto_hours','timeto_minutes'),
    'functions' => array( 'viewstatistic' ),
    'multiple_arguments' => array('department_ids','group_ids','user_ids','department_group_ids')
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

$ViewList['onlinehours'] = array(
    'params' => array(),
    'uparams' => array('group_by','xls','timefrom','timeto','user_id','timefrom_minutes','timefrom_hours','timeto_hours','timeto_minutes'),
    'functions' => array( 'viewstatistic' )
);

$FunctionList['exportxls'] = array('explain' => 'Allow to operator to export departments statistic in XLS');
$FunctionList['viewstatistic'] = array('explain' =>'Allow operator to view statistic');
$FunctionList['configuration'] = array('explain' =>'Allow operator to configure statistic');

?>