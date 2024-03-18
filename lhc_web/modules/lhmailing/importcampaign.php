<?php

$currentUser = erLhcoreClassUser::instance();
$currentUser->getUserID();


$campaign = erLhcoreClassModelMailconvMailingCampaign::fetch($Params['user_parameters']['id']);

if (!($campaign instanceof erLhcoreClassModelMailconvMailingCampaign)) {
    die('Invalid campaign!');
}

$tpl = erLhcoreClassTemplate::getInstance('lhmailing/importcampaign.tpl.php');

$itemDefault = new erLhcoreClassModelMailconvMailingRecipient();

if (isset($_POST['remove_old']) && $_POST['remove_old'] == true) {
    $tpl->set('remove_old', true);
}

if (isset($_POST['UploadFileAction'])) {

    $errors = [];

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
                if(!$header) {
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

            if (isset($_POST['remove_old']) && $_POST['remove_old'] == true) {
                $db = ezcDbInstance::get();
                $stmt = $db->prepare('DELETE FROM `lhc_mailconv_mailing_campaign_recipient` WHERE `campaign_id` = :campaign_id');
                $stmt->bindValue(':campaign_id', $campaign->id, PDO::PARAM_INT);
                $stmt->execute();
            }

            foreach ($data as $item) {

                $cannedMessage = erLhcoreClassModelMailconvMailingCampaignRecipient::findOne(array('filter' => array('campaign_id' => $campaign->id, 'email' => $item['email'])));

                if (!($cannedMessage instanceof erLhcoreClassModelMailconvMailingCampaignRecipient)) {
                    $cannedMessage = new erLhcoreClassModelMailconvMailingCampaignRecipient();
                    $stats['imported']++;
                } else {
                    $stats['updated']++;
                }

                $cannedMessage->campaign_id = $campaign->id;
                $cannedMessage->type = erLhcoreClassModelMailconvMailingCampaignRecipient::TYPE_MANUAL;
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