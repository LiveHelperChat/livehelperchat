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

$FunctionList['configuration'] = array('explain' => 'Voice & Video & ScreenShare module configuration');

?>