<?php

$Module = array( "name" => "Product",
				 'variable_params' => true );

$ViewList = array();

$ViewList['index'] = array(
    'params' => array(),
    'functions' => array( 'manage_product' )
);

$ViewList['getproducts'] = array(
    'params' => array('id','product_id')
);

$FunctionList['manage_product'] = array('explain' =>'Allow users to maintain themes');

?>