<?php

$Module = array( "name" => "Webhooks" );

$ViewList = array();

$ViewList['configuration'] = array(
    'params' => array(),
    'functions' => array( 'configuration' )
);

$ViewList['pushchat'] = array(
    'params' => array(),
    'functions' => array( 'configuration' )
);

$ViewList['new'] = array(
    'params' => array(),
    'functions' => array( 'configuration' )
);

$ViewList['newincoming'] = array(
    'params' => array(),
    'functions' => array( 'configuration' )
);

$ViewList['editincoming'] = array(
    'params' => array('id'),
    'functions' => array( 'configuration' )
);

$ViewList['incoming'] = array(
    'params' => array('identifier'),
);

$ViewList['edit'] = array(
    'params' => array('id'),
    'functions' => array( 'configuration' )
);

$ViewList['delete'] = array(
    'params' => array('id'),
    'uparams' => array('csfr'),
    'functions' => array( 'configuration' )
);

$ViewList['deleteincoming'] = array(
    'params' => array('id'),
    'uparams' => array('csfr'),
    'functions' => array( 'configuration' )
);

$ViewList['incomingwebhooks'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array( 'configuration' )
);

$FunctionList['configuration'] = array('explain' => 'Webhooks module configuration');

?>