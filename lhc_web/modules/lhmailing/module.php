<?php

$Module = array( "name" => "Mailing module");

$ViewList = array();

$ViewList['mailinglist'] = array(
    'params' => array(),
    'functions' => array( 'use_admin' )
);

$FunctionList = array();
$FunctionList['use_admin'] = array('explain' => 'Permission to use mailing module');

?>