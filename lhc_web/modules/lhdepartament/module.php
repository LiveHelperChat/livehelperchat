<?php

$Module = array( "name" => "Departments configuration");

$ViewList = array();
   
$ViewList['departaments'] = array( 
    'script' => 'departaments.php',
    'params' => array(),
    'functions' => array( 'list' )
    ); 
    
$ViewList['new'] = array( 
    'script' => 'new.php',
    'params' => array(),
    'functions' => array( 'create' )
    ); 
    
$ViewList['edit'] = array( 
    'script' => 'edit.php',
    'params' => array('departament_id'),
    'functions' => array( 'edit' )
    ); 
    
$ViewList['delete'] = array( 
    'script' => 'delete.php',
    'params' => array('departament_id'),
    'functions' => array( 'delete' )
    );

$FunctionList['list'] = array('explain' => 'Access to list departments');  
$FunctionList['create'] = array('explain' => 'Create new department');  
$FunctionList['edit'] = array('explain' => 'Edit department');  
$FunctionList['delete'] = array('explain' => 'Allow to delete department');  
$FunctionList['selfedit'] = array('explain' => 'Allow user to choose his departments');  

?>