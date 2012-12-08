<?php

$Module = array( "name" => "Frontpage",
				 'variable_params' => true );

$ViewList = array();
   
$ViewList['default'] = array( 
    'script' => 'default.php',
    'params' => array(),
    'functions' => array( 'use' )
    );
   
$FunctionList['use'] = array('explain' => 'General frontpage use permission');  

?>