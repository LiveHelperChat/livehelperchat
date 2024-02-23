<?php

$Module = array( "name" => "Mail conversation module");

$ViewList = array();

$ViewList['index'] = array(
    'params' => array(),
    'functions' => array( 'use_admin' )
);

$ViewList['uploadimage'] = array(
    'params' => array(),
    'uparams' => array('csrf'),
    'functions' => array( 'use_admin' )
);

$ViewList['tpx'] = array(
    'params' => array('id')
);

$ViewList['sendemail'] = array(
    'params' => array(),
    'uparams' => array('chat_id'),
    'functions' => array( 'send_mail' )
);

$ViewList['geticketbymessageid'] = array(
    'params' => array(),
    'functions' => array( 'send_mail' )
);

$ViewList['uploadfile'] = array(
    'params' => array(),
    'uparams' => array('csrf'),
    'functions' => array( 'use_admin' )
);

$ViewList['attatchfiledata'] = array(
    'params' => array('id'),
    'functions' => array( 'use_admin' )
);

$ViewList['mailbox'] = array(
    'params' => array(),
    'uparams' => array('mail','failed','sync_status','active'),
    'functions' => array( 'mailbox_manage' )
);

$ViewList['personalmailboxgroups'] = array(
    'params' => array(),
    'functions' => array( 'mailbox_manage' )
);

$ViewList['newpersonalmailboxgroup'] = array(
    'params' => array(),
    'functions' => array( 'mailbox_manage' )
);

$ViewList['editpersonalmailboxgroup'] = array(
    'params' => array('id'),
    'functions' => array( 'mailbox_manage' )
);

$ViewList['inlinedownload'] = array(
    'params' => array('id','id_conv'),
    'functions' => array( 'use_admin' )
);

$ViewList['previewmail'] = array(
    'params' => array('id'),
    'functions' => array( 'use_admin' )
);

$ViewList['transfermail'] = array(
    'params' => array('id'),
    'functions' => array( 'use_admin' )
);

$ViewList['changemailbox'] = array(
    'params' => array('id'),
    'functions' => array( 'change_mailbox' )
);

$ViewList['merge'] = array(
    'params' => array('id'),
    'functions' => array( 'use_admin' )
);

$ViewList['apiunmerge'] = array(
    'params' => array('id'),
    'functions' => array( 'use_admin' )
);

$ViewList['apimaildownload'] = array(
    'params' => array('id','id_conv'),
    'functions' => array( 'can_download' )
);

$ViewList['apisendreply'] = array(
    'params' => array('id'),
    'functions' => array( 'use_admin' )
);

$ViewList['apifetchmails'] = array(
    'params' => array('id','ts'),
    'functions' => array( 'use_admin' )
);

$ViewList['attatchfile'] = array(
    'params' => array(),
    'uparams' => array('persistent','user_id','visitor','upload_name','attachment'),
    'functions' => array( 'use_admin' )
);

$ViewList['insertfile'] = array(
    'params' => array('id'),
    'uparams' => array('mode'),
    'functions' => array( 'use_admin' )
);

$ViewList['searchtemplate'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'use_admin' )
);

$ViewList['attachtemplate'] = array(
    'params' => array('id'),
    'functions' => array( 'use_admin' )
);

$ViewList['getreplydata'] = array(
    'params' => array('id','mode'),
    'functions' => array( 'use_admin' )
);

$ViewList['mailprint'] = array(
    'params' => array('id','id_conv'),
    'functions' => array( 'use_admin' )
);

$ViewList['downloadrfc822'] = array(
    'params' => array('id','id_conv'),
    'functions' => array( 'use_admin' )
);

$ViewList['mailprintcovnersation'] = array(
    'params' => array('id'),
    'functions' => array( 'use_admin' )
);

$ViewList['view'] = array(
    'params' => array('id'),
    'functions' => array( 'use_admin' )
);

$ViewList['single'] = array(
    'params' => array('id'),
    'functions' => array( 'use_admin' )
);

$ViewList['apinoreplyrequired'] = array(
    'params' => array('id'),
    'functions' => array( 'use_admin' )
);

