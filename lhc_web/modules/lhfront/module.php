<?php

$Module = array( "name" => "Frontpage",
				 'variable_params' => true );

$ViewList = array();
   
$ViewList['default'] = array( 
    'params' => array(),
    'functions' => array( 'use' )
    );
   
$FunctionList['use'] = array('explain' => 'General frontpage use permission');  

?>