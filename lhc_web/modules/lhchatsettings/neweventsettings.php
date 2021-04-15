<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhchatsettings/neweventsettings.tpl.php');

$startSettings = new erLhcoreClassModelChatEventTrack();

$data = (array)$startSettings->data_array;

if (isset($_POST['UpdateConfig']) || isset($_POST['SaveConfig']))
{
    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        erLhcoreClassModule::redirect('chatsettings/eventlist');
        exit;
    }

    $Errors = erLhcoreClassAdminChatValidatorHelper::validateTrackEvent($data);

    if (!isset($_POST['DepartmentID']) || !is_numeric($_POST['DepartmentID'])) {
        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Please choose a department');
    } else {
        $startSettings->department_id = (int)$_POST['DepartmentID'];
    }

    if (!isset($_POST['name']) || empty($_POST['name'])) {
        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Please enter a name');
    } else {
        $startSettings->name = $_POST['name'];
    }

    if ( count($Errors) == 0 ) {

        $startSettings->data = serialize($data);
        $startSettings->saveThis();

        erLhcoreClassModule::redirect('chatsettings/eventlist');
        exit;

    } else {
        $tpl->set('errors',$Errors);
    }
}

$tpl->set('ga_options',$data);
$tpl->set('event_item',$startSettings);
$tpl->set('tab','');

$Result['content'] = $tpl->fetch();
$Result['path'] = array(
    array(
        'url' => erLhcoreClassDesign::baseurl('system/configuration'),
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/settings', 'System configuration')
    ),
    array(
        'url' => erLhcoreClassDesign::baseurl('chatsettings/eventindex'),
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Events tracking')
    ),
    array(
        'url' => erLhcoreClassDesign::baseurl('chatsettings/eventlist'),
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'Events tracking by department')
    ),
    array(
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/etracking', 'New event tracking')
    )
)

?>