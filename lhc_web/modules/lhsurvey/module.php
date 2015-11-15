<?php

$Module = array( "name" => "Survey");

$ViewList = array();

$ViewList['fillwidget'] = array(
    'params' => array(),
    'uparams' => array('chatid','ua','switchform','operator','theme','vid','sound','hash','hash_resume','mode','offline','leaveamessage','department','priority','chatprefill','survey','eclose')
);

$ViewList['fill'] = array(
    'params' => array(),
    'uparams' => array('chatid','ua','switchform','operator','theme','vid','sound','hash','hash_resume','mode','offline','leaveamessage','department','priority','chatprefill','survey','eclose')
);

$ViewList['collected'] = array(
    'params' => array('survey_id'),
    'uparams' => array('timefrom','timeto','department_id','user_id','print','xls','stars','group_results','minimum_chats'),
    'functions' => array( 'manage_survey' )
);

$FunctionList = array();
$FunctionList['manage_survey'] = array('explain' => 'Allow operator to manage/view survey');

?>