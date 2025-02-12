<?php

$tpl = erLhcoreClassTemplate::getInstance('lhmailconv/editmailbox.tpl.php');

$item =  erLhcoreClassModelMailconvMailbox::fetch($Params['user_parameters']['id']);

$tab = '';

if (isset($Params['user_parameters_unordered']['action']) && $Params['user_parameters_unordered']['action'] == 'sync') {

    $cfg = erConfigClassLhConfig::getInstance();
    $worker = $cfg->getSetting( 'webhooks', 'worker' );

    $item->last_process_time = 0;
    $item->sync_started = 0;
    $item->last_sync_time = 0;
    $item->sync_status = erLhcoreClassModelMailconvMailbox::SYNC_PENDING;
    $uuidStatusArray = $item->uuid_status_array;
    foreach ($uuidStatusArray as $key => $uuidStatus) {
        $uuidStatusArray[$key] = 0;
    }
    $item->uuid_status = json_encode($uuidStatusArray);
    $item->updateThis(array('update' => array('sync_started','last_sync_time','sync_status','last_process_time','uuid_status')));

    if ($worker == 'resque' && class_exists('erLhcoreClassExtensionLhcphpresque')) {
        $inst_id = class_exists('erLhcoreClassInstance') ? \erLhcoreClassInstance::$instanceChat->id : 0;
        erLhcoreClassModule::getExtensionInstance('erLhcoreClassExtensionLhcphpresque')->enqueue('lhc_mailconv', 'erLhcoreClassMailConvWorker', array('inst_id' => $inst_id, 'mailbox_id' => $item->id));
    } else {
        erLhcoreClassMailconvParser::syncMailbox($item, ['live' => true]);
    }

    $tab = 'tab_utilities';
}

if (isset($Params['user_parameters_unordered']['action']) && $Params['user_parameters_unordered']['action'] == 'resetsync') {
    $item->last_process_time = 0;
    $item->sync_started = 0;
    $item->last_sync_time = 0;
    $item->sync_status = erLhcoreClassModelMailconvMailbox::SYNC_PENDING;
    $uuidStatusArray = $item->uuid_status_array;
    foreach ($uuidStatusArray as $key => $uuidStatus) {
        $uuidStatusArray[$key] = 0;
    }
    $item->uuid_status = json_encode($uuidStatusArray);
    $item->updateThis(array('update' => array('sync_started','last_sync_time','sync_status','last_process_time','uuid_status')));

    $tab = 'tab_utilities';
}

if (isset($Params['user_parameters_unordered']['action']) && $Params['user_parameters_unordered']['action'] == 'mailbox') {
    try {
        erLhcoreClassMailconvParser::getMailBox($item);
    } catch (Exception $e) {
        $tpl->set('errors',[$e->getMessage()]);
    }
    $tab = 'tab_mailbox';
}

if (isset($_POST['Save_mailbox'])) {

    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        erLhcoreClassModule::redirect('mailconv/mailbox');
        exit;
    }

    $mailBoxes = $item->mailbox_sync_array;

    foreach ($mailBoxes as $index => $mailBox) {
        if (isset($_POST['Mailbox']) && is_array($_POST['Mailbox']) && in_array($mailBox['path'], $_POST['Mailbox'])) {
            $mailBoxes[$index]['sync'] = true;
        } else {
            $mailBoxes[$index]['sync'] = false;
        }

        if ($_POST['MailboxDeleted'] && $_POST['MailboxDeleted'] == $mailBox['path']) {
            $mailBoxes[$index]['sync_deleted'] = true;
        } else {
            $mailBoxes[$index]['sync_deleted'] = false;
        }

        if ($_POST['MailboxSend'] && $_POST['MailboxSend'] == $mailBox['path']) {
            $mailBoxes[$index]['send_folder'] = true;
        } else {
            $mailBoxes[$index]['send_folder'] = false;
        }
    }

    $item->mailbox_sync_array = $mailBoxes;
    $item->mailbox_sync = json_encode($item->mailbox_sync_array);
    $item->saveThis();

} else if (ezcInputForm::hasPostData()) {

    if (isset($_POST['Cancel_action'])) {
        erLhcoreClassModule::redirect('mailconv/mailbox');
        exit ;
    }

    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        erLhcoreClassModule::redirect('mailconv/mailbox');
        exit;
    }

    $Errors = erLhcoreClassMailconvValidator::validateMailbox($item);

    if (count($Errors) == 0) {
        try {
            $item->saveThis();

            if (
                isset($_POST['Update_page']) ||
                isset($_POST['UpdateSignature_page']) ||
                isset($_POST['UpdateMrules_page']) ||
                isset($_POST['UpdateOptions_page'])
            ) {

                if (isset($_POST['UpdateSignature_page'])) {
                    $tab = 'tab_signature';
                }

                if (isset($_POST['UpdateOptions_page'])) {
                    $tab = 'tab_options';
                }

                if (isset($_POST['UpdateMrules_page'])) {
                    $tab = 'tab_mrules';
                }

                $tpl->set('updated',true);
            } else {
                erLhcoreClassModule::redirect('mailconv/mailbox');
                exit;
            }

        } catch (Exception $e) {
            $tpl->set('errors',array($e->getMessage()));
        }

    } else {
        $tpl->set('errors',$Errors);
    }
}

$tpl->setArray(array(
    'item' => $item,
    'tab' => $tab,
));

$Result['content'] = $tpl->fetch();
$Result['additional_footer_js'] = '<script src="'.erLhcoreClassDesign::design('js/tinymce/js/tinymce/tinymce.min.js').'"></script>';

$Result['path'] = array(
    array(
        'url' => erLhcoreClassDesign::baseurl('system/configuration') . '#!#mailconv',
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Mail conversation')
    ),
    array(
        'url' => erLhcoreClassDesign::baseurl('mailconv/mailbox'),
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv', 'Mailbox')
    ),
    array(
        'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv', 'Edit')
    )
);

?>