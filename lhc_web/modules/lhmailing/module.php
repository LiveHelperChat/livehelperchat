<?php

$Module = array( "name" => "Mailing module");

$ViewList = array();

$ViewList['mailinglist'] = array(
    'params' => array(),
    'functions' => array( 'use_admin' )
);

$ViewList['mailingrecipient'] = array(
    'params' => array(),
    'uparams' => array('ml'),
    'functions' => array( 'use_admin' ),
    'multiple_arguments' => array('ml')
);

$ViewList['campaign'] = array(
    'params' => array(),
    'uparams' => array('id','action'),
    'functions' => array( 'use_admin' )
);

$ViewList['newcampaign'] = array(
    'params' => array(),
    'uparams' => array(),
    'functions' => array( 'use_admin' )
);

$ViewList['newcampaignrecipient'] = array(
    'params' => array('id','recipient_id'),
    'uparams' => array(),
    'functions' => array( 'use_admin' )
);

$ViewList['campaignrecipient'] = array(
    'params' => array(),
    'uparams' => array('campaign','export','status','opened'),
    'functions' => array( 'use_admin' )
);

$ViewList['deleterecipient'] = array(
    'params' => array('id'),
    'uparams' => array('csfr'),
    'functions' => array( 'use_admin' )
);

$ViewList['newmailingrecipient'] = array(
    'params' => array(),
    'uparams' => array('ml'),
    'functions' => array( 'use_admin' ),
    'multiple_arguments' => array('ml')
);

$ViewList['editmailingrecipient'] = array(
    'params' => array('id'),
    'functions' => array( 'use_admin' )
);

$ViewList['newmailinglist'] = array(
    'params' => array(),
    'functions' => array( 'use_admin' )
);

$ViewList['editmailinglist'] = array(
    'params' => array('id'),
    'functions' => array( 'use_admin' )
);

$ViewList['editcampaign'] = array(
    'params' => array('id'),
    'functions' => array( 'use_admin' )
);

$ViewList['importfrommailinglist'] = array(
    'params' => array('id'),
    'functions' => array( 'use_admin' )
);

$ViewList['deletemailinglist'] = array(
    'params' => array('id'),
    'uparams' => array('csfr'),
    'functions' => array( 'use_admin' )
);

$ViewList['deletecampaign'] = array(
    'params' => array('id'),
    'uparams' => array('csfr'),
    'functions' => array( 'use_admin' )
);

$ViewList['deletecampaignrecipient'] = array(
    'params' => array('id'),
    'uparams' => array('csfr'),
    'functions' => array( 'use_admin' )
);

$ViewList['detailssend'] = array(
    'params' => array('id'),
    'functions' => array( 'use_admin' )
);

$ViewList['sendtestemail'] = array(
    'params' => array('id'),
    'uparams' => array('csfr'),
    'functions' => array( 'use_admin' )
);

$ViewList['import'] = array(
    'params' => array(),
    'uparams' => array('ml'),
    'functions' => array( 'use_admin' ),
    'multiple_arguments' => array('ml')
);

$ViewList['importcampaign'] = array(
    'params' => array('id'),
    'functions' => array( 'use_admin' )
);

$FunctionList = array();
$FunctionList['use_admin'] = array('explain' => 'Permission to use mailing module');
$FunctionList['all_campaigns'] = array('explain' => 'Operator can see all campaigns');
$FunctionList['all_mailing_list'] = array('explain' => 'Operator can see all mailing list');

?>