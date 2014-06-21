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

$FunctionList['administratethemes'] = array('explain' =>'Allow users to maintain themes');

?>