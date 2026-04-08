<?php

$Module = ['name' => 'Users, groups management'];

$ViewList = [
    'login' => [
        'params' => [],
        'uparams' => ['r', 'external_request', 'noaccess'],
    ],
    'autologin' => [
        'params' => ['hash'],
        'uparams' => ['r', 'u', 'l', 't'],
    ],
    'autologinuser' => [
        'params' => ['hash'],
        'uparams' => [],
    ],
    'logout' => [
        'params' => [],
        'uparams' => ['csfr'],
    ],
    'loginas' => [
        'params' => ['id'],
        'functions' => ['loginas'],
    ],
    'loginasuser' => [
        'params' => ['id'],
        'uparams' => ['hash', 'ts', 'showlogin'],
    ],
    'account' => [
        'params' => [],
        'uparams' => ['msg', 'action', 'csfr', 'tab', 'title', 'message', 'fmsg'],
        'functions' => ['selfedit'],
    ],
    'editdepartment' => [
        'params' => ['user_id', 'dep_id'],
        'uparams' => ['csfr', 'action', 'mode', 'editor'],
        'functions' => ['selfedit'],
    ],
    'newdepartment' => [
        'params' => ['user_id'],
        'uparams' => ['csfr', 'mode', 'editor'],
        'functions' => ['selfedit'],
    ],
    'userdepartments' => [
        'params' => ['user_id'],
        'uparams' => ['editor'],
        'functions' => ['selfedit'],
    ],
    'avatarbuilder' => [
        'params' => ['id'],
        'uparams' => [],
        'functions' => ['selfedit'],
    ],
    'userlist' => [
        'params' => [],
        'uparams' => ['email', 'name', 'username', 'surname', 'group_ids', 'disabled', 'export', 'timefrom', 'timeto', 'timefrom_minutes', 'timefrom_hours', 'timeto_hours', 'timeto_minutes', 'department_ids', 'department_group_ids'],
        'functions' => ['userlist'],
        'multiple_arguments' => ['group_ids', 'department_ids', 'department_group_ids'],
    ],
    'grouplist' => [
        'params' => [],
        'functions' => ['grouplist'],
    ],
    'edit' => [
        'params' => ['user_id'],
        'uparams' => ['tab', 'category', 'timefrom', 'timefrom_hours', 'timefrom_seconds', 'timefrom_minutes', 'timeto', 'timeto_minutes', 'timeto_seconds', 'timeto_hours'],
        'functions' => ['edituser'],
        'multiple_arguments' => ['category'],
    ],
    'delete' => [
        'params' => ['user_id'],
        'uparams' => ['csfr'],
        'functions' => ['deleteuser'],
    ],
    'clone' => [
        'params' => ['user_id'],
        'uparams' => ['csfr'],
        'functions' => ['clone'],
    ],
    'new' => [
        'params' => [],
        'uparams' => ['tab'],
        'functions' => ['createuser'],
    ],
    'import' => [
        'params' => [],
        'uparams' => [],
        'functions' => ['import'],
    ],
    'newgroup' => [
        'params' => [],
        'functions' => ['creategroup', 'editgroup'],
    ],
    'editgroup' => [
        'params' => ['group_id'],
        'functions' => ['editgroup'],
    ],
    'clonegroup' => [
        'params' => ['group_id'],
        'uparams' => ['csfr'],
        'functions' => ['editgroup'],
    ],
    'groupassignuser' => [
        'params' => ['group_id'],
        'functions' => ['groupassignuser'],
    ],
    'deletegroup' => [
        'params' => ['group_id'],
        'uparams' => ['csfr'],
        'functions' => ['deletegroup'],
    ],
    'forgotpassword' => [
        'params' => [],
    ],
    'remindpassword' => [
        'params' => ['hash'],
    ],
    'setsetting' => [
        'params' => ['identifier', 'value'],
        'uparams' => ['csfr'],
    ],
    'setsettingajax' => [
        'params' => ['identifier', 'value'],
        'uparams' => ['indifferent'],
    ],
    'setsettingajaxraw' => [
        'params' => ['identifier'],
    ],
    'setoffline' => [
        'functions' => ['changeonlinestatus'],
        'params' => ['status'],
    ],
    'setalwaysonline' => [
        'functions' => ['changealwaysonline'],
        'params' => ['status'],
    ],
    'setinactive' => [
        'functions' => ['changeonlinestatus'],
        'params' => ['status'],
    ],
    'wentinactive' => [
        'functions' => ['changeonlinestatus'],
        'params' => [],
    ],
    'setinvisible' => [
        'functions' => ['changevisibility'],
        'params' => ['status'],
    ],
    'autologinconfig' => [
        'params' => [],
        'uparams' => ['csfr'],
        'functions' => ['userautologinconfig'],
    ],
    'passwordrequirements' => [
        'params' => [],
        'uparams' => ['csfr'],
        'functions' => ['pswdsecurity'],
    ],
    'updatepassword' => [
        'params' => ['user_id', 'ts', 'hash'],
        'uparams' => ['csfr'],
        'functions' => [],
    ],
    'setopstatus' => [
        'params' => ['user_id'],
        'functions' => ['setopstatus'],
    ],
];

