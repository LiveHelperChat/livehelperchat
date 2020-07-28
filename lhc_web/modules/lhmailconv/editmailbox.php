<?php

$tpl = erLhcoreClassTemplate::getInstance('lhmailconv/editmailbox.tpl.php');

$item =  erLhcoreClassModelMailconvMailbox::fetch($Params['user_parameters']['id']);

$tab = '';

if (isset($Params['user_parameters_unordered']['action']) && $Params['user_parameters_unordered']['action'] == 'sync') {
    erLhcoreClassMailconvParser::syncMailbox($item);
    $tab = 'tab_utilities';
}

if (isset($Params['user_parameters_unordered']['action']) && $Params['user_parameters_unordered']['action'] == 'mailbox') {
    erLhcoreClassMailconvParser::getMailBox($item);
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

            if (isset($_POST['Update_page'])) {
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
    'tab' => '',
));

$Result['content'] = $tpl->fetch();

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