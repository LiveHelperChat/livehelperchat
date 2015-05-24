<?php

$Module = array( "name" => "Statistic module",
				 'variable_params' => true );

$ViewList = array();

$ViewList['departmentstatusxls'] = array(
    'params' => array(),
    'functions' => array( 'use' ),
    'uparams' => array()
);

$FunctionList['use'] = array('explain' => 'Allow to use statistic module');

?>