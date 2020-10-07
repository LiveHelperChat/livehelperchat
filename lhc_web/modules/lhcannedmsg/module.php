<?php

$Module = array( "name" => "Canned Messages");

$ViewList = array();

$ViewList['showsuggester'] = array(
    'params' => array('chat_id'),
    'uparams' => array(),
    'functions' => array( 'use' ),
);

$ViewList['filter'] = array(
    'params' => array('chat_id'),
    'uparams' => array(),
    'functions' => array( 'use' ),
);

$ViewList['import'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array( 'use_import' ),
);

$FunctionList['use'] = array('explain' => 'General permission to use canned messages module');
$FunctionList['see_global'] = array('explain' => 'Allow operator to see global canned messages');
$FunctionList['use_import'] = array('explain' => 'Allow operator to import canned messages');

?>