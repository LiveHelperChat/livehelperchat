<?php

$Module = array( "name" => "Chat settings",
				 'variable_params' => true );

$ViewList = array();

$ViewList['startsettingslist'] = array(
    'params' => array(),
    'functions' => array( 'administrate' )
);

$ViewList['newstartsettings'] = array(
    'params' => array(),
    'functions' => array( 'administrate' )
);

$ViewList['editstartsettings'] = array(
    'params' => array('id'),
    'functions' => array( 'administrate' )
);

$ViewList['deletestartsettings'] = array(
    'params' => array('id'),
    'uparams' => array('csfr'),
    'functions' => array( 'administrate' )
);

$ViewList['startchatformsettings'] = array(
    'params' => array(),
    'functions' => array( 'administrate' )
);

$ViewList['startchatformsettingsindex'] = array(
    'params' => array(),
    'functions' => array( 'administrate' )
);

$ViewList['editeventsettings'] = array(
    'params' => array('id'),
    'functions' => array( 'events' )
);

$ViewList['eventlist'] = array(
    'params' => array(),
    'functions' => array( 'events' )
);

$ViewList['neweventsettings'] = array(
    'params' => array(),
    'functions' => array( 'events' )
);

$ViewList['eventindex'] = array(
    'params' => array(),
    'functions' => array( 'events' )
);

$ViewList['deleteevent'] = array(
    'params' => array('id'),
    'uparams' => array('csfr'),
    'functions' => array( 'administrate' )
);

$FunctionList['administrate'] = array('explain' => 'Allow to configure chat start form');
$FunctionList['events'] = array('explain' => 'Allow to configure events tracking');

?>