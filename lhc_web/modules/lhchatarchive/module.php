<?php

$Module = array( "name" => "Chat archive module");

$ViewList = array();

$ViewList['archive'] = array(
    'params' => array(),
    'functions' => array( 'archive' )
);

$ViewList['newarchive'] = array(
    'params' => array(),
    'functions' => array( 'archive' )
);

$ViewList['startarchive'] = array(
    'params' => array(),
    'functions' => array( 'archive' )
);

$ViewList['archivechats'] = array(
    'params' => array(),
    'functions' => array( 'archive' )
);

$ViewList['list'] = array(
    'params' => array(),
    'functions' => array( 'archive' )
);

$ViewList['listarchivechats'] = array(
    'params' => array('id'),
    'uparams' => array('nick','email','timefrom','timeto'),
    'functions' => array( 'archive' )
);

$ViewList['edit'] = array(
    'params' => array('id'),
    'functions' => array( 'archive' )
);

$ViewList['viewarchivedchat'] = array(
    'params' => array('archive_id','chat_id'),
    'functions' => array( 'archive' )
);

$ViewList['printchatadmin'] = array(
    'params' => array('archive_id','chat_id'),
    'functions' => array( 'archive' )
);

$ViewList['sendmail'] = array(
    'params' => array('archive_id','chat_id'),
    'functions' => array( 'archive' )
);

$ViewList['deletearchivechat'] = array(
    'params' => array('archive_id','chat_id'),
    'uparams' => array('csfr'),
    'functions' => array( 'archive' )
);

$ViewList['process'] = array(
    'params' => array('id'),
    'functions' => array( 'archive' )
);

$FunctionList['archive'] = array('explain' => 'Allow user to use archive functionality');

?>