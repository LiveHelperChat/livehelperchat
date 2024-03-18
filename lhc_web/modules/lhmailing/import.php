<?php

$currentUser = erLhcoreClassUser::instance();
$currentUser->getUserID();

$tpl = erLhcoreClassTemplate::getInstance('lhmailing/import.tpl.php');

$itemDefault = new erLhcoreClassModelMailconvMailingRecipient();

if (is_array($Params['user_parameters_unordered']['ml'])) {
    $itemDefault->ml_ids_front = $Params['user_parameters_unordered']['ml'];
}

if (isset($_POST['remove_old']) && $_POST['remove_old'] == true) {
    $tpl->set('remove_old', true);
}

if (isset($_POST['UploadFileAction'])) {

    $itemDefault->ml_ids_front = isset($_POST['ml']) && !empty($_POST['ml']) ? $_POST['ml'] : [];

    $errors = [];

    if (empty($itemDefault->ml_ids_front)) {
        $errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('canned/import','Please choose at-least one mailing list!');
    }

    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        $errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('canned/import','Missing CSRF Token!!');
    }

    if (!erLhcoreClassSearchHandler::isFile('files',array('csv')) || !mb_check_encoding(file_get_contents($_FILES['files']["tmp_name"]), 'UTF-8')) {
        $errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('canned/import','File is not UTF-8 encoded!');
    }

    if (empty($errors)) {

        $dir = 'var/tmpfiles/';
        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('theme.temppath', array('dir' => & $dir));

        erLhcoreClassFileUpload::mkdirRecursive( $dir );

        $filename = erLhcoreClassSearchHandler::moveUploadedFile('files', $dir);

        $header = NULL;
        $data = array();

        if (($handle = fopen($dir . $filename, 'r')) !== FALSE)
        {
            while (($row = fgetcsv($handle, 10000, ",")) !== FALSE)
            {
                if (!$header) {
                    $header = $row;
                } else {
                    if (count($header) != count($row)) {
                        $row = $row + array_fill(count($row),count($header) - count($row),'');
                    }
                    $data[] = array_combine($header, $row);
                }
            }
            fclose($handle);
        }

        unlink($dir . $filename);

        $canned = ['email','mailbox','name','attr_str_1','attr_str_2','attr_str_3','attr_str_4','attr_str_5','attr_str_6'];

        $stats = array(
            'updated' => 0,
            'imported' => 0,
            'removed' => 0,
        );

        if ($canned === $header) {
            if (isset($_POST['remove_old']) && $_POST['remove_old'] == true && !empty($itemDefault->ml_ids_front)) {
                foreach (erLhcoreClassModelMailconvMailingListRecipient::getList(array('filterin' => ['mailing_list_id' => $itemDefault->ml_ids_front], 'limit' => false)) as $oldAssignment) {
                    if (is_object($oldAssignment->mailing_recipient)) {
                        $oldAssignment->mailing_recipient->removeThis();
                    }
                    $stats['removed']++;
                }
            }

            foreach ($data as $item) {

                $cannedMessage = erLhcoreClassModelMailconvMailingRecipient::findOne(array('filter' => array('email' => $item['email'])));

                if (!($cannedMessage instanceof erLhcoreClassModelMailconvMailingRecipient)) {
                    $cannedMessage = new erLhcoreClassModelMailconvMailingRecipient();
                    $stats['imported']++;
                } else {
                    $stats['updated']++;
                }

                $cannedMessage->ml_ids = array_unique(array_merge($itemDefault->ml_ids_front,$cannedMessage->ml_ids_front));
                $cannedMessage->setState($item);
                $cannedMessage->saveThis();
            }

            $tpl->set('update', $stats);
        } else {
            $tpl->set('errors', [erTranslationClassLhTranslation::getInstance()->getTranslation('canned/import','Expected columns does not match!')]);
        }

    } elseif (!empty($errors)) {
        $tpl->set('errors', $errors);
    } else {
        $tpl->set('errors', [erTranslationClassLhTranslation::getInstance()->getTranslation('canned/import','Invalid file format')]);
    }
}

$tpl->set('item', $itemDefault);

$Result['content'] = $tpl->fetch();
$Result['pagelayout'] = 'popup';

?>