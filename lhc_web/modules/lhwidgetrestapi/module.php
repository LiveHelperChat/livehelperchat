<?php

$Module = array( "name" => "Live helper Chat REST API service");

$ViewList = array();

$ViewList['getproducts'] = array(
    'params' => array('id','product_id')
);

$ViewList['settings'] = array(
    'params' => array(),
    'uparams' => array('department','ua','identifier'),
    'multiple_arguments' => array ( 'department', 'ua' )
);

$ViewList['offlinesettings'] = array(
    'params' => array(),
    'uparams' => array('ua','switchform','operator','theme','vid','sound','hash','hash_resume','mode','offline','leaveamessage','department','priority','chatprefill','survey','sdemo','prod','phash','pvhash','fullheight','ajaxmode'),
	'multiple_arguments' => array ( 'department', 'ua', 'prod' )
);

$ViewList['updatejs'] = array(
    'params' => array(),
    'uparams' => array(),
	'multiple_arguments' => array ()
);

$ViewList['lang'] = array(
    'params' => array(),
    'uparams' => array(),
	'multiple_arguments' => array ()
);

$ViewList['onlinesettings'] = array(
    'params' => array(),
    'uparams' => array('ua','switchform','operator','theme','vid','sound','hash','hash_resume','mode','offline','leaveamessage','department','priority','chatprefill','survey','sdemo','prod','phash','pvhash','fullheight','ajaxmode'),
	'multiple_arguments' => array ( 'department', 'ua', 'prod' )
);

$ViewList['submitoffline'] = array(
    'params' => array(),
    'uparams' => array('ua','switchform','operator','theme','vid','sound','hash','hash_resume','mode','offline','leaveamessage','department','priority','chatprefill','survey','sdemo','prod','phash','pvhash','fullheight','ajaxmode'),
	'multiple_arguments' => array ( 'department', 'ua', 'prod' )
);

$ViewList['submitonline'] = array(
    'params' => array(),
    'uparams' => array('ua','switchform','operator','theme','vid','sound','hash','hash_resume','mode','offline','leaveamessage','department','priority','chatprefill','survey','sdemo','prod','phash','pvhash','fullheight','ajaxmode'),
	'multiple_arguments' => array ( 'department', 'ua', 'prod' )
);

$ViewList['initchat'] = array(
    'params' => array(),
    'uparams' => array(),
	'multiple_arguments' => array ()
);

$ViewList['uisettings'] = array(
    'params' => array(),
    'uparams' => array(),
	'multiple_arguments' => array ()
);

$ViewList['fetchmessages'] = array(
    'params' => array(),
    'uparams' => array(),
	'multiple_arguments' => array ()
);

$ViewList['addmsguser'] = array(
    'params' => array('chat_id','hash'),
    'uparams' => array('mode'),
);

$ViewList['chatcheckstatus'] = array(
    'params' => array(),
    'uparams' => array('status','department','vid','uactiv','wopen','uaction','hash','hash_resume','dot','hide_offline','isproactive'),
    'multiple_arguments' => array ( 'department' )
);

$ViewList['checkchatstatus'] = array(
    'params' => array('chat_id','hash'),
    'uparams' => array('mode','theme','dot')
);

$ViewList['loadsound'] = array(
    'params' => array('sound'),
    'uparams' => array()
);

$ViewList['themepage'] = array(
    'params' => array('theme'),
    'uparams' => array()
);

$ViewList['themeneedhelp'] = array(
    'params' => array('theme'),
    'uparams' => array()
);

$ViewList['theme'] = array(
    'params' => array('theme'),
    'uparams' => array('p')
);

$ViewList['themestatus'] = array(
    'params' => array('theme'),
    'uparams' => array()
);

$ViewList['checkinvitation'] = array(
    'params' => array(),
    'uparams' => array()
);

$ViewList['getinvitation'] = array(
    'params' => array(),
    'uparams' => array()
);

$ViewList['screensharesettings'] = array(
    'params' => array(),
    'uparams' => array()
);

$FunctionList = array();
$FunctionList['use_admin'] = array('explain' => 'Allow operator to manage REST API');

?>