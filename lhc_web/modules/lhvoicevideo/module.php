<?php

$Module = array( "name" => "Voice & Video & ScreenShare" );

$ViewList = array();

$ViewList['configuration'] = array(
    'params' => array(),
    'functions' => array( 'configuration' )
);

$ViewList['sessions'] = array(
    'params' => array(),
    'functions' => array( 'configuration' )
);

$ViewList['call'] = array(
    'params' => array('id','hash')
);

$ViewList['join'] = array(
    'params' => array('id','hash'),
    'uparams' => array('action'),
);

$ViewList['joinop'] = array(
    'params' => array('id'),
    'uparams' => array('action'),
    'functions' => array( 'use' )
);

$ViewList['joinoperator'] = array(
    'params' => array('id'),
    'functions' => array( 'use' )
);

$FunctionList['configuration'] = array('explain' => 'Voice & Video & ScreenShare module configuration');
$FunctionList['use'] = array('explain' => 'Allow operator to use Voice & Video & ScreenShare calls');

?>