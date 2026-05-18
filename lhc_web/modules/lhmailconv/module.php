<?php

$Module = ['name' => 'Mail conversation module'];

$ViewList = [
    'index' => [
        'params' => [],
        'functions' => ['use_admin'],
    ],
    'uploadimage' => [
        'params' => [],
        'uparams' => ['csrf'],
        'functions' => ['use_admin'],
    ],
    'tpx' => [
        'params' => ['id'],
    ],
    'sendemail' => [
        'params' => [],
        'uparams' => ['chat_id', 'layout', 'var1', 'var2', 'var3', 'var4'],
        'functions' => ['send_mail'],
    ],
    'geticketbymessageid' => [
        'params' => [],
        'functions' => ['send_mail'],
    ],
    'getsignature' => [
        'params' => [],
        'functions' => ['send_mail'],
    ],
    'uploadfile' => [
        'params' => [],
        'uparams' => ['csrf'],
        'functions' => ['use_admin'],
    ],
    'attatchfiledata' => [
        'params' => ['id'],
        'functions' => ['use_admin'],
    ],
    'mailbox' => [
        'params' => [],
        'uparams' => ['mail', 'failed', 'sync_status', 'active', 'csfr', 'resetstatus'],
        'functions' => ['mailbox_manage'],
    ],
    'personalmailboxgroups' => [
        'params' => [],
        'functions' => ['mailbox_manage'],
    ],
    'newpersonalmailboxgroup' => [
        'params' => [],
        'functions' => ['mailbox_manage'],
    ],
    'editpersonalmailboxgroup' => [
        'params' => ['id'],
        'functions' => ['mailbox_manage'],
    ],
    'inlinedownload' => [
        'params' => ['id', 'id_conv'],
        'functions' => ['use_admin'],
    ],
    'manualimport' => [
        'params' => [],
        'uparams' => ['id', 'action', 'csfr'],
        'functions' => ['use_admin'],
    ],
    'inlinedownloadmodal' => [
        'params' => ['id', 'id_conv'],
        'functions' => ['use_admin'],
    ],
    'verifyaccess' => [
        'params' => ['id', 'id_conv'],
        'functions' => ['use_admin'],
    ],
    'previewmail' => [
        'params' => ['id'],
        'functions' => ['use_admin'],
    ],
    'transfermail' => [
        'params' => ['id'],
        'functions' => ['use_admin'],
    ],
    'changemailbox' => [
        'params' => ['id'],
        'functions' => ['change_mailbox'],
    ],
    'merge' => [
        'params' => ['id'],
        'functions' => ['use_admin'],
    ],
    'blocksender' => [
        'params' => ['id'],
        'functions' => ['use_admin'],
    ],
    'apiunmerge' => [
        'params' => ['id'],
        'functions' => ['use_admin'],
    ],
    'apimaildownload' => [
        'params' => ['id', 'id_conv'],
        'functions' => ['can_download'],
    ],
    'apisendreply' => [
        'params' => ['id'],
        'functions' => ['use_admin'],
    ],
    'apifetchmails' => [
        'params' => ['id', 'ts'],
        'functions' => ['use_admin'],
    ],
    'attatchfile' => [
        'params' => [],
        'uparams' => ['persistent', 'user_id', 'visitor', 'upload_name', 'attachment'],
        'functions' => ['use_admin'],
    ],
    'insertfile' => [
        'params' => ['id'],
        'uparams' => ['mode'],
        'functions' => ['use_admin'],
    ],
    'searchtemplate' => [
        'params' => ['id'],
        'uparams' => [],
        'functions' => ['use_admin'],
    ],
    'attachtemplate' => [
        'params' => ['id'],
        'functions' => ['use_admin'],
    ],
    'getreplydata' => [
        'params' => ['id', 'mode'],
        'functions' => ['use_admin'],
    ],
    'mailprint' => [
        'params' => ['id', 'id_conv'],
        'functions' => ['use_admin'],
    ],
    'downloadrfc822' => [
        'params' => ['id', 'id_conv'],
        'functions' => ['use_admin'],
    ],
    'mailprintcovnersation' => [
        'params' => ['id'],
        'functions' => ['use_admin'],
    ],
    'view' => [
        'params' => ['id'],
        'functions' => ['use_admin'],
    ],
    'single' => [
        'params' => ['id'],
        'functions' => ['use_admin'],
    ],
    'apinoreplyrequired' => [
        'params' => ['id'],
        'functions' => ['use_admin'],
    ],
    'loadmessagebody' => [
        'params' => ['id', 'id_conv'],
        'functions' => ['use_admin'],
    ],
    'apilabelmessage' => [
        'params' => ['id'],
        'uparams' => ['subject', 'status'],
        'functions' => ['use_admin'],
    ],
    'apigetlabels' => [
        'params' => ['id'],
        'functions' => ['use_admin'],
    ],
    'apicloseconversation' => [
        'params' => ['id'],
        'functions' => ['use_admin'],
    ],
    'apichangestatus' => [
        'params' => ['id', 'status'],
        'functions' => ['use_admin'],
    ],
    'apideleteconversation' => [
        'params' => ['id'],
        'functions' => ['delete_conversation'],
    ],
    'loadmainconv' => [
        'params' => ['id'],
        'uparams' => ['mode'],
        'functions' => ['use_admin'],
    ],
    'saveremarks' => [
        'params' => ['id'],
        'uparams' => ['type'],
        'functions' => ['use_admin'],
    ],
    'mailhistory' => [
        'params' => ['id'],
        'uparams' => [],
        'functions' => ['use_admin'],
    ],
    'conversations' => [
        'params' => [],
        'uparams' => [
            'group_conv', 'timefrom_type', 'ids', 'is_external', 'ipp', 'timefromts', 'opened', 'phone', 'lang_ids',
            'is_followup', 'sortby', 'conversation_status_ids', 'undelivered', 'view', 'has_attachment', 'mailbox_ids',
            'conversation_id', 'subject', 'department_ids', 'department_group_ids', 'user_ids', 'group_ids',
            'subject_id', 'wait_time_from', 'wait_time_till', 'nick', 'email', 'timefrom', 'timeto', 'user_id',
            'export', 'conversation_status', 'hum', 'product_id', 'timefrom', 'timefrom_minutes', 'timefrom_hours',
            'timefrom_seconds', 'timeto', 'timeto_minutes', 'timeto_hours', 'timeto_seconds', 'department_group_id',
            'group_id', 'message_id',
        ],
        'functions' => ['use_admin'],
        'multiple_arguments' => [
            'department_ids', 'department_group_ids', 'user_ids', 'group_ids', 'bot_ids', 'mailbox_ids',
            'conversation_status_ids', 'lang_ids', 'subject_id', 'ids',
        ],
    ],
    'newmailbox' => [
        'params' => [],
        'functions' => ['mailbox_manage'],
    ],
    'syncmailbox' => [
        'params' => ['id'],
        'functions' => ['use_admin'],
    ],
    'matchingrules' => [
        'params' => [],
        'uparams' => ['department_ids', 'department_group_ids', 'mailbox_ids', 'from_name', 'from_mail', 'subject_contains'],
        'functions' => ['mrules_manage'],
        'multiple_arguments' => ['department_ids', 'department_group_ids', 'mailbox_ids'],
    ],
    'newmatchrule' => [
        'params' => [],
        'functions' => ['mrules_manage'],
    ],
    'editmailbox' => [
        'params' => ['id'],
        'uparams' => ['action'],
        'functions' => ['mailbox_manage'],
    ],
    'editmatchrule' => [
        'params' => ['id'],
        'functions' => ['mrules_manage'],
    ],
    'deletemailbox' => [
        'params' => ['id'],
        'uparams' => ['csfr'],
        'functions' => ['mailbox_manage'],
    ],
    'deleteconversation' => [
        'params' => ['id'],
        'uparams' => ['csfr'],
        'functions' => ['use_admin'],
    ],
    'deleteresponsetemplate' => [
        'params' => ['id'],
        'uparams' => ['csfr'],
        'functions' => ['rtemplates_manage'],
    ],
    'deletematchingrule' => [
        'params' => ['id'],
        'uparams' => ['csfr'],
        'functions' => ['mrules_manage'],
    ],
    'responsetemplates' => [
        'params' => [],
        'uparams' => ['name', 'template_plain', 'template', 'dep_id', 'subject_id'],
        'functions' => ['rtemplates_see'],
        'multiple_arguments' => ['dep_id', 'subject_id'],
    ],
    'pendingimport' => [
        'params' => [],
        'uparams' => ['mailbox_id', 'uid', 'status'],
        'functions' => ['mailbox_manage'],
    ],
    'subject' => [
        'params' => ['id'],
        'uparams' => ['subject', 'status'],
        'functions' => ['use_admin'],
    ],
    'addsubjectbytemplate' => [
        'params' => ['message_id', 'template_id'],
        'uparams' => [],
        'functions' => ['use_admin'],
    ],
    'apiresponsetemplates' => [
        'params' => ['id'],
        'uparams' => [],
        'functions' => ['use_admin'],
    ],
    'newresponsetemplate' => [
        'params' => [],
        'functions' => ['rtemplates_manage'],
    ],
    'editresponsetemplate' => [
        'params' => ['id'],
        'functions' => ['rtemplates_manage'],
    ],
    'previewresponsetemplate' => [
        'params' => ['id'],
        'functions' => ['rtemplates_see'],
    ],
    'notifications' => [
        'params' => [],
        'functions' => ['use_alarms'],
    ],
    'options' => [
        'params' => [],
        'functions' => ['mailbox_manage'],
    ],
    'optionsgeneral' => [
        'params' => [],
        'functions' => ['mailbox_manage'],
    ],
    'importtemplate' => [
        'params' => [],
        'uparams' => [],
        'functions' => ['use_import'],
    ],
    'relatedtickets' => [
        'params' => ['id'],
        'uparams' => [],
        'functions' => ['closerelated'],
    ],
];

