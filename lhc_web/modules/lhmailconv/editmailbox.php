<?php

$tpl = erLhcoreClassTemplate::getInstance('lhmailconv/editmailbox.tpl.php');

$item =  erLhcoreClassModelMailconvMailbox::fetch($Params['user_parameters']['id']);

$tab = '';

if (isset($Params['user_parameters_unordered']['action']) && $Params['user_parameters_unordered']['action'] == 'sync') {
    erLhcoreClassMailconvParser::syncMailbox($item);
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

    $mailBoxes = $item->mailbox_sync_array;

    foreach ($mailBoxes as $index => $mailBox) {
        if (in_array($mailBox['path'], $_POST['Mailbox'])) {
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

    $Errors = erLhcoreClassMailconvValidator::validateMailbox($item);

    if (count($Errors) == 0) {
        try {
            $item->saveThis();

            if (isset($_POST['Update_page']) || isset($_POST['UpdateSignature_page'])) {

                if (isset($_POST['UpdateSignature_page'])) {
                    $tab = 'tab_signature';
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