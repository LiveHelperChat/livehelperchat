<?php

$Module = array( "name" => "System configuration");

$ViewList = array();

$ViewList['htmlcode'] = array(
    'params' => array(),
    'functions' => array( 'generatejs' )
);

$ViewList['embedcode'] = array(
    'params' => array(),
    'functions' => array( 'generatejs' )
);

$ViewList['configuration'] = array(
    'params' => array(),
    'functions' => array( 'use' )
);

$ViewList['expirecache'] = array(
    'params' => array(),
    'functions' => array( 'expirecache' )
);

$ViewList['smtp'] = array(
    'params' => array(),
    'functions' => array( 'configuresmtp' )
);

$ViewList['timezone'] = array(
    'params' => array(),
    'functions' => array( 'timezone' )
);

$ViewList['languages'] = array(
    'params' => array(),
    'uparams' => array('updated','sa'),
    'functions' => array( 'changelanguage' )
);

$ViewList['update'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array( 'performupdate' )
);

$FunctionList['use'] = array('explain' => 'Allow user to see configuration links');
$FunctionList['expirecache'] = array('explain' => 'Allow user to clear cache');
$FunctionList['generatejs'] = array('explain' => 'Allow user to access HTML generation');
$FunctionList['configuresmtp'] = array('explain' => 'Allow user to configure SMTP');
$FunctionList['configurelanguages'] = array('explain' => 'Allow user to configure languages');
$FunctionList['changelanguage'] = array('explain' => 'Allow user to change his languages');
$FunctionList['timezone'] = array('explain' => 'Allow user to change global time zone');
$FunctionList['performupdate'] = array('explain' => 'Allow user to update Live Helper Chat');

?>