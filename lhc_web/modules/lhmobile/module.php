<?php

$Module = array( "name" => "System configuration");

$ViewList = array();

$ViewList['settings'] = array(
    'params' => array(),
    'functions' => array( 'manage' )
);

$ViewList['sessions'] = array(
    'params' => array(),
    'functions' => array( 'manage' )
);

$ViewList['editsession'] = array(
    'params' => array('id'),
    'functions' => array( 'manage' )
);

$ViewList['deletesession'] = array(
    'params' => array('id'),
    'uparams' => array('csfr'),
    'functions' => array( 'manage' )
);

$FunctionList['manage'] = array('explain' => 'Allow operator to manage online sessions');

?>