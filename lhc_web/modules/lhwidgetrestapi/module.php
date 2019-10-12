<?php

$Module = array( "name" => "Live helper Chat REST API service");

$ViewList = array();

$ViewList['settings'] = array(
    'params' => array(),
    'uparams' => array('department','ua','identifier'),
    'functions' => array(  ),
    'multiple_arguments' => array ( 'department', 'ua' )
);

$ViewList['offlinesettings'] = array(
    'params' => array(),
    'functions' => array(),
    'uparams' => array('ua','switchform','operator','theme','vid','sound','hash','hash_resume','mode','offline','leaveamessage','department','priority','chatprefill','survey','sdemo','prod','phash','pvhash','fullheight','ajaxmode'),
	'multiple_arguments' => array ( 'department', 'ua', 'prod' )
);

$ViewList['onlinesettings'] = array(
    'params' => array(),
    'functions' => array(),
    'uparams' => array('ua','switchform','operator','theme','vid','sound','hash','hash_resume','mode','offline','leaveamessage','department','priority','chatprefill','survey','sdemo','prod','phash','pvhash','fullheight','ajaxmode'),
	'multiple_arguments' => array ( 'department', 'ua', 'prod' )
);

$ViewList['submitoffline'] = array(
    'params' => array(),
    'functions' => array(),
    'uparams' => array('ua','switchform','operator','theme','vid','sound','hash','hash_resume','mode','offline','leaveamessage','department','priority','chatprefill','survey','sdemo','prod','phash','pvhash','fullheight','ajaxmode'),
	'multiple_arguments' => array ( 'department', 'ua', 'prod' )
);

$ViewList['submitonline'] = array(
    'params' => array(),
    'functions' => array(),
    'uparams' => array('ua','switchform','operator','theme','vid','sound','hash','hash_resume','mode','offline','leaveamessage','department','priority','chatprefill','survey','sdemo','prod','phash','pvhash','fullheight','ajaxmode'),
	'multiple_arguments' => array ( 'department', 'ua', 'prod' )
);

$ViewList['initchat'] = array(
    'params' => array(),
    'functions' => array(),
    'uparams' => array(),
	'multiple_arguments' => array ()
);

$ViewList['fetchmessages'] = array(
    'params' => array(),
    'functions' => array(),
    'uparams' => array(),
	'multiple_arguments' => array ()
);

$FunctionList = array();
$FunctionList['use_admin'] = array('explain' => 'Allow operator to manage REST API');

?>