<?php

$Module = array( "name" => "Theme",
				 'variable_params' => true );

$ViewList = array();

$ViewList['export'] = array(
    'params' => array('theme'),
    'functions' => array( 'administratethemes' )
);

$ViewList['import'] = array(
    'params' => array(),
    'functions' => array( 'administratethemes' )
);

$ViewList['index'] = array(
    'params' => array(),
    'functions' => array( 'administratethemes' )
);

$ViewList['default'] = array(
    'params' => array(),
    'functions' => array( 'administratethemes' )
);

$ViewList['defaultadmintheme'] = array(
    'params' => array(),
    'functions' => array( 'administratethemes' )
);

$ViewList['adminthemes'] = array(
    'params' => array(),
    'functions' => array( 'administratethemes' )
);

$ViewList['adminnewtheme'] = array(
    'params' => array(),
    'functions' => array( 'administratethemes' )
);

$ViewList['adminthemedelete'] = array(
    'params' => array('id'),
    'uparams' => array('csfr'),
    'functions' => array('administratethemes'),
);

$ViewList['adminthemeedit'] = array(
    'params' => array('id'),
    'functions' => array( 'administratethemes' )
);

$ViewList['deleteresource'] = array (
    'params' => array('id', 'context', 'hash', ),
    'functions' => array('administratethemes'),
);

$ViewList['gethash'] = array (
    'params' => array(),
);

$FunctionList['administratethemes'] = array('explain' =>'Allow users to maintain themes');

?>