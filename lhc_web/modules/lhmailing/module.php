<?php

$Module = ['name' => 'Mailing module'];

$ViewList = [
    'mailinglist' => [
        'params' => [],
        'functions' => ['use_admin'],
    ],
    'mailingrecipient' => [
        'params' => [],
        'uparams' => ['ml'],
        'functions' => ['use_admin'],
        'multiple_arguments' => ['ml'],
    ],
    'campaign' => [
        'params' => [],
        'uparams' => ['id', 'action'],
        'functions' => ['use_admin'],
    ],
    'newcampaign' => [
        'params' => [],
        'uparams' => [],
        'functions' => ['use_admin'],
    ],
    'newcampaignrecipient' => [
        'params' => ['id', 'recipient_id'],
        'uparams' => [],
        'functions' => ['use_admin'],
    ],
    'campaignrecipient' => [
        'params' => [],
        'uparams' => ['campaign', 'export', 'status', 'opened'],
        'functions' => ['use_admin'],
    ],
    'deleterecipient' => [
        'params' => ['id'],
        'uparams' => ['csfr'],
        'functions' => ['use_admin'],
    ],
    'newmailingrecipient' => [
        'params' => [],
        'uparams' => ['ml'],
        'functions' => ['use_admin'],
        'multiple_arguments' => ['ml'],
    ],
    'editmailingrecipient' => [
        'params' => ['id'],
        'functions' => ['use_admin'],
    ],
    'newmailinglist' => [
        'params' => [],
        'functions' => ['use_admin'],
    ],
    'editmailinglist' => [
        'params' => ['id'],
        'functions' => ['use_admin'],
    ],
    'editcampaign' => [
        'params' => ['id'],
        'functions' => ['use_admin'],
    ],
    'importfrommailinglist' => [
        'params' => ['id'],
        'functions' => ['use_admin'],
    ],
    'deletemailinglist' => [
        'params' => ['id'],
        'uparams' => ['csfr'],
        'functions' => ['use_admin'],
    ],
    'deletecampaign' => [
        'params' => ['id'],
        'uparams' => ['csfr'],
        'functions' => ['use_admin'],
    ],
    'deletecampaignrecipient' => [
        'params' => ['id'],
        'uparams' => ['csfr'],
        'functions' => ['use_admin'],
    ],
    'detailssend' => [
        'params' => ['id'],
        'functions' => ['use_admin'],
    ],
    'sendtestemail' => [
        'params' => ['id'],
        'uparams' => ['csfr'],
        'functions' => ['use_admin'],
    ],
    'import' => [
        'params' => [],
        'uparams' => ['ml'],
        'functions' => ['use_admin'],
        'multiple_arguments' => ['ml'],
    ],
    'importcampaign' => [
        'params' => ['id'],
        'functions' => ['use_admin'],
    ],
];

$FunctionList = [
    'use_admin' => ['explain' => 'Permission to use mailing module'],
    'all_campaigns' => ['explain' => 'Operator can see all campaigns'],
    'all_mailing_list' => ['explain' => 'Operator can see all mailing list'],
];
