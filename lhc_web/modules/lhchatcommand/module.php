<?php

$Module = array( "name" => "Chat commands",
				 'variable_params' => true );

$ViewList = array();

$ViewList['command'] = array(
    'params' => array('chat_id','command_id'),
    'uparams' => array(),
    'functions' => array( 'use' )
);

$FunctionList['use'] = array('explain' => 'Allow operator to use chat commands defined in bot commands section');

?>