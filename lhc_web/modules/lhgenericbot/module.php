<?php

$Module = array( "name" => "Generic Bot",
				 'variable_params' => true );

$ViewList = array();

$ViewList['bot'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['nodegroups'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['list'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['new'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['edit'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['delete'] = array(
    'params' => array('id'),
    'uparams' => array('csfr'),
    'functions' => array( 'use' )
);

$ViewList['nodegrouptriggers'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['addgroup'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['updategroup'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['updatetrigger'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['savetrigger'] = array(
    'params' => array(),
    'uparams' => array('method'),
    'functions' => array( 'use' )
);

$ViewList['addtrigger'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$ViewList['removetrigger'] = array(
    'params' => array('id'),
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