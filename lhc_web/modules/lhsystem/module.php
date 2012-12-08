<?php

$Module = array( "name" => "System configuration");

$ViewList = array();
   
$ViewList['htmlcode'] = array( 
    'script' => 'htmlcode.php',
    'params' => array(),
    'functions' => array( 'generatejs' )
    );  
      
$ViewList['configuration'] = array( 
    'script' => 'configuration.php',
    'params' => array(),
    'functions' => array( 'use' )
    );   
         
$ViewList['expirecache'] = array( 
    'script' => 'expirecache.php',
    'params' => array(),
    'functions' => array( 'expirecache' )
    ); 
    
$FunctionList['use'] = array('explain' => 'Allow user to see configuration links');  
$FunctionList['expirecache'] = array('explain' => 'Allow user to clear cache');  
$FunctionList['generatejs'] = array('explain' => 'Allow user access HTML generation');  

?>