<?php

$Module = array( "name" => "Notifications" );

$ViewList = array();

$ViewList['subscribe'] = array(
    'params' => array(),
    'uparams' => array('hash','vid','hash_resume','theme','action')
);

$ViewList['subscribeop'] = array(
    'params' => array(),
    'functions' => array( 'use' )
);

$ViewList['list'] = array(
    'params' => array(),
    'functions' => array( 'use' )
);

$ViewList['oplist'] = array(
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

$ViewList['editsubscriberop'] = array(
    'params' => array('id'),
    'functions' => array( 'use' )
);

$ViewList['downloadworker'] = array(
    'params' => array(),
    'functions' => array( 'use' )
);

$ViewList['downloadworkerop'] = array(
    'params' => array(),
    'functions' => array( 'use' )
);

$ViewList['settings'] = array(
    'params' => array(),
    'functions' => array( 'use' )
);

$ViewList['opsettings'] = array(
    'params' => array(),
    'functions' => array( 'use' )
);

$ViewList['deletesubscriber'] = array(
    'params' => array('id'),
    'uparams' => array('csfr'),
    'functions' => array( 'use' )
);

$ViewList['serviceworkerop'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$FunctionList['use'] = array('explain' => 'Notifications module');

?>