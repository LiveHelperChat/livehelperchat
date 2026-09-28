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

// LiveKit webhook receiver. Requests are authenticated by signature.
$ViewList['webhook'] = array(
    'params' => array()
);

$FunctionList['configuration'] = array('explain' => 'Voice & Video & ScreenShare module configuration');
$FunctionList['sessions'] = array('explain' => 'Allow operator to see Voice & Video calls history');
$FunctionList['supervise'] = array('explain' => 'Allow operator to silently listen to other operators calls');
$FunctionList['use'] = array('explain' => 'Allow operator to use Voice & Video & ScreenShare calls');

?>