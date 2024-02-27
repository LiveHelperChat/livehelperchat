<?php

$Module = array( "name" => "Mail conversation OAuth module");

$ViewList = array();

$ViewList['mslogin'] = array(
    'params' => array('id'),
    'functions' => array( 'use_admin' )
);

$ViewList['options'] = array(
    'params' => array(),
    'functions' => array( 'manage_oauth' )
);

$ViewList['msoauth'] = array(
    'params' => array()
);

$FunctionList = array();
$FunctionList['use_admin'] = array('explain' => 'Permission to use mail conversation OAuth module');
$FunctionList['manage_oauth'] = array('explain' => 'Permission to use mail conversation OAuth module');

?>