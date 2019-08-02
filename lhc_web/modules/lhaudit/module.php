<?php

$Module = array( "name" => "Audit",
				 'variable_params' => true );

$ViewList = array();

$ViewList['configuration'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array( 'use' ),
);

$FunctionList['use'] = array('explain' => 'Allow operator to configure audit module');

?>