$ViewList['loadmessagebody'] = array(
    'params' => array('id','id_conv'),
    'functions' => array( 'use_admin' )
);

$ViewList['apilabelmessage'] = array(
    'params' => array('id'),
    'uparams' => array('subject','status'),
    'functions' => array( 'use_admin' )
);

$ViewList['apigetlabels'] = array(
    'params' => array('id'),
    'functions' => array( 'use_admin' )
);

$ViewList['apicloseconversation'] = array(
    'params' => array('id'),
    'functions' => array( 'use_admin' )
);

$ViewList['apichangestatus'] = array(
    'params' => array('id','status'),
    'functions' => array( 'use_admin' )
);

$ViewList['apideleteconversation'] = array(
    'params' => array('id'),
    'functions' => array( 'delete_conversation' )
);

$ViewList['loadmainconv'] = array(
    'params' => array('id'),
    'uparams' => array('mode'),
    'functions' => array( 'use_admin' )
);

$ViewList['saveremarks'] = array(
    'params' => array('id'),
    'uparams' => array('type'),
    'functions' => array( 'use_admin' )
);

$ViewList['mailhistory'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'use_admin' )
);

$ViewList['conversations'] = array(
    'params' => array(),
    'uparams' => array('ids','is_external','ipp','timefromts','opened','phone','lang_ids','is_followup','sortby','conversation_status_ids','undelivered','view','has_attachment','mailbox_ids','conversation_id','subject','department_ids','department_group_ids','user_ids','group_ids','subject_id','wait_time_from','wait_time_till','nick','email','timefrom','timeto','user_id','export','conversation_status','hum','product_id','timefrom','timefrom_minutes','timefrom_hours','timefrom_seconds','timeto', 'timeto_minutes', 'timeto_hours','timeto_seconds','department_group_id', 'group_id'),
    'functions' => array( 'use_admin' ),
    'multiple_arguments' => array(
        'department_ids',
        'department_group_ids',
        'user_ids',
        'group_ids',
        'bot_ids',
        'mailbox_ids',
        'conversation_status_ids',
        'lang_ids',
        'subject_id',
        'ids'
    )
);

$ViewList['newmailbox'] = array(
    'params' => array(),
    'functions' => array( 'mailbox_manage' )
);

$ViewList['syncmailbox'] = array(
    'params' => array('id'),
    'functions' => array( 'use_admin' )
);

$ViewList['matchingrules'] = array(
    'params' => array(),
    'uparams' => array('department_ids','department_group_ids','mailbox_ids','from_name','from_mail','subject_contains'),
    'functions' => array( 'mrules_manage' ),
    'multiple_arguments' => array(
        'department_ids',
        'department_group_ids',
        'mailbox_ids'
    )
);

$ViewList['newmatchrule'] = array(
    'params' => array(),
    'functions' => array( 'mrules_manage' )
);

$ViewList['editmailbox'] = array(
    'params' => array('id'),
    'uparams' => array('action'),
    'functions' => array( 'mailbox_manage' )
);

$ViewList['editmatchrule'] = array(
    'params' => array('id'),
    'functions' => array( 'mrules_manage' )
);

$ViewList['deletemailbox'] = array(
    'params' => array('id'),
    'uparams' => array('csfr'),
    'functions' => array( 'mailbox_manage' )
);

$ViewList['deleteconversation'] = array(
    'params' => array('id'),
    'uparams' => array('csfr'),
    'functions' => array( 'use_admin' )
);

$ViewList['deleteresponsetemplate'] = array(
    'params' => array('id'),
    'uparams' => array('csfr'),
    'functions' => array( 'rtemplates_manage' )
);

$ViewList['deletematchingrule'] = array(
    'params' => array('id'),
    'uparams' => array('csfr'),
    'functions' => array( 'mrules_manage' )
);

$ViewList['responsetemplates'] = array(
    'params' => array(),
    'uparams' => array('name','template_plain','template','dep_id','subject_id'),
    'functions' => array( 'rtemplates_see' ),
    'multiple_arguments' => array(
        'dep_id',
        'subject_id'
    )
);

$ViewList['subject'] = array(
    'params' => array('id'),
    'uparams' => array('subject','status'),
    'functions' => array( 'use' ),
);

