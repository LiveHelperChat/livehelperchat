<?php

$Module = array( "name" => "Canned Messages");

$ViewList = array();

$ViewList['showsuggester'] = array(
    'params' => array('chat_id'),
    'uparams' => array(),
    'functions' => array( 'use' ),
);

$ViewList['subject'] = array(
    'params' => array('canned_id'),
    'uparams' => array('subject','status'),
    'functions' => array( 'use' ),
);

$ViewList['filter'] = array(
    'params' => array('chat_id'),
    'uparams' => array(),
    'functions' => array( 'use' ),
);

$ViewList['listreplace'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array( 'use_replace' ),
);

$ViewList['newreplace'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array( 'use_replace' ),
);

$ViewList['editreplace'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'use_replace' ),
);

$ViewList['deletereplace'] = array(
    'params' => array('id'),
    'uparams' => array('csfr'),
    'functions' => array( 'use_replace' ),
);

$ViewList['import'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array( 'use_import' ),
);

$FunctionList['use'] = array('explain' => 'General permission to use canned messages module');
$FunctionList['see_global'] = array('explain' => 'Allow operator to see global canned messages');
$FunctionList['use_import'] = array('explain' => 'Allow operator to import canned messages');
$FunctionList['use_replace'] = array('explain' => 'Allow operator manage replaceable variables');

?>