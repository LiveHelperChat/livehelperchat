<?php

$currentUser = erLhcoreClassUser::instance();
$currentUser->getUserID();

$tpl = erLhcoreClassTemplate::getInstance('lhcannedmsg/import.tpl.php');

if (isset($_POST['remove_old']) && $_POST['remove_old'] == true) {
    $tpl->set('remove_old', true);
}

if (isset($_POST['UploadFileAction'])) {

    if (erLhcoreClassSearchHandler::isFile('files',array('csv'))) {

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
                if(!$header)
                    $header = $row;
                else
                    $data[] = array_combine($header, $row);
            }
            fclose($handle);
        }

        unlink($dir . $filename);

        $canned = array_keys((new erLhcoreClassModelCannedMsg())->getState());

        $stats = array(
            'updated' => 0,
            'imported' => 0,
            'removed' => 0,
        );

        if ($canned === $header) {

            if (isset($_POST['remove_old']) && $_POST['remove_old'] == true) {
                foreach (erLhcoreClassModelCannedMsg::getList(array('limit' => false)) as $oldCanned) {
                    $oldCanned->removeThis();
                    $stats['removed']++;
                }
            }

            foreach ($data as $item) {

                if (is_numeric($item['id'])) {
                    $cannedMessage = erLhcoreClassModelCannedMsg::fetch($item['id']);
                }

                if (!($cannedMessage instanceof erLhcoreClassModelCannedMsg)) {
                    $cannedMessage = new erLhcoreClassModelCannedMsg();
                    $stats['imported']++;
                } else {
                    $stats['updated']++;
                }

                unset($item['id']);

                $cannedMessage->setState($item);
                $cannedMessage->saveThis();
            }

            $tpl->set('update', $stats);
        } else {
            $tpl->set('errors', [erTranslationClassLhTranslation::getInstance()->getTranslation('canned/import','Expected columns does not match!')]);
        }

    } else {
        $tpl->set('errors', [erTranslationClassLhTranslation::getInstance()->getTranslation('canned/import','Invalid file format')]);
    }
}

$Result['content'] = $tpl->fetch();
$Result['pagelayout'] = 'popup';

?>