$FunctionList = [
    'groupassignuser' => ['explain' => 'Allow user to assign user to group'],
    'editgroup' => ['explain' => 'Allow user to edit group'],
    'creategroup' => ['explain' => 'Allow user to create group'],
    'deletegroup' => ['explain' => 'Allow user to delete group'],
    'createuser' => ['explain' => 'Allow user to create another user'],
    'deleteuser' => ['explain' => 'Allow user to delete another user'],
    'edituser' => ['explain' => 'Allow user to edit another user'],
    'editusergroupall' => ['explain' => 'Allow user to edit other user groups even if they are not a member of it.'],
    'grouplist' => ['explain' => 'Allow user to list group'],
    'userlist' => ['explain' => 'Allow user to list users'],
    'selfedit' => ['explain' => 'Allow user to edit their own data'],
    'self_all_departments' => ['explain' => 'Allow user to assign themself to all departments option'],
    'edit_all_departments' => ['explain' => 'Allow user to assign other users to all departments option'],
    'assign_all_department_individual' => ['explain' => 'Allow user edit other users all individual departments'],
    'assign_all_department_group' => ['explain' => 'Allow user to assign other users to all department groups'],
    'assign_to_own_department_individual' => ['explain' => 'Allow user to change other users individual departments (only if operator belong to them)'],
    'assign_to_own_department_group' => ['explain' => 'Allow user to change other users departments groups (only if operator belong to them)'],
    'see_user_assigned_departments' => ['explain' => 'Allow user to see to other user assigned departments'],
    'see_user_assigned_departments_groups' => ['explain' => 'Allow user to see to other user assigned departments groups'],
    'see_assigned_departments' => ['explain' => 'Allow user to see departments assigned to them'],
    'see_assigned_departments_groups' => ['explain' => 'Allow user to see departments groups assigned to them'],
    'editdepartaments' => ['explain' => 'Allow user to edit their own responsible departments/departments groups'],
    'userlistonline' => ['explain' => 'Allow user to see logged operators list, only from their department'],
    'userlistonlineall' => ['explain' => 'Allow user to see logged operators list, not only from their department'],
    'changeonlinestatus' => ['explain' => 'Allow user to change their online status'],
    'changeskypenick' => ['explain' => 'Allow user to change/enter their skype nick'],
    'personalcannedmsg' => ['explain' => 'Allow user to have personal canned messages'],
    'personalautoresponder' => ['explain' => 'Allow user to have personal auto responder messages'],
    'changevisibility' => ['explain' => 'Allow user to change their visibility mode'],
    'change_visibility_list' => ['explain' => 'Allow user to choose what list should be visible to them, pending/active/unread/closed'],
    'allowtochoosependingmode' => ['explain' => 'Allow user to choose what pending chats they can see, only assigned to them or all.'],
    'receivepermissionrequest' => ['explain' => 'Allow user to choose should if they should receive other operator\'s permission requests'],
    'userautologin' => ['explain' => 'Allow user to use autologin'],
    'userautologinconfig' => ['explain' => 'Allow user to configure autologin'],
    'canseedepartmentstats' => ['explain' => 'Allow user to see departments statistic'],
    'canseealldepartmentstats' => ['explain' => 'Allow user to see all departments statistic, not only from their departments'],
    'import' => ['explain' => 'Allow user to import users'],
    'loginas' => ['explain' => 'Allow user to login as other user'],
    'passwordsecurity' => ['explain' => 'Allow user to set password security requirements'],
    'see_all' => ['explain' => 'Allow user see all users/groups not only from their group/groups'],
    'see_all_group_users' => ['explain' => 'Allow user see all group users they belongs to.'],
    'changealwaysonline' => ['explain' => 'Allow user to change always online mode'],
    'setopstatus' => ['explain' => 'Allow user to change other user online status from online operators widget'],
    'change_chat_nickname' => ['explain' => 'Allow user to change their own chat nickname'],
    'changephoto' => ['explain' => 'Allow user to change their own photo/avatar'],
    'change_job_title' => ['explain' => 'Allow user to change their own job title'],
    'change_core_attributes' => ['explain' => 'Allow user to change their own username/password/e-mail/XMPP Usernautoame'],
    'change_name_surname' => ['explain' => 'Allow user to change their own name/surname'],
    'change_password' => ['explain' => 'Allow user to change their password'],
    'pswdsecurity' => ['explain' => 'Allow user to manage password requirements'],
    'largeactivitytimeout' => ['explain' => 'Allow user to choose a large inactivity timeout'],
    'clone' => ['explain' => 'Allow user to clone other user and his settings'],
];
