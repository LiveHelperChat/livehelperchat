<?php

$Module = array( "name" => "Webhooks" );

$ViewList = array();

$ViewList['configuration'] = array(
    'params' => array(),
    'functions' => array( 'configuration' )
);

$ViewList['new'] = array(
    'params' => array(),
    'functions' => array( 'configuration' )
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

$FunctionList['configuration'] = array('explain' => 'Webhooks module configuration');

?>