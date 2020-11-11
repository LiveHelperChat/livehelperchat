<?php

$Module = array( "name" => "Audit",
				 'variable_params' => true );

$ViewList = array();

$ViewList['configuration'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array( 'use' ),
);

$ViewList['loginhistory'] = array(
    'params' => array(),
    'uparams' => array('user_id'),
    'functions' => array( 'use' ),
);

$ViewList['logjserror'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array(  ),
);

$FunctionList['use'] = array('explain' => 'Allow operator to configure audit module');

?>