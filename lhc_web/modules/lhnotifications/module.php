<?php

$Module = array( "name" => "Notifications" );

$ViewList = array();

$ViewList['subscribe'] = array(
    'params' => array(),
    'uparams' => array('hash','vid','hash_resume','theme','action')
);

$ViewList['list'] = array(
    'params' => array(),
    'functions' => array( 'use' )
);

$ViewList['index'] = array(
    'params' => array(),
    'functions' => array( 'use' )
);

$ViewList['read'] = array(
    'params' => array(),
    'uparams' => array('id','hash','theme','mode','hashread'),
);

$ViewList['editsubscriber'] = array(
    'params' => array('id'),
    'functions' => array( 'use' )
);

$ViewList['downloadworker'] = array(
    'params' => array(),
    'functions' => array( 'use' )
);

$ViewList['settings'] = array(
    'params' => array(),
    'functions' => array( 'use' )
);

$ViewList['deletesubscriber'] = array(
    'params' => array('id'),
    'uparams' => array('csfr'),
    'functions' => array( 'use' )
);

$FunctionList['use'] = array('explain' => 'Notifications module');

?>