<?php

$Module = array( "name" => "Product",
				 'variable_params' => true );

$ViewList = array();

$ViewList['index'] = array(
    'params' => array(),
    'functions' => array( 'administratethemes' )
);

$FunctionList['manage_product'] = array('explain' =>'Allow users to maintain themes');

?>