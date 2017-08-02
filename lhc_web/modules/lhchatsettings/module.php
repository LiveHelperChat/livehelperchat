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

$FunctionList['administrate'] = array('explain' => 'General permission to use chat module');

?>