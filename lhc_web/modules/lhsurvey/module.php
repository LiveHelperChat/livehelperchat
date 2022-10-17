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

$ViewList['backtochat'] = array(
    'params' => array('chat_id','hash','survey'),
    'uparams' => array()
);

$ViewList['isfilled'] = array(
    'params' => array('chat_id','hash','survey'),
    'uparams' => array()
);

$ViewList['choosesurvey'] = array(
    'params' => array('chat_id','survey_id'),
    'uparams' => array(),
    'functions' => array( 'redirect_to_survey' )
);

$ViewList['collected'] = array(
    'params' => array('survey_id'),
    'uparams' => array('timefrom','timeto','department_id','user_id','print','xls','xlslist','xml','json','group_results','minimum_chats',
    'max_stars_1',
    'max_stars_2',
    'max_stars_3',
    'max_stars_4',
    'max_stars_5',
    'question_options_1',
    'question_options_2',
    'question_options_3',
    'question_options_4',
    'question_options_5',
    'department_ids',
    'department_group_ids',
    ),
    'functions' => array( 'manage_survey' ),
    'multiple_arguments' => array('max_stars_1','max_stars_2','max_stars_3','max_stars_4','max_stars_5',
        'department_ids',
        'department_group_ids'
    )
);

$ViewList['collecteditem'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'manage_survey' )
);

$FunctionList = array();
$FunctionList['manage_survey'] = array('explain' => 'Allow operator to manage/view survey');
$FunctionList['redirect_to_survey'] = array('explain' => 'Allow operator to redirect visitor to survey');

?>