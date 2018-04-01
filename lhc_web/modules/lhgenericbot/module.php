<?php

$Module = array( "name" => "Generic Bot",
				 'variable_params' => true );

$ViewList = array();

$ViewList['bot'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['nodegroups'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['nodegrouptriggers'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['addgroup'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['nodetriggeractions'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$FunctionList['use'] = array('explain' => 'General permission to use generic bot module');

?>