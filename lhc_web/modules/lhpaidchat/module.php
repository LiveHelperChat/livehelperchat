<?php

$Module = array( "name" => "Paid chats module",
				 'variable_params' => true );

$ViewList = array();

$ViewList['settings'] = array (
    'params' => array(),
    'uparams' => array(),
    'functions' => array( 'use_admin' )
);

$ViewList['expiredchat'] = array (
    'params' => array('pchat'),
    'uparams' => array()
);

$ViewList['removedpaidchat'] = array (
    'params' => array('pchat'),
    'uparams' => array()
);

$ViewList['invalidhash'] = array (
    'params' => array('pchat'),
    'uparams' => array()
);

$FunctionList['use_admin'] = array('explain' => 'General permission to configure paid chats module');

?>