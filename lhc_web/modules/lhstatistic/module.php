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
    'uparams' => array('tab','timefrom','timeto','department_id','user_id','group_id','department_group_id'),
    'functions' => array( 'viewstatistic' )
);

$ViewList['onlinehours'] = array(
    'params' => array(),
    'uparams' => array('timefrom','timeto','user_id'),
    'functions' => array( 'viewstatistic' )
);

$FunctionList['exportxls'] = array('explain' => 'Allow to operator to export departments statistic in XLS');
$FunctionList['viewstatistic'] = array('explain' =>'Allow operator to view statistic');

?>