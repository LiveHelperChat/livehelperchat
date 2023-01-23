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

$ViewList['acceptinvites'] = array(
    'params' => array(),
    'uparams' => array('view'),
    'functions' => array( 'use' )
);

$ViewList['shareview'] = array(
    'params' => array('id'),
    'functions' => array( 'use' )
);

$ViewList['updatepassivemode'] = array(
    'params' => array('id'),
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

$ViewList['exportview'] = array(
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

$FunctionList['configuration'] = array('explain' => 'Allow operator to configure views');
$FunctionList['use'] = array('explain' => 'Allow operator to use views');
$FunctionList['use_chat'] = array('explain' => 'Allow operator to use views for chats');

?>