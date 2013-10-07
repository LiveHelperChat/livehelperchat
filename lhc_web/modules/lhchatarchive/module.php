<?php

$Module = array( "name" => "Chat archive module");

$ViewList = array();

$ViewList['archive'] = array(
    'script' => 'archive.php',
    'params' => array(),
    'functions' => array( 'archive' )
);

$ViewList['newarchive'] = array(
    'script' => 'newarchive.php',
    'params' => array(),
    'functions' => array( 'archive' )
);

$ViewList['startarchive'] = array(
    'script' => 'startarchive.php',
    'params' => array(),
    'functions' => array( 'archive' )
);

$ViewList['archivechats'] = array(
    'script' => 'archivechats.php',
    'params' => array(),
    'functions' => array( 'archive' )
);

$ViewList['list'] = array(
    'script' => 'list.php',
    'params' => array(),
    'functions' => array( 'archive' )
);

$ViewList['process'] = array(
    'script' => 'process.php',
    'params' => array('id'),
    'functions' => array( 'archive' )
);

$FunctionList['archive'] = array('explain' => 'Allow user to use archive functionality');

?>