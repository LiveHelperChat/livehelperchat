<?php

$Module = array( "name" => "Voice & Video & ScreenShare" );

$ViewList = array();

$ViewList['configuration'] = array(
    'params' => array(),
    'functions' => array( 'configuration' )
);

$ViewList['sessions'] = array(
    'params' => array(),
    'functions' => array( 'sessions' )
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
    'uparams' => array('mode'),
    'functions' => array( 'use' )
);

$ViewList['recording'] = array(
    'params' => array('id'),
    'uparams' => array('download'),
    'functions' => array( 'recordings' )
);

// LiveKit webhook receiver. Requests are authenticated by signature.
$ViewList['webhook'] = array(
    'params' => array()
);

$FunctionList['configuration'] = array('explain' => 'Voice & Video & ScreenShare module configuration');
$FunctionList['sessions'] = array('explain' => 'Allow operator to see Voice & Video calls history');
$FunctionList['supervise'] = array('explain' => 'Allow operator to silently listen to other operators calls');
$FunctionList['record'] = array('explain' => 'Allow operator to start and stop call recording');
$FunctionList['recordings'] = array('explain' => 'Allow operator to listen and download call recordings');
$FunctionList['use'] = array('explain' => 'Allow operator to use Voice & Video & ScreenShare calls');

?>