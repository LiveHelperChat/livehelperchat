<?php

include 'lib/vendor/autoload.php';

$tpl = erLhcoreClassTemplate::getInstance('lhnotifications/edit.tpl.php');

$subscriber =  erLhcoreClassModelNotificationSubscriber::fetch((int)$Params['user_parameters']['id']);

if ( isset($_POST['Cancel_edit']) ) {
    erLhcoreClassModule::redirect('genericbot/list');
    exit;
}

$input = new stdClass();
$input->chat_id = '';

if (isset($_POST['Send_action'])) {
    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        erLhcoreClassModule::redirect('notifications/list');
        exit;
    }

    $Errors = erLhcoreClassNotifications::validateTestNotification($input, $subscriber);

    if (count($Errors) == 0) {
        $tpl->set('notification_send',true);
    }  else {
        $tpl->set('errors',$Errors);
    }
}

if (isset($_POST['Update_action']) || isset($_POST['Save_action'])  )
{
    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        erLhcoreClassModule::redirect('notifications/list');
        exit;
    }

    $Errors = erLhcoreClassNotifications::validateSubscriber($subscriber);

    if (count($Errors) == 0)
    {
        $subscriber->saveThis();

        if (isset($_POST['Save_action'])) {
            erLhcoreClassModule::redirect('notifications/list');
            exit;
        } else {
            $tpl->set('updated',true);
        }

    }  else {
        $tpl->set('errors',$Errors);
    }
}

$tpl->set('item', $subscriber);
$tpl->set('input', $input);
$tpl->set('tab', '');


$Result['content'] = $tpl->fetch();
$Result['path'] = array(
    array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','System configuration')),
    array('url' => erLhcoreClassDesign::baseurl('notifications/index'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('notifications/index', 'Notifications')),
    array('url' => erLhcoreClassDesign::baseurl('notifications/list'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('notifications/index','Subscribers list')),
    array('title' => $subscriber->id));

?>