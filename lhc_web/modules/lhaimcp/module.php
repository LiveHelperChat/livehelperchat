<?php

$Module = array( "name" => "AI MCP Server",
				 'variable_params' => true );

$ViewList = array();

$ViewList['mcp'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array( ),
);

$ViewList['key'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array( 'use'),
);

$FunctionList['use'] = array('explain' => 'Allow operator to set API key for MCP');

?>