<?php

$Module = array( "name" => "Frontpage");

$ViewList = array();
   
$ViewList['default'] = array( 
    'params' => array(),
    'functions' => array( 'use' )
);

$ViewList['tabs'] = array(
    'params' => array(),
    'uparams' => array('id'),
    'functions' => array('use'),
    'multiple_arguments' => array('id')
);

$ViewList['switchdashboard'] = array(
    'params' => array(),
    'uparams' => array('action'),
    'functions' => array('use'),
);

$FunctionList['use'] = array('explain' => 'General frontpage use permission');  

?>