$ViewList['addsubjectbytemplate'] = array(
    'params' => array('message_id', 'template_id'),
    'uparams' => array(),
    'functions' => array( 'use' ),
);

$ViewList['apiresponsetemplates'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'use_admin' )
);

$ViewList['newresponsetemplate'] = array(
    'params' => array(),
    'functions' => array( 'rtemplates_manage' )
);

$ViewList['editresponsetemplate'] = array(
    'params' => array('id'),
    'functions' => array( 'rtemplates_manage' )
);

$ViewList['previewresponsetemplate'] = array(
    'params' => array('id'),
    'functions' => array( 'rtemplates_see' )
);

$ViewList['notifications'] = array(
    'params' => array(),
    'functions' => array( 'use_alarms' )
);

$ViewList['options'] = array(
    'params' => array(),
    'functions' => array( 'mailbox_manage' )
);

$ViewList['optionsgeneral'] = array(
    'params' => array(),
    'functions' => array( 'mailbox_manage' )
);

$ViewList['importtemplate'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array( 'use_import' ),
);

$ViewList['relatedtickets'] = array(
    'params' => array('id'),
    'uparams' => array(),
    'functions' => array( 'closerelated' ),
);

$FunctionList = array();
$FunctionList['use_admin'] = array('explain' => 'Permission to use mail conversation module');
$FunctionList['mailbox_manage'] = array('explain' => 'Permission to manage mailbox');
$FunctionList['mrules_manage'] = array('explain' => 'Permission to manage matching rules');
$FunctionList['rtemplates_manage'] = array('explain' => 'Permission to manage response templates');
$FunctionList['rtemplates_see'] = array('explain' => 'Permission to see response templates');
$FunctionList['use_alarms'] = array('explain' => 'Permission to use alarm widget');
$FunctionList['delete_conversation'] = array('explain' => 'Permission to delete conversation');
$FunctionList['close_all_conversation'] = array('explain' => 'Permission to close conversation even if operator is not an owner of it');
$FunctionList['send_mail'] = array('explain' => 'Allow operator to send an e-mail');
$FunctionList['allow_attach_files'] = array('explain' => 'Allow operator to attach files');
$FunctionList['manage_reply_recipients'] = array('explain' => 'Allow operator to change recipient');
$FunctionList['changedepartment'] = array('explain' => 'Allow operator to change department');
$FunctionList['changeowner'] = array('explain' => 'Allow operator to change owner of the e-mail');
$FunctionList['allowtransfer'] = array('explain' =>'Allow user to transfer chat to another user/department');
$FunctionList['use_import'] = array('explain' =>'Allow user import response templates');
$FunctionList['send_as_new'] = array('explain' =>'Allow user to reply an email as SEnd as New');
$FunctionList['export_mails'] = array('explain' => 'Allow operator to export filtered mails');
$FunctionList['quick_actions'] = array('explain' => 'Allow operator to user quick actions module');
$FunctionList['change_mailbox'] = array('explain' => 'Allow operator to change mail mailbox');
$FunctionList['include_images'] = array('explain' => 'Allow operator include images from the original e-mail');
$FunctionList['closerelated'] = array('explain' => 'Allow operator to close related e-mail tickets based on e-mail');
$FunctionList['use_pmailsw'] = array('explain' => 'Allow operator to use pending mails widget.');
$FunctionList['list_all_mails'] = array('explain' => 'Allow operator to list all mails independently of operator and status.');
$FunctionList['list_my_mails'] = array('explain' => 'Allow operator to list mails he is owner');
$FunctionList['list_pending_mails'] = array('explain' => 'Allow operator to list mails without an owner and in status pending.');
$FunctionList['mail_see_unhidden_email'] = array('explain' => 'Allow operator to see full e-mail address.');
$FunctionList['phone_see_unhidden'] = array('explain' => 'Allow operator to see full phone number.');
$FunctionList['have_phone_link'] = array('explain' => 'Allow operator to click phone number as a link. Phone number will be exposed.');
$FunctionList['send_as_forward'] = array('explain' => 'Allow operator to forward mail message.');
$FunctionList['can_download'] = array('explain' => 'Allow operator to download raw mail message.');

?>