$FunctionList = [
    'use_admin' => ['explain' => 'Permission to use mail conversation module'],
    'mailbox_manage' => ['explain' => 'Permission to manage mailbox'],
    'mrules_manage' => ['explain' => 'Permission to manage matching rules'],
    'rtemplates_manage' => ['explain' => 'Permission to manage response templates'],
    'rtemplates_see' => ['explain' => 'Permission to see response templates'],
    'use_alarms' => ['explain' => 'Permission to use alarm widget'],
    'delete_conversation' => ['explain' => 'Permission to delete conversation'],
    'close_all_conversation' => ['explain' => 'Permission to close conversation even if operator is not an owner of it'],
    'send_mail' => ['explain' => 'Allow operator to send an e-mail'],
    'allow_attach_files' => ['explain' => 'Allow operator to attach files'],
    'manage_reply_recipients' => ['explain' => 'Allow operator to change recipient'],
    'changedepartment' => ['explain' => 'Allow operator to change department'],
    'changeowner' => ['explain' => 'Allow operator to change owner of the e-mail'],
    'allowtransfer' => ['explain' => 'Allow user to transfer chat to another user/department'],
    'use_import' => ['explain' => 'Allow user import response templates'],
    'send_as_new' => ['explain' => 'Allow user to reply an email as SEnd as New'],
    'export_mails' => ['explain' => 'Allow operator to export filtered mails'],
    'quick_actions' => ['explain' => 'Allow operator to user quick actions module'],
    'change_mailbox' => ['explain' => 'Allow operator to change mail mailbox'],
    'include_images' => ['explain' => 'Allow operator include images from the original e-mail'],
    'closerelated' => ['explain' => 'Allow operator to close related e-mail tickets based on e-mail'],
    'use_pmailsw' => ['explain' => 'Allow operator to use pending mails widget.'],
    'list_all_mails' => ['explain' => 'Allow operator to list all mails independently of operator and status.'],
    'list_my_mails' => ['explain' => 'Allow operator to list mails they are owner of'],
    'list_pending_mails' => ['explain' => 'Allow operator to list mails without an owner and in status pending.'],
    'mail_see_unhidden_email' => ['explain' => 'Allow operator to see full e-mail address.'],
    'phone_see_unhidden' => ['explain' => 'Allow operator to see full phone number.'],
    'have_phone_link' => ['explain' => 'Allow operator to click phone number as a link. Phone number will be exposed.'],
    'mail_export' => ['explain' => 'Allow operator to see e-mail address in exported file'],
    'phone_export' => ['explain' => 'Allow operator to see phone in exported file'],
    'send_as_forward' => ['explain' => 'Allow operator to forward mail message.'],
    'can_download' => ['explain' => 'Allow operator to download raw mail message.'],
    'export_variables' => ['explain' => 'Allow operator export mail variable.'],
    'open_all' => ['explain' => 'Allow operator to open all pending mails, not only assigned to him'],
    'open_unassigned_mail' => ['explain' => 'Allow operator to open unassigned pending mail'],
    'download_unverified' => ['explain' => 'Allow operators to download unverified files'],
    'download_verified' => ['explain' => 'Allow operators to download verified, but sensitive files'],
    'download_restricted' => ['explain' => 'Allow operators to download restricted file types'],
    'reply_to_all' => ['explain' => 'Allow operators to set `Reply To` to any mail in new mail form. Otherwise configured mailbox will be required.'],
    'merge_cross_departments' => ['explain' => 'Allow operators to merge mails across different departments.'],
];
