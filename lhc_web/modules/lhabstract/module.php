<?php

$Module = array( "name" => "Abstract module");

$ViewList = array();

$ViewList['new'] = array(
    'script' => 'new.php',
    'functions' => array( 'use' ),
    'params' => array('identifier')
);

$ViewList['list'] = array(
    'script' => 'list.php',
    'functions' => array( 'use' ),
    'params' => array('identifier'),
    'uparams' => array('name')
);

$ViewList['downloadbinnary'] = array(
    'script' => 'downloadbinnary.php',
    'functions' => array( 'use' ),
    'params' => array('identifier','object_id')
);

$ViewList['copyautoresponder'] = array(
    'functions' => array( 'use' ),
    'params' => array('id')
);

$ViewList['edit'] = array(
    'script' => 'edit.php',
    'functions' => array( 'use' ),
    'params' => array('identifier','object_id')
);

$ViewList['delete'] = array(
    'script' => 'delete.php',
    'functions' => array( 'use' ),
    'params' => array('identifier','object_id'),
    'uparams' => array('csfr')
);

$ViewList['index'] = array(
    'script' => 'index.php',
    'functions' => array( 'use' ),
    'params' => array()
);

$FunctionList = array();
$FunctionList['use'] = array('explain' => 'Allow to use abstract module');

?>