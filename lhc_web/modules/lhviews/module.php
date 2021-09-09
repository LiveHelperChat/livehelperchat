<?php

$Module = array( "name" => "Views module" );

$ViewList = array();

$ViewList['home'] = array(
    'params' => array('id'),
    'functions' => array( 'use' )
);

$ViewList['loadinitialdata'] = array(
    'params' => array(),
    'functions' => array( 'use' )
);

$ViewList['loadview'] = array(
    'params' => array('id'),
    'uparams' => array('mode'),
    'functions' => array( 'use' )
);

$ViewList['updateviews'] = array(
    'params' => array(),
    'functions' => array( 'use' )
);

$ViewList['edit'] = array(
    'params' => array('id'),
    'functions' => array( 'use' )
);

$ViewList['deleteview'] = array(
    'params' => array('id'),
    'functions' => array( 'use' )
);

$ViewList['view'] = array(
    'params' => array(),
    'functions' => array( 'configuration' )
);

$FunctionList['configuration'] = array('explain' => 'Voice & Video & ScreenShare module configuration');
$FunctionList['use'] = array('explain' => 'Allow operator to use Voice & Video & ScreenShare calls');

?>