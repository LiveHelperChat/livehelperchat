<?php

$Module = array( "name" => "Mail conversation module");

$ViewList = array();

$ViewList['index'] = array(
    'params' => array(),
    'functions' => array( 'use_admin' )
);

$ViewList['mailbox'] = array(
    'params' => array(),
    'functions' => array( 'use_admin' )
);

$ViewList['newmailbox'] = array(
    'params' => array(),
    'functions' => array( 'use_admin' )
);

$ViewList['matchingrules'] = array(
    'params' => array(),
    'functions' => array( 'use_admin' )
);

$ViewList['newmatchrule'] = array(
    'params' => array(),
    'functions' => array( 'use_admin' )
);

$ViewList['editmailbox'] = array(
    'params' => array('id'),
    'functions' => array( 'use_admin' )
);

$ViewList['editmatchrule'] = array(
    'params' => array('id'),
    'functions' => array( 'use_admin' )
);

$ViewList['deletemailbox'] = array(
    'params' => array('id'),
    'uparams' => array('csfr'),
    'functions' => array( 'use_admin' )
);

$FunctionList = array();
$FunctionList['use_admin'] = array('explain' => 'Permission to use mail conversation module');

?>