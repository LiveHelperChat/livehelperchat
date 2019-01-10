<?php

$Module = array( "name" => "Abstract module");

$ViewList = array();

$ViewList['new'] = array(
    'functions' => array( 'use' ),
    'params' => array('identifier')
);

$ViewList['list'] = array(
    'functions' => array( 'use' ),
    'params' => array('identifier'),
    'uparams' => array('name','object_id','category','source')
);

$ViewList['downloadbinnary'] = array(
    'functions' => array( 'use' ),
    'params' => array('identifier','object_id')
);

$ViewList['copyautoresponder'] = array(
    'functions' => array( 'use' ),
    'params' => array('id')
);

$ViewList['edit'] = array(
    'functions' => array( 'use' ),
    'params' => array('identifier','object_id')
);

$ViewList['delete'] = array(
    'functions' => array( 'use' ),
    'params' => array('identifier','object_id'),
    'uparams' => array('csfr')
);

$ViewList['index'] = array(
    'functions' => array( 'use' ),
    'params' => array()
);

$FunctionList = array();
$FunctionList['use'] = array('explain' => 'Allow to use abstract module');

?>