<?php

$currentUser = erLhcoreClassUser::instance();
$currentUser->getUserID();

$tpl = erLhcoreClassTemplate::getInstance('lhmailconv/importtemplate.tpl.php');

if (isset($_POST['remove_old']) && $_POST['remove_old'] == true) {
    $tpl->set('remove_old', true);
}

if (isset($_POST['UploadFileAction'])) {

    if (isset($_POST['csfr_token']) && $currentUser->validateCSFRToken($_POST['csfr_token']) && erLhcoreClassSearchHandler::isFile('files',array('csv'))) {

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

        $canned = array_keys((new erLhcoreClassModelMailconvResponseTemplate())->getState());
        $canned[] = 'subject';
        $canned[] = 'department_ids_front';

        $stats = array(
            'updated' => 0,
            'imported' => 0,
            'removed' => 0,
        );

        if ($canned === $header) {

            if (isset($_POST['remove_old']) && $_POST['remove_old'] == true) {
                foreach (erLhcoreClassModelMailconvResponseTemplate::getList(array('limit' => false)) as $oldCanned) {
                    $oldCanned->removeThis();
                    $stats['removed']++;
                }
            }

            foreach ($data as $item) {

                if (!empty($item['unique_id'])) {
                    $cannedMessage = erLhcoreClassModelMailconvResponseTemplate::findOne(array('filter' => array('unique_id' => $item['unique_id'])));
                }

                if (!($cannedMessage instanceof erLhcoreClassModelMailconvResponseTemplate)) {
                    $cannedMessage = new erLhcoreClassModelMailconvResponseTemplate();
                    $stats['imported']++;
                } else {
                    $stats['updated']++;
                }

                // Set departments
                if (isset($item['department_ids_front']) && !empty($item['department_ids_front'])) {
                    $cannedMessage->department_ids = explode(',', $item['department_ids_front']);
                }

                unset($item['id']);

                $cannedMessage->setState($item);
                $cannedMessage->saveThis();

                // Delete any previous subjects if any
                $db = ezcDbInstance::get();
                $stmt = $db->prepare('DELETE FROM `lhc_mailconv_response_template_subject` WHERE `template_id` = :canned_id');
                $stmt->bindValue(':canned_id', $cannedMessage->id,PDO::PARAM_INT);
                $stmt->execute();

                if (isset($item['subject']) && !empty($item['subject'])) {

                    $subjectsPlain = explode(',',$item['subject']);
                    foreach ($subjectsPlain as $subjectIndex => $subject) {
                        $subjectsPlain[$subjectIndex] = trim($subject);
                    }

                    $subjects = erLhAbstractModelSubject::getList(array('limit' => false, 'filterin' => array('name' => $subjectsPlain)));
                    foreach ($subjects as $subject) {
                        $cannedSubject = new erLhcoreClassModelMailconvResponseTemplateSubject();
                        $cannedSubject->template_id = $cannedMessage->id;
                        $cannedSubject->subject_id = $subject->id;
                        $cannedSubject->saveThis();
                    }
                }

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